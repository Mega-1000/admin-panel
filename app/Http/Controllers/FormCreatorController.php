<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class FormCreatorController extends Controller
{
    /**
     * Show form creator with all forms. Powered by Livewire.
     *
     * @return View
     */
    public function create(): View
    {
        return view('form-creator.create');
    }
}
