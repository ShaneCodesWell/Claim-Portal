<?php

namespace App\Imports;

use App\Models\Agent;
use App\Models\Branch;
use App\Services\AgentSyncService;
use App\Support\PhoneNormalizer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class AgentsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading, WithBatchInserts
{
    use SkipsFailures;

    protected array $branchLookup;

    // Fields Genova is allowed to refresh on an existing agent
    protected array $updatableFields = ['phone', 'gender', 'date_of_birth', 'email'];

    public function __construct(protected AgentSyncService $agentSync)
    {
        $this->branchLookup = Branch::where('is_active', true)
            ->get(['id', 'name'])
            ->mapWithKeys(fn($b) => [Str::lower($b->name) => $b->id])
            ->toArray();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = $this->normalizeRow($row);

            $branchId = null;
            if ($data['branch']) {
                $branchId = $this->branchLookup[Str::lower($data['branch'])] ?? null;
            }

            // Match priority: glims code, then genova code, then email
            $existing = null;
            if ($data['glims_agent_code']) {
                $existing = Agent::where('glims_agent_code', $data['glims_agent_code'])->first();
            }
            if (!$existing && $data['genova_agent_code']) {
                $existing = Agent::where('genova_agent_code', $data['genova_agent_code'])->first();
            }
            if (!$existing && $data['email']) {
                $existing = Agent::where('email', $data['email'])->first();
            }

            if ($existing) {
                // Only refresh the fields we trust Genova to keep current
                $updateData = [];
                foreach ($this->updatableFields as $field) {
                    if ($data[$field] !== null && $data[$field] !== '') {
                        $updateData[$field] = $data[$field];
                    }
                }

                if (!empty($updateData)) {
                    $existing->update($updateData);
                }

                // Force a sync only if this agent was somehow never synced before
                if (is_null($existing->glims_last_synced_at) && is_null($existing->genova_last_synced_at)) {
                    $this->agentSync->dispatchPolicySync($existing->fresh());
                }

                continue;
            }

            // New agent — populate everything
            $agent = Agent::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'],
                'gender'            => $data['gender'],
                'date_of_birth'     => $data['date_of_birth'],
                'league'            => $data['league'],
                'glims_agent_code'  => $data['glims_agent_code'],
                'genova_agent_code' => $data['genova_agent_code'],
                'branch_id'         => $branchId,
                'user_category'     => $data['user_category'],
                'sub_user_category' => $data['sub_user_category'],
            ]);

            $this->agentSync->dispatchPolicySync($agent);
        }
    }

    /**
     * Trim whitespace, collapse double spaces, normalize phone/gender/casing.
     */
    protected function normalizeRow(mixed $row): array
    {
        $clean = fn($value) => $value !== null && $value !== ''
            ? preg_replace('/\s+/', ' ', trim((string) $value))
            : null;

        $phone = $clean($row['phone'] ?? null);

        return [
            'name'              => $clean($row['name'] ?? null),
            'email'             => $clean($row['email'] ?? null),
            'phone'             => $phone ? PhoneNormalizer::normalizeGhanaPhone($phone) : null,
            'gender'            => $clean($row['gender'] ?? null) ? strtolower($clean($row['gender'])) : null,
            'date_of_birth'     => $clean($row['date_of_birth'] ?? null),
            'league'            => $clean($row['league'] ?? null),
            'glims_agent_code'  => $clean($row['glims_agent_code'] ?? null),
            'genova_agent_code' => $clean($row['genova_agent_code'] ?? null),
            'branch'            => $clean($row['branch'] ?? null),
            'user_category'     => $clean($row['user_category'] ?? null),
            'sub_user_category' => $clean($row['sub_user_category'] ?? null),
        ];
    }

    public function rules(): array
    {
        return [
            '*.name'          => 'required|string|max:255',
            '*.email'         => 'nullable|email|max:255',
            '*.gender'        => 'nullable|in:male,female,other,Male,Female,Other',
            '*.date_of_birth' => 'nullable|date',
        ];
    }

    public function batchSize(): int
    {
        return 200;
    }

    public function chunkSize(): int
    {
        return 200;
    }
}
