<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    public $name;
    public $id;
    public $required;
    public $placeholder;
    public $value;
    public $rows;
    /**
     * Create a new component instance.
     */
    public function __construct(
        $name = '', 
        $id = '', 
        $required = false,
        $placeholder = '',
        $value = '',
        $rows = 4
    )
    {
        $this->name = $name;
        $this->id = $id ?: $name; // Use name as id if not provided
        $this->required = $required;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->rows = $rows;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.textarea');
    }
}
