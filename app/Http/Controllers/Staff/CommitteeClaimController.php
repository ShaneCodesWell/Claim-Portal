<?php
namespace App\Http\Controllers\Staff;

use App\Enums\ClaimStatus;
use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Services\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommitteeClaimController extends Controller
{
    public function __construct(protected ClaimService $claimService)
    {}

    public function index(Request $request)
    {
        $claims = Claim::where('status', ClaimStatus::COMMITTEE_REVIEW)
            ->with(['customer', 'policy', 'branch'])
            ->latest('committee_review_at')
            ->paginate(15);

        return view('staff.committee.index', compact('claims'));
    }

    public function show(Claim $claim)
    {
        if (! $claim->isCommitteeReview()) {
            abort(403, 'This claim is not currently with the Claims Committee.');
        }

        $claim->load([
            'customer',
            'policy',
            'branch',
            'activities.staff',
            'documents',
            'assignedTo',
            'surveyor',
            'committeeDecidedBy',
        ]);

        return view('staff.committee.show', compact('claim'));
    }

    public function uploadDocuments(Request $request, Claim $claim)
    {
        $request->validate([
            'documents'   => 'required|array',
            'documents.*' => 'file|max:5120|mimes:jpg,jpeg,png,gif,pdf',
        ]);

        $this->claimService->attachDocuments(
            claim: $claim,
            files: $request->file('documents'),
            uploadedBy: Auth::user(),
            type: 'survey_document',
        );

        return back()->with('success', count($request->file('documents')) . ' document(s) uploaded.');
    }

    public function decide(Request $request, Claim $claim)
    {
        if (! $claim->isCommitteeReview()) {
            abort(403, 'This claim is not currently with the Claims Committee.');
        }

        $validated = $request->validate([
            'decision' => 'required|in:' . ClaimStatus::APPROVED . ',' . ClaimStatus::REJECTED,
            'notes'    => 'nullable|string|max:2000',
        ]);

        $this->claimService->makeCommitteeDecision(
            $claim,
            $validated['decision'],
            Auth::user(),
            $validated['notes'] ?? null
        );

        return redirect()
            ->route('committee.claims.index')
            ->with('success', "Decision recorded for claim {$claim->claim_number}.");
    }
}
