<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MetronicsLayout extends Component
{
    public $minimize;

    public function __construct($minimize = 'off')
    {
        $this->minimize = $minimize;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('layouts.metronics');
    }
}
