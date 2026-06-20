<?php
namespace App\View\Components\Surveyor;

use App\Models\Claim;
use Closure;
use Illuminate\Contracts\View\View;
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
            'submitted_claims' => Claim::where('status', 'submitted')->count(),
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
