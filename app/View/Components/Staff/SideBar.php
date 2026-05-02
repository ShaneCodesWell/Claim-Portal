<?php
namespace App\View\Components\Staff;

use Closure;
use App\Models\Claim;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class SideBar extends Component
{
    public $stats;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $userId = Auth::id();

        $this->stats = [
            'my_claims'     => Claim::where('assigned_to', $userId)->count(),
            'incoming_claims' => Claim::where('status', 'incoming')->count(),
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View | Closure | string
    {
        return view('components.staff.side-bar');
    }
}
