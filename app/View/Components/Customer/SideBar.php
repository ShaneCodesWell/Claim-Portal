<?php
namespace App\View\Components\Customer;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\Policy;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class SideBar extends Component
{
    public $customer;
    public $stats;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->customer = Auth::guard('customer')->user();

        $customerIds = $this->customer->resolvedCustomerIds();

        $this->stats = [
            'my_policies' => Policy::whereIn('customer_id', $customerIds)->where('status', 'active')->count(),
            'my_claims'   => Claim::whereIn('customer_id', $customerIds)->whereIn('status', ClaimStatus::editable())->count(),
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View | Closure | string
    {
        return view('components.customer.side-bar');
    }
}
