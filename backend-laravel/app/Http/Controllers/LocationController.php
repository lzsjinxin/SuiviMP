<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function getAll(){
        return Location::with(['materials' => function($query) {
            $query->where('active', true);
        }])
        ->where('active', true)
        ->orderBy('id', 'desc')
        ->get();
    }


     public function getbyId($id){
        if(!is_numeric($id)){
         return response('No Data Found',404);   
        }
        //Lookup the value
        if (!is_null(Location::where('id', $id)->where('active', true)->first())){
            return Location::where('id', $id)->with(['materials' => function($query) {
            $query->where('active', true);
        }])
        ->where('active', true)
        ->orderBy('id', 'desc')
        ->get();
        }else{

            return response('No Data Found',404);
        }
    }


    public function create(Request $request)
        {
            $unit = Location::create([
                'name' => $request["name"],
                'adresse' => $request["adresse"],
                'useradd' => $request["user"],
            ]);
        
            return response()->json($unit, 201);
    } 


public function update(Request $request, $id){


            //Lookup the value
            if (!is_null(Location::where('id', $id)->where('active', true)->first())){ 
                //Validation Success
                $requestBody = json_decode($request->getContent());

                $location = Location::where('id', $id)->where('active', true)->first();

                $location->name = $requestBody->name;

                $location->adresse = $requestBody->adresse;
        
                $location->userupdate = $requestBody->user;
        
                $location->save();

                return response($location,200);

                }
                return response('',404);
    }


    public function logicalDelete($id){
         //Lookup the value
         if (!is_null(Location::where('id', $id)->where('active', true)->first())){ 
            //If value is found
                //Validation Success

                $location = Location::where('id', $id)->where('active', true)->first();
                
                $location->active = false;
        
                $location->save();
    
                return response("Deleted",204);
    
        }else{
            //If value Not Found


            return response('No Data Found',404);
        }
    }
}
