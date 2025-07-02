<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialBatch;
use Illuminate\Support\Facades\Auth;

class MaterialBatchController extends Controller
{
    public function getAll(){
        return MaterialBatch::where('active', true)
        ->orderBy('id','desc')
        ->get();
    }


    public function create(Request $request)
    {
        $validated = $request->validate([
            'expiry_date' => 'date|nullable',
            'can_be_expired' => 'required|boolean',
            'batch_number'=>'required|string',
            'user' => 'required|integer'
        ]);

        $arrival = MaterialBatch::create([
            'batch_number' => $validated['batch_number'],
            'can_be_expired' => $validated['can_be_expired'],
            'expiry_date' => $validated['expiry_date'],
            'userAdd' => Auth::guard('sanctum')->id()
        ]);

        return response()->json($arrival, 201);
    }
}
