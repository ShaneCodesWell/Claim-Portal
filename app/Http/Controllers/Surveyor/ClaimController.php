<?php
namespace App\Http\Controllers\Surveyor;

use App\Enums\ClaimStatus;
use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Services\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClaimController extends Controller
{
    public function __construct(protected ClaimService $claimService)
    {}

    public function index(Request $request)
    {
        $query = Claim::where('status', ClaimStatus::UNDER_SURVEY)
            ->with(['customer', 'policy', 'branch']);

        match ($request->filter) {
            'low'    => $query->where('amount', '<=', 30000),
            'medium' => $query->whereBetween('amount', [30001, 100000]),
            'high'   => $query->where('amount', '>', 100000),
            default  => null,
        };

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('policy', fn($q) => $q->where('policy_number', 'like', "%{$search}%"));
            });
        }

        $claims = $query->latest('surveyed_at')->paginate(15)->withQueryString();

        return view('surveyor.claims.index', compact('claims'));
    }

    public function show(Claim $claim)
    {
        if (! $claim->isUnderSurvey()) {
            abort(403, 'This claim is not currently under survey.');
        }

        $claim->load(['customer', 'policy', 'branch', 'activities.user', 'documents']);

        return view('surveyor.claims.show', compact('claim'));
    }

    public function complete(Request $request, Claim $claim)
    {
        if (! $claim->isUnderSurvey()) {
            abort(403, 'This claim is not currently under survey.');
        }

        $validated = $request->validate([
            'survey_notes' => 'required|string|max:2000',
        ]);

        $this->claimService->completeSurvey($claim, Auth::user(), $validated['survey_notes']);

        return redirect()
            ->route('surveyor.claims.index')
            ->with('success', "Survey completed for claim {$claim->claim_number}.");
    }
}
