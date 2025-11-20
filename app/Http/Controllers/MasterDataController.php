<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    /**
     * Display the master data dashboard.
     */
    public function index()
    {
        return view('master-data.index');
    }
}
