<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function getAll(){
        return Product::with([
            'productSeries' => function($query) {
            $query->where('active', true);
        },
        'productStatus' => function($query) {
            $query->where('active', true);
        },
        'productOperations' => function($query) {
            $query->where('active', true)
                ->with(['operationDefinition' => function($q) {
                $q->where('active', true);
            }]);
        },
        'productMaterials' => function($query) {
            $query->where('active', true);
        },
        ])
        ->where('active', true)
        ->orderBy('id', 'desc')
        ->get();
    }

    public function getCreated(){
        return Product::with([
            'productSeries' => function($query) {
                $query->where('active', true);
            },
            'productStatus' => function($query) {
                $query->where('active', true);
            },
            'productOperations' => function($query) {
                $query->where('active', true);
            },
            'productMaterials' => function($query) {
                $query->where('active', true);
            },
        ])
            ->whereHas('productStatus', function($query) {
            $query->where('status', 'Created')->where('active', true);
        })
            ->where('active', true)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getbyTier($id){

        if(!is_numeric($id)){
            return response('No Data Found',404);
        }

        return  Product::with(['productSeries'])
            ->whereHas('productSeries', function ($query) use ($id) {
                $query->where('id_tier', $id);
            })
            ->where('active', true)->get();
    }

    public function getbyId($id){

        if(!is_numeric($id)){
        return response('No Data Found',404);
        }
        //Lookup the value
        if (!is_null(value: Product::where('id', $id)->where('active', true)->first())){
            return Product::where('id', $id)->with([
            'productSeries' => function($query) {
            $query->where('active', true);
        },
        'productStatus' => function($query) {
            $query->where('active', true);
        },
        'productOperations' => function($query) {
            $query->where('active', true);
        },
        'productMaterials' => function($query) {
            $query->where('active', true);
        },
        ])
        ->where('active', true)
        ->orderBy('id', 'desc')
        ->get();

        }else{

            return response('No Data Found',404);
        }

    }

    public function create(Request $request){
        $idSeries = $request["series"];
        $title = $request["title"];
        $materials = $request["materials"];
        $user = Auth::guard('sanctum')->id();
        $product = Product::create([
                'id_series' =>$idSeries,
                'id_status' =>6,  // TODO: Always make this in db "Created" in Product_status table
                'title' => $title,
                'useradd' => $user,
            ]);
        //Insert Product_materials table rows
        foreach ($materials as $material) {
            ProductMaterial::create([
                'id_material'=> $material,
                'id_product'=> $product->id,
                'useradd'=>$user,
            ]);
        }
        return response()->json($product,201);
    }


public function update(Request $request, $id) {
    if (!is_numeric($id)) {
        return response('No Data Found', 404);
    }

    // Lookup the product
    $dbProduct = Product::where('id', $id)->where('active', true)->first();

    if (!$dbProduct) {
        return response('No Data Found', 404);
    }

    // Get request data
    $idSeries = $request["series"];
    $title = $request["title"];
    $materials = $request["materials"] ?? []; // Ensure this is an array
    $user = Auth::guard('sanctum')->id();

    // Update Product table
    $dbProduct->id_series = $idSeries;
    $dbProduct->title = $title;
    $dbProduct->userupdate = $user;
    $dbProduct->save();

    // Get current materials
    $currentMaterials = ProductMaterial::where('id_product', $dbProduct->id)
        ->where('active', true)
        ->pluck('id_material')
        ->toArray();

    // Materials to add (in request but not in DB)
    $materialsToAdd = array_diff($materials, $currentMaterials);

    // Materials to remove (in DB but not in request)
    $materialsToRemove = array_diff($currentMaterials, $materials);

    // Add new materials
    foreach ($materialsToAdd as $materialId) {
        ProductMaterial::create([
            'id_material' => $materialId,
            'id_product' => $dbProduct->id,
            'useradd' => $user,
            'active' => true
        ]);
    }

    // Soft delete removed materials
    ProductMaterial::where('id_product', $dbProduct->id)
        ->whereIn('id_material', $materialsToRemove)
        ->delete();

    return response()->json([
        'message' => 'Product updated successfully',
        'added' => $materialsToAdd,
        'removed' => $materialsToRemove
    ], 200);
}




}
