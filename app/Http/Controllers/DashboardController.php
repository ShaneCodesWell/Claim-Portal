<?php
namespace App\Http\Controllers;

use App\Http\Resources\PolicyResource;
use App\Models\Policy;
use App\Services\GenovaApiService;
use App\Services\GlimsService;
use App\Services\GlimsSyncService;
use App\Services\PolicySyncService;
use Illuminate\Http\Request;
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

        $policies = Policy::whereIn('customer_id', $customerIds)
            ->where('status', '!=', 'expired')
            ->with('customer')
            ->search($request->input('search'))
            ->ofType($request->input('type'))
            ->orderBy('last_synced_at', 'desc')
            ->paginate(6)
            ->withQueryString();

        $policies->setCollection(
            $policies->getCollection()->map(fn($p) => (new PolicyResource($p))->toArray(request()))
        );

        $businessClasses = Policy::whereIn('customer_id', $customerIds)
            ->where('status', 'active')
            ->whereNotNull('business_class_name')
            ->distinct()
            ->pluck('business_class_name');

        // Policies that are not expired are active right
        $statusCounts = Policy::whereIn('customer_id', $customerIds)
            ->selectRaw("
                CASE
                    WHEN status = 'expired' THEN 'expired'
                    ELSE 'active'
                END as status_group,
                COUNT(*) as total
            ")
            ->groupBy('status_group')
            ->pluck('total', 'status_group');

        return view('customer.dashboard.index', compact('customer', 'policies', 'businessClasses', 'statusCounts'));
    }

    public function pollPolicies(Request $request): \Illuminate\Http\JsonResponse
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return response()->json(['ready' => false]);
        }

        $customerIds = $customer->resolvedCustomerIds();

        $count = Policy::whereIn('customer_id', $customerIds)
            ->where('status', 'active')
            ->count();

        $syncDone = ! is_null($customer->fresh()->last_synced_at); // fresh() to avoid stale cache

        return response()->json([
            'ready'   => $syncDone, // done = sync completed, not "has policies"
            'count'   => $count,
            'syncing' => ! $syncDone,
        ]);
    }

}
