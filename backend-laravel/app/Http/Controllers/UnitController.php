<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Unit;

class UnitController extends Controller
{
    public function getAll(){
        return Unit::where('active', true)->orderBy('id','DESC')->get();
    }
}
