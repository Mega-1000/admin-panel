<?php

namespace App\Http\Controllers;

class DebugController extends Controller
{
    public function index()
    {
        if (config('app.env') == 'production') {
            return null;
        }

    }
}
