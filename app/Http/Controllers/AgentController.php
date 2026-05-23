<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('agent.dashboard.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $agents       = Agent::latest()->paginate(5);
        $roles        = UserRole::staffRoles();
        $roleLabels   = UserRole::labels();
        $departments  = Department::where('is_active', true)->get();
        $branches     = Branch::where('is_active', true)->get();

        return view('admin.organization.agent.create', compact('agents', 'roles', 'roleLabels', 'departments', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAgentRequest $request)
    {
        $validated             = $request->validated();
        $validated['password'] = bcrypt($validated['password']);

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

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($validated['password']);
        }

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
