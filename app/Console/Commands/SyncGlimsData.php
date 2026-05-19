<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Policy;
use App\Services\GlimsService;
use App\Services\GlimsSyncService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncGlimsData extends Command
{
    protected $signature   = 'glims:sync {--client_code= : Sync a single client code only} {--fresh : Clear existing GLIMS data before syncing}';
    protected $description = 'Sync active policies and customers from GLIMS (Oracle/VACLIVE) into the Claims Portal DB';

    public function __construct(
        private GlimsService $glims,
        private GlimsSyncService $sync
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Checking GLIMS connection...');

        if (! $this->glims->isConnected()) {
            $this->error('Cannot connect to GLIMS Oracle database. Must be on-premise.');
            Cache::put('glims_sync_status', [
                'status'  => 'failed',
                'message' => 'Cannot connect to GLIMS. Must be on-premise.',
                'at'      => now()->toDateTimeString(),
            ], now()->addHours(2));
            return Command::FAILURE;
        }

        $this->info('Connected to VACLIVE.');

        // ── Single customer mode ───────────────────────────────
        if ($clientCode = $this->option('client_code')) {
            return $this->syncSingleClient($clientCode);
        }

        // ── Full sync mode ─────────────────────────────────────
        return $this->syncAll();
    }

    private function syncAll(): int
    {
        $this->info('Starting full active-policy sync...');

        // Mark sync as running so the UI can show progress
        Cache::put('glims_sync_status', [
            'status'    => 'running',
            'message'   => 'Sync in progress...',
            'started_at' => now()->toDateTimeString(),
            'synced'    => 0,
            'failed'    => 0,
        ], now()->addHours(2));

        $synced  = 0;
        $failed  = 0;
        $skipped = 0;

        $bar = null; // progress bar — only works in terminal, not queue

        $this->glims->getAllActivePolicies(function ($rows) use (&$synced, &$failed, &$skipped, &$bar) {

            if (! $bar) {
                // We don't know total upfront with chunk — just show activity
                $this->line('Processing chunk of ' . count($rows) . ' policies...');
            }

            // Group by client code so we upsert each customer once per chunk
            $byClient = collect($rows)->groupBy('policy_owner');

            foreach ($byClient as $clientCode => $clientPolicies) {
                try {
                    DB::transaction(function () use ($clientCode, $clientPolicies, &$synced, &$skipped) {
                        $first = $clientPolicies->first();

                        // ── Upsert customer ────────────────────
                        $name = trim(implode(' ', array_filter([
                            $first->client_first_name ?? null,
                            $first->client_middle_name ?? null,
                            $first->client_family_name ?? null,
                        ])));

                        $phone = $first->client_home_mobile ?? $first->client_home_tel ?? null;
                        $email = $first->client_home_email ?? null;

                        $existingCustomer = Customer::where('external_customer_code', $clientCode)
                            ->orWhere(function ($q) use ($phone) {
                                if ($phone) $q->where('phone', $phone);
                            })->first();

                        $sources = $existingCustomer ? ($existingCustomer->sources ?? []) : [];
                        if (! in_array('glims', $sources)) {
                            $sources[] = 'glims';
                        }

                        $dbCustomer = Customer::updateOrCreate(
                            ['external_customer_code' => $clientCode],
                            [
                                'sources'        => $sources,
                                'name'           => $existingCustomer->name ?? $name,
                                'phone'          => $existingCustomer->phone ?? $phone,
                                'email'          => $existingCustomer->email ?? $email,
                                'last_synced_at' => now(),
                            ]
                        );

                        // ── Upsert each policy ─────────────────
                        foreach ($clientPolicies as $row) {
                            $policyNumber = $row->policy_formatted_number ?? $row->policy_sequence;

                            // Motor risks
                            $motorRisks    = $this->glims->getMotorRisks($row->policy_sequence);
                            $vehicleNumber = ! empty($motorRisks)
                                ? ((array) $motorRisks[0])['objecth_02_plate_number'] ?? null
                                : null;

                            $rawPayload = [
                                'POLICY_SEQUENCE'          => $row->policy_sequence,
                                'POLICY_NUMBER'            => $policyNumber,
                                'POLICY_OWNER'             => $row->policy_owner,
                                'POLICY_STATUS'            => $row->policy_status,
                                'POLICY_STATUS_REASON'     => $row->policy_status_reason,
                                'POLICY_CURRENCY'          => $row->policy_currency,
                                'POLICY_TOTAL_PREMIUM'     => $row->policy_total_premium,
                                'POLICY_TOTAL_SI'          => $row->policy_total_si,
                                'POLICY_COMMENCEMENT_DATE' => $this->julianToDate($row->policy_commencement_date),
                                'POLICY_EXPIRY_DATE'       => $this->julianToDate($row->policy_expiry_date),
                                'POLICY_EFFECTIVE_DATE'    => $this->julianToDate($row->policy_effective_date),
                                'POLICY_PRODUCT_ID'        => $row->policy_product_id,
                                'POLICY_LOB_ID'            => $row->policy_lob_id,
                                'POLICY_MAIN_CLASS_ID'     => $row->policy_main_class_id,
                                'POLICY_BRANCH_ID'         => $row->policy_branch_id,
                                'POLICY_AGENT_ID'          => $row->policy_agent_id,
                                'POLICY_PRODUCT_NAME'      => $row->policy_product_name ?? 'Unknown Product',
                                'POLICY_LOB_NAME'          => $row->policy_lob_name ?? 'Unknown LOB',
                                'POLICY_MAIN_CLASS_NAME'   => $row->policy_main_class_name ?? 'Unknown Class',
                                'POLICY_BRANCH_NAME'       => $row->policy_branch_name ?? 'Unknown Branch',
                                'POLICY_AGENT_NAME'        => $row->policy_agent_name ?? 'Unknown Agent',
                                'vehicle_number'           => $vehicleNumber,
                                'motor_risks'              => $motorRisks,
                                'status_label'             => 'active',
                            ];

                            Policy::updateOrCreate(
                                ['external_policy_id' => (string) $row->policy_sequence],
                                [
                                    'customer_id'         => $dbCustomer->id,
                                    'source'              => 'glims',
                                    'policy_number'       => $policyNumber,
                                    'insured_name'        => $dbCustomer->name,
                                    'business_class_id'   => $row->policy_lob_id ?? null,
                                    'product_id'          => $row->policy_product_id ?? null,
                                    'business_class_name' => $row->policy_main_class_name ?? 'Unknown Class',
                                    'product_name'        => $row->policy_product_name ?? 'Unknown Product',
                                    'start_date'          => $this->julianToDate($row->policy_commencement_date),
                                    'end_date'            => $this->julianToDate($row->policy_expiry_date),
                                    'effective_date'      => $this->julianToDate($row->policy_effective_date),
                                    'status'              => 'active',
                                    'raw_payload'         => $rawPayload,
                                    'last_synced_at'      => now(),
                                ]
                            );

                            $synced++;
                        }
                    });

                } catch (\Exception $e) {
                    $failed++;
                    Log::error('GLIMS full sync error for client: ' . $clientCode, [
                        'error' => $e->getMessage(),
                    ]);
                }

                // Update cache progress every client
                Cache::put('glims_sync_status', [
                    'status'    => 'running',
                    'message'   => "Syncing... {$synced} policies processed",
                    'started_at' => Cache::get('glims_sync_status')['started_at'] ?? now()->toDateTimeString(),
                    'synced'    => $synced,
                    'failed'    => $failed,
                ], now()->addHours(2));
            }
        });

        // ── Final status ───────────────────────────────────────
        $summary = [
            'status'      => 'completed',
            'message'     => "Sync complete. {$synced} policies synced, {$failed} failed.",
            'synced'      => $synced,
            'failed'      => $failed,
            'last_run_at' => now()->toDateTimeString(),
        ];

        Cache::put('glims_sync_status', $summary, now()->addHours(24));

        $this->info("✓ Sync complete. Policies synced: {$synced}, Failed: {$failed}");

        Log::info('GLIMS full sync completed', $summary);

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function syncSingleClient(string $clientCode): int
    {
        $this->info("Syncing client: {$clientCode}");

        $customer = $this->glims->customerVerification($clientCode, 'client_code');

        if (! $customer) {
            $this->error("No customer found in GLIMS for client code: {$clientCode}");
            return Command::FAILURE;
        }

        $synced = $this->sync->syncCustomer($customer);

        $this->info('Sync complete. Policies synced: ' . count($synced));
        foreach ($synced as $policyNumber => $data) {
            $this->line("  ✓ {$policyNumber} [{$data['status']}]");
        }

        return Command::SUCCESS;
    }

    private function julianToDate($julianDay): ?string
    {
        if (! $julianDay) return null;
        try {
            $julian = (int) $julianDay;
            [$month, $day, $year] = explode('/', jdtogregorian($julian));
            return Carbon::createFromDate($year, $month, $day)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}