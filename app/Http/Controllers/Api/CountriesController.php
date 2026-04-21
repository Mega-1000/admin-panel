<?php

namespace App\Http\Controllers\Api;

use App\Entities\Country;
use App\Http\Controllers\Controller;

class CountriesController extends Controller
{
    use ApiResponsesTrait;

    public function index()
    {
        return response()->json(Country::all());
    }
}
