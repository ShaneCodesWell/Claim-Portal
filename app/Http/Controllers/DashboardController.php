<?php

namespace App\Http\Controllers;

use App\Http\Resources\PolicyResource;
use App\Models\Policy;
use App\Services\GenovaApiService;
use App\Services\GlimsService;
use App\Services\GlimsSyncService;
use App\Services\PolicySyncService;
use App\Jobs\SyncCustomerPoliciesJob;
use Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected GenovaApiService $api;
    protected PolicySyncService $policySync;
    protected GlimsService $glimsService;
    protected GlimsSyncService $glimsSyncService;

    public function __construct(
        GenovaApiService $api,
        PolicySyncService $policySync,
        GlimsService $glimsService,
        GlimsSyncService $glimsSyncService
    ) {
        $this->api              = $api;
        $this->policySync       = $policySync;
        $this->glimsService     = $glimsService;
        $this->glimsSyncService = $glimsSyncService;
    }

    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        // Resolve all customer IDs that belong to this person
        // (handles the case where the same person has separate Customer records
        //  from Genova and GLIMS that haven't been merged yet)
        $customerIds = $customer->resolvedCustomerIds();

        // Silent background refresh if data is older than 6 hours.
        // The job skips itself if synced within 30 minutes, so this is safe.
        if ($customer->last_synced_at && $customer->last_synced_at->lt(now()->subHours(6))) {
            SyncCustomerPoliciesJob::dispatch($customer)->onQueue('high');
        }

        $policies = Policy::whereIn('customer_id', $customerIds)
            ->visible()
            ->with('customer')
            ->search($request->input('search'))
            ->ofType($request->input('type'))
            ->orderByRaw('end_date DESC NULLS LAST')   // Postgres: NULLs sort first on DESC otherwise
            ->orderByDesc('id')                        // tie-breaker so pagination is stable
            ->paginate(6)
            ->withQueryString();

        $policies->setCollection(
            $policies->getCollection()->map(fn($p) => (new PolicyResource($p))->toArray(request()))
        );

        $businessClasses = Policy::whereIn('customer_id', $customerIds)
            ->visible()
            ->whereNotNull('business_class_name')
            ->distinct()
            ->pluck('business_class_name');

        $statusCounts = collect([
            'active' => Policy::whereIn('customer_id', $customerIds)->visible()->count(),
        ]);

        return view('customer.dashboard.index', compact('customer', 'policies', 'businessClasses', 'statusCounts'));
    }

    public function pollPolicies(Request $request): JsonResponse
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return response()->json(['ready' => false]);
        }

        $customerIds = $customer->resolvedCustomerIds();

        $count = Policy::whereIn('customer_id', $customerIds)
            ->visible()
            ->count();

        $syncDone = ! is_null($customer->fresh()->last_synced_at); // fresh() to avoid stale cache

        return response()->json([
            'ready'   => $syncDone, // done = sync completed, not "has policies"
            'count'   => $count,
            'syncing' => ! $syncDone,
        ]);
    }
}
