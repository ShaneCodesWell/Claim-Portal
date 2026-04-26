<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Models\User;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::withCount('users')->latest()->paginate(10);
        return view('admin.organization.index', ['tab' => 'branches'])->with('branches', $branches);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $staffMembers = User::all();
        return view('admin.organization.branches.create', compact('staffMembers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBranchRequest $request)
    {
        $validated = $request->validated();
        $company = Company::firstOrFail();

        Branch::create([
             ...$validated,
            'company_id' => $company->id,
        ]);

        // return back()->with('success', 'Branch created successfully.');
        return redirect()->route('organization', ['tab' => 'branches'])->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        $staffMembers = User::all();
        return view('admin.organization.branches.edit', compact('branch', 'staffMembers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBranchRequest $request, Branch $branch)
    {

        $validated = $request->validated();
        $branch->update($validated);

        // return back()->with('success', 'Branch updated successfully.');
        return redirect()->route('organization', ['tab' => 'branches'])->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        if ($branch->users()->count() > 0) {
            return back()->with('error', 'Cannot delete a branch that has staff members assigned to it.');
        }

        $branch->delete();
        return redirect()->route('organization', ['tab' => 'branches'])->with('success', 'Branch deleted successfully.');
    }
}
