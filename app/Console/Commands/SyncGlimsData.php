<?php

namespace App\Console\Commands;

use App\Services\GlimsService;
use App\Services\GlimsSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncGlimsData extends Command
{
    protected $signature   = 'glims:sync {--client_code= : Sync a single client code only}';
    protected $description = 'Sync policies and customers from GLIMS (Oracle/VACLIVE) into the Claims Portal DB';

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
            $this->error('Cannot connect to GLIMS Oracle database. Check credentials and host.');
            return Command::FAILURE;
        }

        $this->info('Connected to VACLIVE. Starting sync...');

        // ── Single customer mode (for testing or on-demand) ──
        if ($clientCode = $this->option('client_code')) {
            return $this->syncSingleClient($clientCode);
        }

        // ── Full sync mode ────────────────────────────────────
        // NOTE: You probably don't want to pull ALL customers at once
        // in production. Scope this to active policies only.
        $this->warn('Full sync not implemented yet — use --client_code for now.');
        $this->info('Example: php artisan glims:sync --client_code=C00123');

        return Command::SUCCESS;
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
}