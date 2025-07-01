<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialType;

class MaterialTypeController extends Controller
{
    public function getAll(){
        return MaterialType::where('active', true)
        ->orderBy('id','desc')
        ->get();
    }
    public function create(Request $request)
    {
        $validated = $request->validate([
            'type'=>'required|string',
            'user' => 'required|integer'
        ]);
    
        $arrival = MaterialType::create([
            'type' => $validated['type'],
            'userAdd' => $validated['user']
        ]);
    
        return response()->json($arrival, 201);
    } 
    
        public function getOperationsPerType(){
        return MaterialType::with(['operationDefinition' => function($query) {
            $query->where('active', true);
        }])->where('active', true)
        ->orderBy('id','desc')
        ->get();
    }


}
