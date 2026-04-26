<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Branch;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::with('branch')->with('head')->withCount('users')->latest()->paginate(10);
        $branches    = Branch::where('is_active', true)->get();

        return view('admin.organization.index', compact('departments', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $staffMembers = User::all();
        $branches    = Branch::where('is_active', true)->get();
        return view('admin.organization.departments.create', compact('staffMembers', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        $validated = $request->validated();

        Department::create([
             ...$validated,
            'company_id' => Company::first()->id,
        ]);

        return redirect()->route('organization', ['tab' => 'departments'])->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $staffMembers = User::all();
        $branches    = Branch::where('is_active', true)->get();
        return view('admin.organization.departments.edit', compact('department', 'staffMembers', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $department->update($request->validated());
        return redirect()->route('organization', ['tab' => 'departments'])->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        if ($department->users()->count() > 0) {
            return redirect()->route('organization', ['tab' => 'departments'])->with('error', 'Cannot delete a department that has staff members assigned to it.');
        }

        $department->delete();

        return redirect()->route('organization', ['tab' => 'departments'])->with('success', 'Department deleted.');
    }
}
