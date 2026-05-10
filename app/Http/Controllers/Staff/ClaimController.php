<?php
namespace App\Http\Controllers\Staff;

use App\Enums\ClaimStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ClaimDocument;
use App\Models\User;
use App\Services\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            'total_claims'  => Claim::where('assigned_to', Auth::user()->id)->count(),
            'under_review'  => Claim::where('assigned_to', Auth::user()->id)->where('status', 'under_review')->count(),
            'closed_claims' => Claim::where('assigned_to', Auth::user()->id)->where('status', 'closed')->count(),
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

    public function previewDocument(ClaimDocument $document, Request $request)
    {
        // Verify the document belongs to a claim owned by this customer
        // (skip this check on staff routes — add middleware instead)
        $path = Storage::disk('local')->path($document->file_path);

        if (! file_exists($path)) {
            Log::error('Document not found at path: ' . $path);
            abort(404, 'Document not found.');
        }

        if ($request->boolean('download')) {
            return response()->download($path, $document->original_name);
        }

        return response()->file($path, [
            'Content-Type'        => $document->mime_type,
            'Content-Disposition' => 'inline; filename="' . $document->original_name . '"',
        ]);
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

    public function edit(Claim $claim)
    {
        $claim->load(['policy', 'documents']);

        $viewMap = [
            'motor'            => 'staff.claims.edit.motor',
            'fire'             => 'staff.claims.edit.fire',
            'general_accident' => 'staff.claims.edit.general-accident',
        ];

        $view = $viewMap[$claim->claim_type] ?? null;

        if (! $view) {
            return redirect()->route('staff.claims.show', $claim)
                ->with('error', 'No edit form available for this claim type.');
        }

        return view($view, compact('claim'));
    }

    public function update(Request $request, Claim $claim)
    {
        $validated = $request->validate([
            'claim_type'         => 'required|string',
            'form_data'          => 'required|array',
            'documents'          => 'nullable|array',
            'documents.*'        => 'file|max:5120|mimes:jpg,jpeg,png,gif,pdf',
            'delete_documents'   => 'nullable|array',
            'delete_documents.*' => 'integer|exists:claim_documents,id',
            'note'               => 'nullable|string|max:500',
        ]);

        $claim->update(['form_data' => $validated['form_data']]);

        // Delete marked documents
        if (! empty($validated['delete_documents'])) {
            $docsToDelete = ClaimDocument::whereIn('id', $validated['delete_documents'])
                ->where('claim_id', $claim->id)
                ->get();

            foreach ($docsToDelete as $doc) {
                Storage::disk('local')->delete($doc->file_path);
                $doc->delete();
            }
        }

        // Attach new documents — stamped with the staff user who uploaded them
        if ($request->hasFile('documents')) {
            $this->claimService->attachDocuments(
                claim: $claim,
                files: $request->file('documents'),
                uploadedBy: Auth::user(), // ← staff user recorded here
                type: 'staff_upload',
            );
        }

        $this->claimService->logActivityPublic(
            $claim,
            Auth::user(),
            'form_updated',
            $validated['note'] ?? 'Form data updated by staff.',
            ['updated_by' => Auth::user()->id, 'role' => Auth::user()->role]
        );

        return response()->json([
            'success'      => true,
            'message'      => 'Claim updated successfully.',
            'claim_number' => $claim->claim_number,
            'redirect'     => route('staff.claims.show', $claim),
        ]);
    }
}
