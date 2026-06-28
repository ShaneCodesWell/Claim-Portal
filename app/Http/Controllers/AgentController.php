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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $agent = Auth::guard('agent')->user();

    //     if (! $agent) {
    //         return redirect()->route('agent.login')->with('error', 'Session expired. Please login again.');
    //     }

    //     $policies = Policy::forAgent($agent->id)
    //         ->with('customer')
    //         ->search($request->input('search'))
    //         ->ofType($request->input('type'))
    //         ->ofStatus($request->input('status'))
    //         ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
    //         ->orderBy('last_synced_at', 'desc')
    //         ->paginate(6)
    //         ->withQueryString();

    //     $policies->setCollection(
    //         $policies->getCollection()->map(fn($p) => (new PolicyResource($p))->toArray(request()))
    //     );

    //     $businessClasses = Policy::forAgent($agent->id)
    //         ->whereNotNull('business_class_name')
    //         ->distinct()
    //         ->pluck('business_class_name');

    //     $statusCounts = Policy::forAgent($agent->id)
    //         ->selectRaw('status, count(*) as total')
    //         ->groupBy('status')
    //         ->pluck('total', 'status');

    //     return view('agent.dashboard.index', compact('agent', 'policies', 'businessClasses', 'statusCounts'));
    // }

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

    public function search(Request $request, GlimsApiService $glims)
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
            return view('agent.dashboard.index', [
                'agent'           => $agent,
                'policies'        => collect(),
                'businessClasses' => collect(),
                'statusCounts'    => collect(),
                'searchResult'    => null,
                'searchQuery'     => $policyNumber,
                'searchError'     => 'No policy found with that number in your portfolio.',
            ]);
        }

        // Fetch rich details from GLIMS for display
        $details = $glims->getPolicyDetails($policyNumber);

        return view('agent.dashboard.index', [
            'agent'           => $agent,
            'policies'        => collect(),
            'businessClasses' => collect(),
            'statusCounts'    => collect(),
            'searchResult'    => [
                'local'   => (new PolicyResource($localPolicy))->toArray(request()),
                'details' => $details, // rich vehicle/risk data
            ],
            'searchQuery'     => $policyNumber,
            'searchError'     => null,
        ]);
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
