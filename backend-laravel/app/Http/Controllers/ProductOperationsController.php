<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOperation;
use App\Models\ProductStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductOperationsController extends Controller
{

    public function getbyProductId($id){
        if(!is_numeric($id)){
            return response('No Data Found',404);
        }
        //Lookup the value
        if (!is_null(value: Product::where('id', $id)->where('active', true)->first())){
            return ProductOperation::
            with(['operationDefinition' => function($query){
                $query->where('active', true);
            },
                'product' => function($query){
                    $query->with(['productStatus' => function($q){
                        $q->where('active', true);
                    }])->
                    where('active', true);
                }])->
            where('id_product', $id)->where("active", true)->get();
        }else{

            return response('No Data Found',404);
        }

    }
    public function create(Request $request){
        $idProduct = $request['id_product'];
        $operations = $request['operations'];
        $user = Auth::guard('sanctum')->id();
        $createdoperations = array();

        $product  = Product::where('id', $idProduct)->first();


        $product->id_status = ProductStatus::where('status', 'Operations Affected')->first()->id ;
        $product->save();

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


    public function update(Request $request, $id){
        if (!is_numeric($id)) {
            return response('No Data Found', 404);
        }
        // Get request data
        $operations = $request["operations"]?? []; // Ensure this is an array
        $user = Auth::guard('sanctum')->id();
        $createdoperations = array();

        //Delete All Operations related to the Product
        ProductOperation::where('id_product', $id)->delete();
        //Reinsert all operations
        foreach ($operations as $index => $operation) {
            $newProductOperation = ProductOperation::create([
                'id_product' => $id,
                'id_operations' => $operation['id'],
                'operation_order'=>$index+1,
                'useradd' => $user,
            ]);
            array_push($createdoperations, $newProductOperation);
        }
            return response()->json($createdoperations,200);
    }
}
