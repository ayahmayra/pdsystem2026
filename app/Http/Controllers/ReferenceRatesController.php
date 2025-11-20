<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReferenceRatesController extends Controller
{
    /**
     * Display the reference rates dashboard.
     */
    public function index()
    {
        return view('reference-rates.index');
    }
}
