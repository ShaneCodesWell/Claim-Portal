<?php
namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Claim;

class ClaimFormModal extends Component
{
    public $claim;
    /**
     * Create a new component instance.
     */
    public function __construct(Claim $claim)
    {
        $this->claim = $claim;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View | Closure | string
    {
        return view('components.claim-form-modal');
    }
}
