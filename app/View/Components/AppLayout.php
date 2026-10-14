<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function __construct(
        public $lists = null,
        public $activeList = null,
    ) {
        $this->lists ??= auth()->check()
            ? auth()->user()->todoLists()->withCount('openTasks')->orderBy('position')->get()
            : collect();
    }

    public function render(): View
    {
        return view('layouts.app');
    }
}
