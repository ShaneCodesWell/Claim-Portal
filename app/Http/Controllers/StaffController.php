<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {

        return view('staff.dashboard.index');
    }

    public function allClaims()
    {
        return view('staff.all-claims.index');
    }

    public function myClaims()
    {
        return view('staff.my-claims.index');
    }

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
        $customers = Customer::latest()->paginate(10);
        return view('staff.customers.index', compact('customers'));
    }

    public function settings()
    {
        $staffMembers = User::latest()->paginate(10);
        return view('staff.settings.index', compact('staffMembers'));
    }

    public function addStaff()
    {
        $staffMembers = User::latest()->paginate(10);
        return view('staff.settings.add-staff', compact('staffMembers'));
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'role'       => ['required', 'in:Claims Adjuster,Reviewer,Admin,Viewer'],
            'department' => ['nullable', 'string', 'max:255'],
            'password'   => ['required', 'confirmed', Password::min(8)],
        ]);

        $staff = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'role'       => $validated['role'],
            'department' => $validated['department'] ?? null,
            'password'   => Hash::make($validated['password']),
            'is_admin'   => $validated['role'] === 'Admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Staff member added successfully',
            'staff'   => [
                'id'         => $staff->id,
                'name'       => $staff->name,
                'email'      => $staff->email,
                'role'       => $staff->role,
                'department' => $staff->department,
            ],
        ]);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
