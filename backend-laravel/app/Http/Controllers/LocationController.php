<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function getAll(){
        return Location::where('active', true)
        ->orderBy('id','desc')
        ->get();
    }
}
