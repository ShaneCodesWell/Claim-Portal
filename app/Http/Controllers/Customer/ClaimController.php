<?php
namespace App\Http\Controllers\Customer;

use App\Enums\ClaimSource;
use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Customer;
use App\Models\Policy;
use App\Services\ClaimService;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function __construct(protected ClaimService $claimService)
    {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'policy_id'  => 'required',
            'claim_type' => 'required|string',
            'form_data'  => 'required|array',
        ]);

        // Look up by external_policy_id since the URL uses Genova's ID
        $policy = Policy::where('external_policy_id', $validated['policy_id'])
            ->orWhere('id', $validated['policy_id'])
            ->first();

        if (! $policy) {
            return response()->json([
                'success' => false,
                'message' => 'Policy not found. Please go back and select your policy again.',
            ], 404);
        }

        $customer = Customer::where('phone', session('phone_number') ?? session('mobile_no'))
            ->orWhere('external_customer_code', session('customer_code'))
            ->first();

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please log in again.',
            ], 401);
        }

        // Verify policy belongs to this customer
        if ($policy->customer_id !== $customer->id) {
            return response()->json([
                'success' => false,
                'message' => 'This policy does not belong to your account.',
            ], 403);
        }

        $claim = $this->claimService->register(
            customer: $customer,
            policy: $policy,
            claimType: $validated['claim_type'],
            formData: $validated['form_data'],
            source: ClaimSource::CUSTOMER_PORTAL,
        );

        return response()->json([
            'success'      => true,
            'message'      => 'Your claim has been submitted successfully.',
            'claim_number' => $claim->claim_number,
            'redirect'     => route('claims.index'),
        ]);
    }

    public function index()
    {
        $customer = Customer::where('phone', session('phone_number') ?? session('mobile_no'))
            ->orWhere('external_customer_code', session('customer_code'))
            ->first();

        $claims = Claim::where('customer_id', $customer?->id)
            ->with(['policy'])
            ->latest()
            ->paginate(10);

        return view('customer.claims.index', compact('claims'));
    }

    public function show(Claim $claim)
    {
        $claim->load(['policy', 'activities.user', 'documents']);
        return view('customer.claims.show', compact('claim'));
    }
}
