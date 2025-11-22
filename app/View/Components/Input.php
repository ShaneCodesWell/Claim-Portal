<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public $name;
    public $id;
    public $type;
    public $required;
    public $placeholder;
    public $value;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $name = '', 
        $id = '', 
        $type = 'text', 
        $required = false,
        $placeholder = '',
        $value = ''
    )
    {
        $this->name = $name;
        $this->id = $id ?: $name;
        $this->type = $type;
        $this->required = $required;
        $this->placeholder = $placeholder;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input');
    }
}
