<?php

namespace App\Http\Controllers;

class DebugController extends Controller
{
    public function index()
    {
        if (env('APP_ENV') == 'production') {
            return null;
        }
        
    }
}
