<?php

namespace App\View\Components\Agent;

use Closure;
use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\ClaimDraft;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SideBar extends Component
{
    public $agent;
    public $stats;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->agent = Auth::guard('agent')->user();

        $this->stats = [
            'my_claims'   => Claim::where('initiated_by_agent_id', $this->agent->id)->whereIn('status', ClaimStatus::editable())->count(),
            'my_drafts' => ClaimDraft::where('agent_id', $this->agent->id)->count(),
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.agent.side-bar');
    }
}
