<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function getAll(){
        return Department::with(['users'])
        ->whereHas('users', fn($query) => $query->where('active', true))
        ->where('active', true)
        ->orderBy('id','desc')
        ->get();
    }
}
