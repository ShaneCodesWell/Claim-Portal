<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Resources\PolicyResource;
use App\Models\Customer;
use App\Models\Policy;
use App\Services\AgentPolicySearchService;
use App\Services\GenovaApiService;
use App\Services\GlimsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    /**
     * Lazy-load full risk/vehicle detail for one policy, for the customer
     * search results modal. Local raw_payload is used if already populated
     * (instant, no API call); otherwise falls back to a live fetch from the
     * policy's source system and caches the result back onto the policy so
     * subsequent opens are instant too.
     */
    public function risks(Policy $policy, GlimsApiService $glims, GenovaApiService $genova)
    {
        $agent = Auth::guard('agent')->user();
 
        if (! $agent || $policy->agent_id !== $agent->id) {
            abort(403);
        }
 
        $risks = (new PolicyResource($policy))->toArray(request())['risks'] ?? [];
 
        if (empty($risks)) {
            $risks = $this->enrichRisks($policy, $glims, $genova);
        }
 
        return response()->json([
            'success'        => true,
            'policy_id'      => $policy->id,
            'status'         => $policy->status,
            'is_fleet'       => count($risks) > 1,
            'claim_form_url' => route('agent.claims.create', ['policy_id' => $policy->id]),
            'risks'          => $risks,
        ]);
    }
 
    private function enrichRisks(Policy $policy, GlimsApiService $glims, GenovaApiService $genova): array
    {
        try {
            if ($policy->source === 'glims') {
                $risks = $glims->getRisksForPolicy($policy->policy_number);
            } elseif ($policy->source === 'genova' && $policy->external_policy_id) {
                $response = $genova->policySearch($policy->external_policy_id);
                $risks = $response->successful()
                    ? ($response->json('data.policies.0.risks') ?? [])
                    : [];
            } else {
                $risks = [];
            }
        } catch (\Exception $e) {
            Log::warning('SearchController: risk enrichment failed', [
                'policy_id' => $policy->id,
                'source'    => $policy->source,
                'error'     => $e->getMessage(),
            ]);
            return [];
        }
 
        if (! empty($risks)) {
            $raw           = $policy->raw_payload ?? [];
            $raw['risks']  = $risks;
            $policy->update(['raw_payload' => $raw]);
            $policy->refresh();
 
            return (new PolicyResource($policy))->toArray(request())['risks'] ?? [];
        }
 
        return [];
    }
 
    public function search(Request $request, AgentPolicySearchService $policySearch)
    {
        $request->validate([
            'type'  => 'required|in:policy,name,phone,vehicle',
            'query' => 'required|string|min:2|max:100',
        ]);
 
        $agent = Auth::guard('agent')->user();
        $type  = $request->input('type');
        $query = trim($request->input('query'));
 
        if ($type === 'policy') {
            return $this->searchByPolicyNumber($agent, $query, $policySearch);
        }
 
        return $this->searchCustomers($agent->id, $type, $query);
    }
 
    private function searchByPolicyNumber($agent, string $policyNumber, AgentPolicySearchService $policySearch)
    {
        $result = $policySearch->findForAgent($agent, $policyNumber);
 
        if (! $result) {
            return response()->json([
                'success' => false,
                'message' => 'No policy found with that number in your portfolio.',
            ], 404);
        }
 
        return response()->json([
            'success' => true,
            'source'  => $result['source'], // 'local' or 'api' — lets the UI show the "retrieved live" note
            'policy'  => $result['policy'],
            'details' => $result['details'],
        ]);
    }
 
    /**
     * Name / phone / vehicle — all agent-scoped, DB-only, returns a list
     * of matched customers (each with only this agent's policies attached).
     */
    private function searchCustomers(int $agentId, string $type, string $value)
    {
        $customers = $type === 'vehicle'
            ? $this->byVehicleNumber($agentId, $value)
            : $this->byField($agentId, $type, $value);
 
        if ($customers->isEmpty()) {
            return response()->json([
                'success'   => true,
                'customers' => [],
                'message'   => "No matching customer found. If you're expecting to see this customer, they may not have synced to your portfolio yet — try again shortly, or search by policy number if you have it.",
            ]);
        }
 
        return response()->json([
            'success'   => true,
            'customers' => $customers->map(fn (Customer $c) => $this->formatCustomer($c))->values(),
        ]);
    }
 
    private function byField(int $agentId, string $field, string $value)
    {
        $column = $field === 'name' ? 'name' : 'phone';
 
        return Customer::whereHas('policies', fn ($q) => $q->forAgent($agentId))
            ->where($column, 'like', "%{$value}%")
            ->with(['policies' => fn ($q) => $q->forAgent($agentId)])
            ->limit(20)
            ->get();
    }
 
    private function byVehicleNumber(int $agentId, string $vehicleNumber)
    {
        $policies = Policy::forAgent($agentId)
            ->vehicleNumber($vehicleNumber)
            ->with('customer')
            ->get()
            ->filter(fn (Policy $p) => $p->customer !== null);
 
        return $policies
            ->groupBy('customer_id')
            ->map(fn ($group) => tap($group->first()->customer, fn ($c) => $c->setRelation('policies', $group)))
            ->values();
    }
 
    private function formatCustomer(Customer $customer): array
    {
        return [
            'id'    => $customer->id,
            'name'  => $customer->name,
            'code'  => $customer->glims_customer_code ?? $customer->genova_customer_code,
            'phone' => $customer->phone,
            'policies' => $customer->policies->map(fn (Policy $p) => [
                'id'      => $p->id,
                'number'  => $p->policy_number,
                'product' => $p->product_name,
                'status'  => ucfirst($p->status),
            ])->values(),
        ];
    }
}
