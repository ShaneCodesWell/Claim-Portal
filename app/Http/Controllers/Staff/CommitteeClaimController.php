<?php
namespace App\Http\Controllers\Staff;

use App\Enums\ClaimStatus;
use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Services\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            'activities.user',
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
        Log::info('Before validation', $request->all());

        // Log::info('Committee decision request received', [
        //     'claim_id'     => $claim->id,
        //     'claim_number' => $claim->claim_number,
        //     'status'       => $claim->status,
        //     'user_id'      => Auth::id(),
        //     'request'      => $request->all(),
        // ]);

        if (! $claim->isCommitteeReview()) {

            Log::warning('Claim is not in committee review state', [
                'claim_id' => $claim->id,
                'status'   => $claim->status,
            ]);

            abort(403, 'This claim is not currently with the Claims Committee.');
        }

        $validated = $request->validate([
            'decision' => 'required|in:' . ClaimStatus::APPROVED . ',' . ClaimStatus::REJECTED,
            'notes'    => 'nullable|string|max:2000',
        ]);

        Log::info('Committee decision validated', [
            'claim_id' => $claim->id,
            'decision' => $validated['decision'],
            'notes'    => $validated['notes'] ?? null,
        ]);

        try {

            Log::info('Calling makeCommitteeDecision()', [
                'claim_id' => $claim->id,
            ]);

            $this->claimService->makeCommitteeDecision(
                $claim,
                $validated['decision'],
                Auth::user(),
                $validated['notes'] ?? null
            );

            Log::info('Committee decision completed successfully', [
                'claim_id'   => $claim->id,
                'new_status' => $validated['decision'],
            ]);

        } catch (\Throwable $e) {

            Log::error('Committee decision failed', [
                'claim_id' => $claim->id,
                'message'  => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
                'trace'    => $e->getTraceAsString(),
            ]);

            throw $e;
        }

        return redirect()
            ->route('committee.claims.index')
            ->with('success', "Decision recorded for claim {$claim->claim_number}.");
    }
}
