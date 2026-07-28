<?php

namespace App\Jobs;

use App\Models\Agent;
use App\Models\Customer;
use App\Models\Policy;
use App\Services\GlimsApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAgentPoliciesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public Agent $agent) {}

    public function handle(GlimsApiService $glims): void
    {
        $glimsAgentCode = $this->agent->glims_agent_code;

        if (! $glimsAgentCode) {
            Log::info('SyncAgentPoliciesJob: agent has no glims_agent_code, skipping', [
                'agent_id' => $this->agent->id,
            ]);
            return;
        }

        Log::info('SyncAgentPoliciesJob: starting sync', [
            'agent_id'   => $this->agent->id,
            'agent_code' => $glimsAgentCode,
        ]);

        $page   = 1;
        $synced = 0;
        $errors = 0;

        do {
            try {
                $response = $glims->getAgentPolicies($glimsAgentCode, $page);
            } catch (\Exception $e) {
                Log::error('SyncAgentPoliciesJob: API call failed', [
                    'agent_id' => $this->agent->id,
                    'page'     => $page,
                    'error'    => $e->getMessage(),
                ]);
                break;
            }

            if ($response->failed()) {
                Log::error('SyncAgentPoliciesJob: non-200 response', [
                    'agent_id' => $this->agent->id,
                    'page'     => $page,
                    'status'   => $response->status(),
                ]);
                break;
            }

            $body    = $response->json();
            $results = $body['results'] ?? [];
            $count   = $body['count'] ?? 0;

            if (empty($results)) {
                break;
            }

            foreach ($results as $item) {
                try {
                    $this->upsertPolicy($item);
                    $synced++;
                } catch (\Exception $e) {
                    $errors++;
                    Log::warning('SyncAgentPoliciesJob: upsert failed for one policy', [
                        'policy_number' => $item['policy_number'] ?? 'unknown',
                        'error'         => $e->getMessage(),
                    ]);
                }
            }

            // GLIMS returns all results paginated; stop when we've consumed everything
            $fetched = $page * count($results);
            $page++;
        } while ($fetched < $count);

        $this->agent->update(['glims_last_synced_at' => now()]);

        Log::info('SyncAgentPoliciesJob: completed', [
            'agent_id' => $this->agent->id,
            'synced'   => $synced,
            'errors'   => $errors,
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function upsertPolicy(array $item): void
    {
        $policyNumber = $item['policy_number'] ?? null;

        if (! $policyNumber) {
            return;
        }

        // Resolve or lazily create the customer this policy belongs to.
        // Agents' policies always have a customer_code in the GLIMS response.
        $customerCode = (string) ($item['customer_code'] ?? '');
        $customer     = null;

        if ($customerCode) {
            $customer = Customer::firstOrCreate(
                ['external_customer_code' => $customerCode],
                [
                    'name'    => trim(
                        ($item['first_name'] ?? '') . ' ' .
                            ($item['other_names'] ?? '') . ' ' .
                            ($item['family_name'] ?? '')
                    ),
                    'phone'   => null,
                    'email'   => null,
                    'sources' => ['glims'],
                ]
            );
        }

        $startDate  = $item['start_date'] ?? null;
        $expiryDate = $item['expiry_date'] ?? null;

        Policy::updateOrCreate(
            [
                'policy_number' => $policyNumber,
                'source'        => 'glims',
            ],
            [
                'customer_id'         => $customer?->id,
                'agent_id'            => $this->agent->id,
                'external_policy_id'  => (string) ($item['policy_id'] ?? ''),
                'product_name'        => $item['product'] ?? null,
                'business_class_name' => $item['lob'] ?? null,
                'start_date'          => $startDate,
                'end_date'            => $expiryDate,
                'effective_date'      => $item['issue_date'] ?? $startDate,
                'renewal_date'        => $expiryDate,
                'raw_payload'         => $item,
                'last_synced_at'      => now(),
            ]
        );
    }
}
