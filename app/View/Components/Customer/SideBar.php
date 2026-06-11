<?php

namespace App\View\Components\Customer;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SideBar extends Component
{
    public $customer;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->customer = Auth::guard('customer')->user();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.customer.side-bar');
    }
}
