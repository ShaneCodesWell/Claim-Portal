<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Http\Resources\PolicyResource;
use App\Models\Agent;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Policy;
use App\Services\GlimsApiService;
use App\Services\GenovaApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $agent = Auth::guard('agent')->user();

        if (! $agent) {
            return redirect()->route('agent.login')->with('error', 'Session expired. Please login again.');
        }

        return view('agent.dashboard.index', [
            'agent'           => $agent,
            'policies'        => collect(),
            'businessClasses' => collect(),
            'statusCounts'    => collect(),
            'searchResult'    => null,
            'searchQuery'     => null,
        ]);
    }

    public function search(Request $request, GlimsApiService $glims, GenovaApiService $genova)
    {
        $agent = Auth::guard('agent')->user();

        if (! $agent) {
            return redirect()->route('agent.login');
        }

        $policyNumber = trim($request->input('policy_number'));

        if (empty($policyNumber)) {
            return redirect()->route('agent.dashboard.index');
        }

        // Check local DB first — verifies this policy belongs to this agent
        $localPolicy = Policy::where('policy_number', $policyNumber)
            ->where('agent_id', $agent->id)
            ->first();

        if (! $localPolicy) {
            $glimsSyncPending = $agent->glims_agent_code
                && ($agent->last_synced_at === null || $agent->last_synced_at->lt(now()->subMinutes(5)));

            $genovaSyncPending = $agent->genova_agent_code
                && ($agent->genova_last_synced_at === null || $agent->genova_last_synced_at->lt(now()->subMinutes(10)));

            return view('agent.dashboard.index', [
                'agent'           => $agent,
                'policies'        => collect(),
                'businessClasses' => collect(),
                'statusCounts'    => collect(),
                'searchResult'    => null,
                'searchQuery'     => $policyNumber,
                'searchError'     => ($glimsSyncPending || $genovaSyncPending)
                    ? 'Your policy list is still syncing. Please try again in a moment.'
                    : 'No policy found with that number in your portfolio.',
            ]);
        }

        $details = match ($localPolicy->source) {
            'genova' => $this->getGenovaDetails($localPolicy, $genova),
            'glims'  => $glims->getPolicyDetails($policyNumber),
            default  => [],
        };

        return view('agent.dashboard.index', [
            'agent'           => $agent,
            'policies'        => collect(),
            'businessClasses' => collect(),
            'statusCounts'    => collect(),
            'searchResult'    => [
                'local'   => (new PolicyResource($localPolicy))->toArray(request()),
                'details' => $details,
            ],
            'searchQuery'     => $policyNumber,
            'searchError'     => null,
        ]);
    }

    private function getGenovaDetails(Policy $localPolicy, GenovaApiService $genova): array
    {
        if (empty($localPolicy->external_policy_id)) {
            return $localPolicy->raw_payload ?? [];
        }

        try {
            $response = $genova->policySearch($localPolicy->external_policy_id);

            if ($response->successful()) {
                $policies = $response->json('data.policies') ?? [];
                if (! empty($policies)) {
                    return $policies[0]; // live, richer data
                }
            }

            Log::warning('PolicyController: Genova live details empty/failed, falling back to raw_payload', [
                'policy_id' => $localPolicy->external_policy_id,
                'status'    => $response->status(),
            ]);
        } catch (\Exception $e) {
            Log::warning('PolicyController: Genova live details threw, falling back to raw_payload', [
                'policy_id' => $localPolicy->external_policy_id,
                'error'     => $e->getMessage(),
            ]);
        }

        return $localPolicy->raw_payload ?? [];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $agents      = Agent::latest()->paginate(5);
        $roles       = UserRole::staffRoles();
        $roleLabels  = UserRole::labels();
        $departments = Department::where('is_active', true)->get();
        $branches    = Branch::where('is_active', true)->get();

        return view('admin.organization.agent.create', compact('agents', 'roles', 'roleLabels', 'departments', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAgentRequest $request)
    {
        $validated = $request->validated();
        // $validated['password'] = bcrypt($validated['password']);

        Agent::create($validated);

        return redirect()->route('organization', ['tab' => 'agents'])->with('success', 'Agent added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Agent $agent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agent $agent)
    {
        $branches    = Branch::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        $roles       = UserRole::staffRoles();
        $roleLabels  = UserRole::labels();

        return view('admin.organization.agent.edit', compact('agent', 'branches', 'departments', 'roles', 'roleLabels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAgentRequest $request, Agent $agent)
    {
        $validated = $request->validated();

        // if (empty($validated['password'])) {
        //     unset($validated['password']);
        // } else {
        //     $validated['password'] = bcrypt($validated['password']);
        // }

        $agent->update($validated);

        return redirect()->route('organization', ['tab' => 'agents'])->with('success', 'Agent updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agent $agent)
    {
        if ($agent->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($agent->role === 'admin') {
            return back()->with('error', 'Admin accounts cannot be deleted.');
        }

        Agent::destroy($agent->id);

        return back()->with('success', 'Agent removed.');
    }
}
