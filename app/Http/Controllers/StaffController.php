<?php
namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Policy;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function dashboard()
    // {

    //     return view('staff.dashboard.index');
    // }

    public function allClaims()
    {
        return view('staff.all-claims.index');
    }

    // public function processClaim()
    // {
    //     return view('staff.process-claim.index');
    // }

    public function processClaimMotor()
    {
        return view('staff.process-claim.motor');
    }

    public function processClaimFire()
    {
        return view('staff.process-claim.fire');
    }

    public function processClaimGeneralAccident()
    {
        return view('staff.process-claim.general-accident');
    }

    // public function myClaims()
    // {
    //     return view('staff.my-claims.index');
    // }

    public function claimForms()
    {
        return view('staff.claim-forms.index');
    }

    public function createClaimForms()
    {
        return view('staff.claim-forms.create');
    }

    public function claimDouments()
    {
        return view('staff.claim-documents.index');
    }

    public function customers()
    {
        $customers = Customer::withCount('policies')->latest()->paginate(5);

        $stats = [
            'total_customers' => Customer::count(),
            'active_policies' => Policy::where('status', 'active')->count(),
            // 'pending_claims'  => Claim::where('status', 'pending')->count(),
        ];

        return view('staff.customers.index', compact('customers', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $staffMembers = User::latest()->paginate(5);
        $roles        = UserRole::staffRoles();
        $roleLabels   = UserRole::labels();
        $departments  = Department::where('is_active', true)->get();
        $branches     = Branch::where('is_active', true)->get();

        return view('admin.organization.staff.create', compact('staffMembers', 'roles', 'roleLabels', 'departments', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffRequest $request)
    {
        $validated             = $request->validated();
        $validated['password'] = bcrypt($validated['password']);

        // If role is admin, set is_admin to true
        $isAdmin               = $request->boolean('is_admin') || $validated['role'] === 'admin';
        $validated['is_admin'] = $isAdmin;

        User::create($validated);

        return redirect()->route('organization', ['tab' => 'team'])->with('success', 'Staff member added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $staff)
    {
        $branches    = Branch::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        $roles       = UserRole::staffRoles();
        $roleLabels  = UserRole::labels();

        return view('admin.organization.staff.edit', compact('staff', 'branches', 'departments', 'roles', 'roleLabels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStaffRequest $request, User $staff)
    {
        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($validated['password']);
        }

        $staff->update($validated);

        return redirect()->route('organization', ['tab' => 'team'])->with('success', 'Staff member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $staff)
    {
        if ($staff->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($staff->role === 'admin') {
            return back()->with('error', 'Admin accounts cannot be deleted.');
        }

        $staff->delete();

        return back()->with('success', 'Staff member removed.');
    }
}
