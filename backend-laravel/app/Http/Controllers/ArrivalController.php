<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arrival;
use Illuminate\Support\Facades\Validator;

class ArrivalController extends Controller
{
    public function getAll(){
        return Arrival::select(

            'arrival.id',
    
            'arrival.date',
    
            'arrival.vehicule_registration',
    
            'arrival.useradd',
    
            'arrival.userupdate',
    
            'arrival.created_at',
    
            'arrival.updated_at',
    
            'tier.name as tier',
    
            \DB::raw("CONCAT(users.fname, ' ', users.name) as user")
    
        )
    
        ->leftJoin('users', 'users.id', '=', 'arrival.useradd')
    
        ->join('tier', 'tier.id', '=', 'arrival.id_tier')
    
        ->where('arrival.active', true)
    
        ->get();
    }

    public function getbyId($id){

        //Lookup the value
        if (!is_null(Arrival::where('arrival.id', $id)->where('active', true)->first())){
            return Arrival::select(

                'arrival.id',
        
                'arrival.date',
        
                'arrival.vehicule_registration',
        
                'arrival.useradd',
        
                'arrival.userupdate',
        
                'arrival.created_at',
        
                'arrival.updated_at',
        
                'tier.name as tier',
        
                \DB::raw("CONCAT(users.fname, ' ', users.name) as user")
        
            )
        
            ->leftJoin('users', 'users.id', '=', 'arrival.useradd')
        
            ->join('tier', 'tier.id', '=', 'arrival.id_tier')
        
            ->where('arrival.active', true)

            ->where('arrival.id', $id)
        
            ->first();
        }else{
            return response('No Data Found',404);
        }
    }

    public function create(Request $request){

        $validation =  Validator::make($request->all(), [
            
            'date' => 'required|date',
            
            'vehicule_registration' => 'required|string',
            
            'user' => 'required|integer',
            
        ]);
        
        
        if ($validation->fails()) {
            return response($validation->messages(),400);
        } else {
                $requestBody = json_decode($request->getContent());
                $arrival = new Arrival();

                $arrival->date = $requestBody->date;
        
                $arrival->vehicule_registration = $requestBody->vehicule_registration;
        
                $arrival->useradd = $requestBody->user;
        
                $arrival->save();


                return response($arrival,201);
            }
          
    }

    public function update(Request $request, $id){


         //Lookup the value
         if (!is_null(Arrival::where('id', $id)->where('active', true)->first())){ 
            //If value is found

            //Validate input
            $validation =  Validator::make($request->all(), [
            
                'date' => 'required|date',
                
                'vehicule_registration' => 'required|string',
                
                'user' => 'required|integer',
                
            ]);
            
            
            if ($validation->fails()) {
                //Validation Fail
                return response($validation->messages(),400);
            } else {
                //Validation Success
                $requestBody = json_decode($request->getContent());

                $arrival = Arrival::where('id', $id)->where('active', true)->first();
    
                $arrival->date = $requestBody->date;
            
                $arrival->vehicule_registration = $requestBody->vehicule_registration;
        
                $arrival->userupdate = $requestBody->user;
        
                $arrival->save();
    
                return response($arrival,200);
    
                }
        }else{
            //If value Not Found


            return response('No Data Found',404);
        }
    }

    public function logicalDelete($id){
         //Lookup the value
         if (!is_null(Arrival::where('id', $id)->where('active', true)->first())){ 
            //If value is found
                //Validation Success

                $arrival = Arrival::where('id', $id)->where('active', true)->first();
                
                $arrival->active = false;
        
                $arrival->save();
    
                return response("Deleted",204);
    
        }else{
            //If value Not Found


            return response('No Data Found',404);
        }
    }

}
