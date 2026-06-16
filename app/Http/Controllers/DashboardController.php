<?php
namespace App\Http\Controllers;

use App\Http\Resources\PolicyResource;
use App\Models\Customer;
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
        $customerIds = $this->resolveCustomerIds($customer);

        $policies = Policy::whereIn('customer_id', $customerIds)
            ->with('customer')
            ->search($request->input('search'))
            ->ofType($request->input('type'))
            ->ofStatus($request->input('status'))
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('last_synced_at', 'desc')
            ->paginate(6)
            ->withQueryString();

        $policies->setCollection(
            $policies->getCollection()->map(fn($p) => (new PolicyResource($p))->toArray(request()))
        );

        $businessClasses = Policy::whereIn('customer_id', $customerIds)
            ->whereNotNull('business_class_name')
            ->distinct()
            ->pluck('business_class_name');

        $statusCounts = Policy::whereIn('customer_id', $customerIds)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('customer.dashboard.index', compact('customer', 'policies', 'businessClasses', 'statusCounts'));
    }

    /**
     * Resolve all local Customer IDs that belong to the same real-world person.
     *
     * The most common split case:
     *   - Customer logs in via Genova → Customer record A (source: genova)
     *   - Same person also has a GLIMS record → Customer record B (source: glims)
     *   - Both records share the same phone number
     *
     * We collect all matching IDs and use whereIn() so both sets of policies
     * show on the dashboard without needing to merge the Customer records.
     *
     * Returns at minimum [$customer->id] so the query always has something to work with.
     */
    private function resolveCustomerIds(Customer $customer): array
    {
        // Start with the authenticated customer's own ID
        $ids = [$customer->id];

        // If we have a phone number, find any other Customer records sharing it (different source, same person)
        if ($customer->phone) {
            $relatedIds = Customer::where('phone', $customer->phone)->where('id', '!=', $customer->id)->pluck('id')->toArray();

            if (! empty($relatedIds)) {
                $ids = array_merge($ids, $relatedIds);
            }
        }

        return array_unique($ids);
    }

}
