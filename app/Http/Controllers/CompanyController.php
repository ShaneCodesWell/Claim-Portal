<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Agent;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = Company::firstOrFail();

        $staffMembers = User::latest()->paginate(5, ['*'], 'staff_page')
            ->appends([
                'tab'         => 'tab-team',
                'agents_page' => request('agents_page', 1),
                'branch_page' => request('branch_page', 1),
                'dept_page'   => request('dept_page', 1),
            ]);

        $agents = Agent::latest()->paginate(10, ['*'], 'agents_page')
            ->appends([
                'tab'         => 'tab-agents',
                'staff_page'  => request('staff_page', 1),
                'branch_page' => request('branch_page', 1),
                'dept_page'   => request('dept_page', 1),
            ]);

        $branches = Branch::paginate(10, ['*'], 'branch_page')
            ->appends([
                'tab'         => 'tab-branches',
                'staff_page'  => request('staff_page', 1),
                'agents_page' => request('agents_page', 1),
                'dept_page'   => request('dept_page', 1),
            ]);

        $departments = Department::where('is_active', true)->paginate(10, ['*'], 'dept_page')
            ->appends([
                'tab'         => 'tab-departments',
                'staff_page'  => request('staff_page', 1),
                'agents_page' => request('agents_page', 1),
                'branch_page' => request('branch_page', 1),
            ]);

        return view('admin.organization.index', compact('company', 'staffMembers', 'departments', 'branches', 'agents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $company = Company::firstOrFail();
        return view('admin.organization.index', ['tab' => 'profile'])->with('company', $company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request)
    {
        $validated = $request->validated();
        $company   = Company::firstOrFail();

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('company', 'public');
        }

        $company->update($validated);
        cache()->forget('company');
        return back()->with('success', 'Company profile updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        //
    }

    public function settings()
    {
        $staffMembers = User::latest()->paginate(5);
        return view('admin.settings.index', compact('staffMembers'));
    }
}
