<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SyncGlimsDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600; // 1 hour max — large datasets can take time
    public int $tries   = 1;    // Don't retry on failure — sync is not idempotent-safe mid-run

    public function handle(): void
    {
        Log::info('SyncGlimsDataJob: Starting queued sync');
        Artisan::call('glims:sync');
        Log::info('SyncGlimsDataJob: Completed');
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SyncGlimsDataJob failed: ' . $e->getMessage());

        \Illuminate\Support\Facades\Cache::put('glims_sync_status', [
            'status'  => 'failed',
            'message' => 'Sync job failed: ' . $e->getMessage(),
            'at'      => now()->toDateTimeString(),
        ], now()->addHours(2));
    }
}
