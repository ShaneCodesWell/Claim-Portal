<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ClaimantInfo extends Component
{
    public $policy;
    public $customer;
    /**
     * Create a new component instance.
     */
    public function __construct($policy, $customer)
    {
        $this->policy = $policy;
        $this->customer = $customer;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.claimant-info');
    }
}
