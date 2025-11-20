<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationRoutesController extends Controller
{
    /**
     * Display the location routes dashboard.
     */
    public function index()
    {
        return view('location-routes.index');
    }
}
