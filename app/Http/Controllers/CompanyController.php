<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
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
        $branches     = Branch::all();
        $company      = Company::firstOrFail();
        $staffMembers = User::latest()->paginate(5);
        $departments  = Department::all();
        // $staffMembers = User::with(['branch', 'department'])->whereIn('role', UserRole::staffRoles())->latest()->paginate(10);
        return view('admin.organization.index', compact('company', 'staffMembers', 'departments', 'branches'));
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
        return view('admin.organization.index', compact('company'));
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
}
