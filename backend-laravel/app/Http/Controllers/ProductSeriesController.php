<?php

namespace App\Http\Controllers;

use App\Models\ProductSeries;
use Illuminate\Http\Request;

class ProductSeriesController extends Controller
{
    public function getAll(){
         return ProductSeries::with([
            'tier' => function($query) {
            $query->where('active', true);
        },
        'products' => function($query) {
            $query->where('active', true);
        }
        ])
        ->where('active', true)
        ->orderBy('id', 'desc')
        ->get();
    }

        public function getbyId($id){

         if(!is_numeric($id)){
         return response('No Data Found',404);   
        }
        //Lookup the value
        if (!is_null(value: ProductSeries::where('id', $id)->where('active', true)->first())){
        return ProductSeries::where('id', $id)->with([
            'tier' => function($query) {
            $query->where('active', true);
        },
        'products' => function($query) {
            $query->where('active', true);
        }
        ])
        ->where('active', true)
        ->orderBy('id', 'desc')
        ->get();
        }else{

            return response('No Data Found',404);
        }

    }


    public function update(Request $request, $id){
        
        if(!is_numeric($id)){
         return response('No Data Found',404);   
        }

         //Lookup the value
         if (!is_null(ProductSeries::where('id', $id)->where('active', true)->first())){ 
            //If value is found
        $productSeries = ProductSeries::where('id', $id)->where('active', true)->first();
        $idTier = $request["idTier"];
        $series = $request["series"];
        $user = $request["user"];
        //Update Materials table rows
        foreach ($series as $serie) {
            $productSeries->id_tier = $idTier;
            $productSeries->series = $serie['name'];
            $productSeries->userupdate = $user;
            $productSeries->save();
        }
    
        return response()->json($productSeries, 200);
    
                
        }else{
            //If value Not Foundd
            return response('No Data Found',404);
        }
    }



        public function create(Request $request)
    {
        $idTier = $request["idTier"];
        $seriesData = $request["series"];
        $user = $request["user"];
        
        //Insert series table rows
        foreach ($seriesData as $serieData) {
            $series = ProductSeries::create([
                'id_tier' => $idTier,
                'series' => $serieData['name'],
                'useradd'=>$user,
            ]);
        }
        return response()->json($series, 201);
    } 




        public function logicalDelete($id){
         //Lookup the value
         if (!is_null(ProductSeries::where('id', $id)->where('active', true)->first())){ 
            //If value is found
                //Validation Success

                $productSeries = ProductSeries::where('id', $id)->where('active', true)->first();
                
                $productSeries->active = false;
        
                $productSeries->save();
    
                return response("Deleted",204);
    
        }else{
            //If value Not Found


            return response('No Data Found',404);
        }
    }
}
