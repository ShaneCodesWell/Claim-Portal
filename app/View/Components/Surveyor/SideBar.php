<?php
namespace App\View\Components\Surveyor;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class SideBar extends Component
{
    public $stats;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->stats = [
            'all_survey_count' => Claim::where('status', 'under_survey')->count(),
            'my_queue_count'   => Claim::where('status', ClaimStatus::UNDER_SURVEY)->where('surveyed_by', Auth::id())->count(),
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View | Closure | string
    {
        return view('components.surveyor.side-bar');
    }
}
