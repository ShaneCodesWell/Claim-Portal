<?php
namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Branch;
use App\Models\Claim;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Policy;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */

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

    public function claimForms()
    {
        return view('staff.claim-forms.index');
    }

    public function createClaimForms()
    {
        return view('staff.claim-forms.create');
    }

    public function claimDocuments()
    {
        // Group claim documents by policy, eager load everything needed
        $policies = Policy::whereHas('claims.documents')
            ->with([
                'claims' => fn($q) => $q->whereHas('documents'),
                'claims.documents',
                'claims.customer',
            ])
            ->paginate(9);

        // Flatten into a structure the view can use easily
        $grouped = $policies->map(function ($policy) {
            $documents = $policy->claims->flatMap->documents;
            return [
                'policy'      => $policy,
                'customer'    => $policy->claims->first()?->customer,
                'documents'   => $documents,
                'pdf_count'   => $documents->filter(fn($d) => str_contains($d->mime_type, 'pdf'))->count(),
                'image_count' => $documents->filter(fn($d) => str_contains($d->mime_type, 'image'))->count(),
                'other_count' => $documents->filter(fn($d) => ! str_contains($d->mime_type, 'pdf') && ! str_contains($d->mime_type, 'image'))->count(),
                'total_size'  => $documents->sum('file_size'),
            ];
        });

        $totalDocs   = $grouped->sum(fn($g) => $g['documents']->count());
        $totalPdfs   = $grouped->sum(fn($g) => $g['pdf_count']);
        $totalImages = $grouped->sum(fn($g) => $g['image_count']);
        $totalOther  = $grouped->sum(fn($g) => $g['other_count']);

        return view('staff.claim-documents.index', compact(
            'grouped', 'policies', 'totalDocs', 'totalPdfs', 'totalImages', 'totalOther'
        ));
    }

    public function customers()
    {
        // Cache expensive stat counts for 5 minutes
        $stats = Cache::remember('customer_page_stats', 300, function () {
            return [
                'total_customers'  => Customer::count(),
                'active_policies'  => Policy::where('status', 'active')->count(),
                'submitted_claims' => Claim::where('status', 'incoming')->count(),
                'closed_claims'    => Claim::where('status', 'closed')->count(),
            ];
        });

        // Remove ONLY invisible/non-breaking characters, keep real spaces
        $search = trim(preg_replace('/[\x{00A0}\x{FEFF}]+/u', '', trim(request('search') ?? '')));

        $customers = Customer::select(['id', 'name', 'email', 'phone', 'external_customer_code', 'created_at'])
            ->withCount('policies')
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('external_customer_code', 'like', "%{$search}%")
                    ->orWhereRaw("REPLACE(phone, ' ', '') LIKE ?", ["%{$search}%"]);
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString(); // keeps ?search= in paginator links

        return view('staff.customers.index', compact('customers', 'stats'));
    }

    public function showCustomer(Customer $customer)
    {
        $policyIds = $customer->policies()->pluck('id');

        $policies = $customer->policies()->latest()->paginate(5);
        $claims   = Claim::whereIn('policy_id', $policyIds)->latest()->paginate(5);

        $stats = [
            'active_policies'  => $customer->policies()->where('status', 'active')->count(),
            'submitted_claims' => Claim::whereIn('policy_id', $policyIds)->where('status', 'incoming')->count(),
            'closed_claims'    => Claim::whereIn('policy_id', $policyIds)->where('status', 'closed')->count(),
            'pending_claims'   => Claim::whereIn('policy_id', $policyIds)->where('status', 'in_progress')->count(),
        ];

        return view('staff.customers.show', compact('customer', 'stats', 'policies', 'claims'));
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
