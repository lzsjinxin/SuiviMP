<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialBatch;
class MaterialBatchController extends Controller
{
    public function getAll(){
        return MaterialBatch::where('active', true)
        ->orderBy('id','desc')
        ->get();
    }

}
