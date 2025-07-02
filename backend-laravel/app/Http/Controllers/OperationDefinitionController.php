<?php

namespace App\Http\Controllers;

use App\Models\OperationDefinition;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationDefinitionController extends Controller
{
    public function getAll(){
    return OperationDefinition::with(['materialType' => function($query) {
            $query->where('active', true);
        }])
        ->where('active', true)
        ->orderBy('id', 'desc')
        ->get();
}


    public function getDefsbyProduct($id){
        if(!is_numeric($id)){
            return response('No Data Found',404);
        }
        $product = Product::find($id);
        if($product){
            $materialTypeIds = $product
                ->productMaterials()
                ->with('material.materialType')
                ->get()
                ->pluck('material.materialType.id')
                ->unique()
                ->filter();
            if ($materialTypeIds ){
                return OperationDefinition::whereIn('id_material_type', $materialTypeIds)
                    ->where('active', true)
                    ->get();
            }else{
                return response('No Data Found',404);
            }
        }else{
            return response('No Data Found',404);
        }



    }
    public function create(Request $request)
        {
            $opDef = OperationDefinition::create([
                'name' => $request["name"],
                'qty_needed' => $request["qty_needed"],
                'time_expected' => $request["time_expected"],
                'id_material_type' => $request["material_type_id"],
                'useradd' => Auth::guard('sanctum')->id(),
            ]);

            return response()->json($opDef, 201);
    }


    public function update(Request $request, $id){


            // Lookup the value
            if (!is_null(OperationDefinition::where('id', $id)->where('active', true)->first())){
                //Validation Success
                $requestBody = json_decode($request->getContent());

                $OperationDefinition = OperationDefinition::where('id', $id)->where('active', true)->first();

                $OperationDefinition->name = $requestBody->name;

                $OperationDefinition->qty_needed = $requestBody->qty_needed;

                $OperationDefinition->time_expected = $requestBody->time_expected;

                $OperationDefinition->id_material_type = $requestBody->material_type_id;

                $OperationDefinition->userupdate = Auth::guard('sanctum')->id();

                $OperationDefinition->save();

                return response($OperationDefinition,200);

                }
                return response('',404);
    }

     public function logicalDelete($id){
         //Lookup the value
         if (!is_null(OperationDefinition::where('id', $id)->where('active', true)->first())){
            //If value is found
                //Validation Success


                $operationDefinition = OperationDefinition::where('id', $id)->where('active', true)->first();

                if($operationDefinition->productOperations()->exists()){
                    return response('Definition utilisé par des produits',403);
                }else{
                $operationDefinition->active = false;

                $operationDefinition->save();

                return response("Deleted",204);
                }



        }else{
            //If value Not Found


            return response('No Data Found',404);
        }
    }

}
