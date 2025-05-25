<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Unit;

class UnitController extends Controller
{
    public function getAll(){
        return Unit::where('active', true)
               ->orderBy('id', 'DESC')
               ->get()
               ->map(function ($unit) {
                   $unit->has_materials = $unit->materials()->exists();
                   return $unit;
               });
        ;
    }
    public function getbyId($id){
        if(!is_numeric($id)){
         return response('No Data Found',404);   
        }
        //Lookup the value
        if (!is_null(Unit::where('id', $id)->where('active', true)->first())){
            return Unit::where('id', $id)->where('active', true)->get()->map(function ($unit) {
                   $unit->has_materials = $unit->materials()->exists();
                   return $unit;
               });
        }else{

            return response('No Data Found',404);
        }
    }


    public function create(Request $request)
        {
            $unit = Unit::create([
                'title' => $request["title"],
                'useradd' => $request["user"],
            ]);
        
            return response()->json($unit, 201);
    } 

    public function update(Request $request, $id){


            //Lookup the value
            if (!is_null(Unit::where('id', $id)->where('active', true)->first())){ 
                //Validation Success
                $requestBody = json_decode($request->getContent());

                $unit = Unit::where('id', $id)->where('active', true)->first();

                $unit->title = $requestBody->title;
            
        
                $unit->userupdate = $requestBody->user;
        
                $unit->save();

                return response($unit,200);

                }
                return response('',404);
    }

    public function logicalDelete($id){
         //Lookup the value
         if (!is_null(Unit::where('id', $id)->where('active', true)->first())){ 
            //If value is found
                //Validation Success

                $unit = Unit::where('id', $id)->where('active', true)->first();
                
                $unit->active = false;
        
                $unit->save();
    
                return response("Deleted",204);
    
        }else{
            //If value Not Found


            return response('No Data Found',404);
        }
    }
}
