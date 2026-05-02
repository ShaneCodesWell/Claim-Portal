<?php
namespace App\Http\Controllers\Staff;

use App\Enums\ClaimStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\User;
use App\Services\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClaimController extends Controller
{
    public function __construct(protected ClaimService $claimService)
    {}

    public function index()
    {
        $claims = Claim::with(['customer', 'policy', 'assignedTo', 'branch'])
            ->latest()
            ->paginate(5);

        return view('staff.claims.index', compact('claims'));
    }

    public function myQueue()
    {
        $claims = Claim::where('assigned_to', Auth::user()->id)
            ->whereNotIn('status', ClaimStatus::terminal())
            ->with(['customer', 'policy'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_claims'     => Claim::where('assigned_to', Auth::user()->id)->count(),
            'under_review'   => Claim::where('assigned_to', Auth::user()->id)->where('status', 'under_review')->count(),
            'closed_claims'    => Claim::where('assigned_to', Auth::user()->id)->where('status', 'closed')->count(),
        ];

        return view('staff.claims.my-queue', compact('claims', 'stats'));
    }

    public function show(Claim $claim)
    {
        $claim->load(['customer', 'policy', 'assignedTo', 'branch', 'activities.user', 'documents']);

        $staffMembers = User::where('is_active', true)
            ->whereIn('role', UserRole::staffRoles())
            ->get();

        return view('staff.claims.show', compact('claim', 'staffMembers'));
    }

    public function print(Claim $claim)
    {
        $claim->load(['customer', 'policy']);

        if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
            try {
                return view('staff.claims.print', compact('claim'))->render();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return view('staff.claims.print', compact('claim'));
    }

    public function assign(Request $request, Claim $claim)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'note'        => 'nullable|string|max:500',
        ]);

        $assignee = User::findOrFail($request->assigned_to);

        $this->claimService->assign(
            claim: $claim,
            assignee: $assignee,
            assignedBy: Auth::user(),
            note: $request->note,
        );

        return back()->with('success', "Claim assigned to {$assignee->name}.");
    }

    public function updateStatus(Request $request, Claim $claim)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', ClaimStatus::all()),
            'note'   => 'nullable|string|max:1000',
        ]);

        $this->claimService->updateStatus(
            claim: $claim,
            newStatus: $request->status,
            updatedBy: Auth::user(),
            note: $request->note,
        );

        return back()->with('success', 'Claim status updated.');
    }

    public function requestInfo(Request $request, Claim $claim)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $this->claimService->requestInfo(
            claim: $claim,
            requestedBy: Auth::user(),
            note: $request->note,
        );

        return back()->with('success', 'Information request sent.');
    }

    public function updateFormData(Request $request, Claim $claim)
    {
        $request->validate([
            'form_data' => 'required|array',
            'note'      => 'nullable|string|max:500',
        ]);

        $this->claimService->updateFormData(
            claim: $claim,
            newData: $request->form_data,
            updatedBy: Auth::user(),
            note: $request->note,
        );

        return back()->with('success', 'Claim details updated.');
    }
}
