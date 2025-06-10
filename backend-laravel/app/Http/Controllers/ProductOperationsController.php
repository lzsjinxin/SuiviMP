<?php

namespace App\Http\Controllers;

use App\Models\ProductOperation;
use Illuminate\Http\Request;

class ProductOperationsController extends Controller
{
    public function create(Request $request){
        $idProduct = $request['id_product'];
        $operations = $request['operations'];
        $user = $request['user'];
        $createdoperations = array();
        foreach ($operations as $index => $operation) {
            $newProductOperation = ProductOperation::create([
                'id_product' => $idProduct,
                'id_operations' => $operation['id'],
                'operation_order'=>$index+1,
                'useradd' => $user,
            ]);
            array_push($createdoperations, $newProductOperation);
        }
        return response()->json($createdoperations,201);
    }
}
