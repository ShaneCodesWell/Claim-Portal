<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Jobs\SyncGlimsDataJob;
use App\Services\GlimsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class GlimsSyncController extends Controller
{
    public function __construct(private GlimsService $glims) {}

    public function trigger(): JsonResponse
    {
        // Check we can actually reach GLIMS before queuing
        if (! $this->glims->isConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot reach GLIMS database. You must be on-premise to run a sync.',
            ], 503);
        }

        // Prevent double-triggering
        $current = Cache::get('glims_sync_status');
        if (($current['status'] ?? '') === 'running') {
            return response()->json([
                'success' => false,
                'message' => 'A sync is already in progress.',
                'status'  => $current,
            ], 409);
        }

        SyncGlimsDataJob::dispatch()->onQueue('default');

        return response()->json([
            'success' => true,
            'message' => 'GLIMS sync started. This may take a few minutes.',
        ]);
    }

    public function status(): JsonResponse
    {
        $status = Cache::get('glims_sync_status', [
            'status'  => 'idle',
            'message' => 'No sync has been run yet.',
        ]);

        // Also include live connection status
        $status['connected'] = $this->glims->isConnected();

        return response()->json($status);
    }
}