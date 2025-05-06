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

            'arrival.id_tier',

            'arrival.status',
    
            'tier.name as tier',

    
            \DB::raw("CONCAT(users.fname, ' ', users.name) as user")
    
        )
    
        ->leftJoin('users', 'users.id', '=', 'arrival.useradd')
    
        ->join('tier', 'tier.id', '=', 'arrival.id_tier')
    
        ->where('arrival.active', true)

        ->orderBy('id','DESC')
    
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
                
                'arrival.id_tier',
        
                'tier.name as tier',

                'arrival.status',
        
                \DB::raw("CONCAT(users.fname, ' ', users.name) as user")
        
            )
        
            ->leftJoin('users', 'users.id', '=', 'arrival.useradd')
        
            ->join('tier', 'tier.id', '=', 'arrival.id_tier')
        
            ->where('arrival.active', true)

            ->where('arrival.id', $id)

            ->orderBy('id','DESC')
        
            ->first();
        }else{
            return response('No Data Found',404);
        }
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'vehicule_registration' => 'string|nullable',
            'user' => 'required|integer',
            'id_tier'=>'required|integer'
        ]);
    
        $arrival = Arrival::create([
            'date' => $validated['date'],
            'vehicule_registration' => $validated['vehicule_registration'],
            'useradd' => $validated['user'],
            'id_tier'=>  $validated['id_tier'],
            'status' => 'In Transit'
        ]);
    
        return response()->json($arrival, 201);
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

                'id_tier' => 'required|integer'
                
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

                $arrival->id_tier = $requestBody->id_tier;
        
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

    public function getTierbyArrivalId($id)
    {
        // Lookup the arrival record
        $arrival = Arrival::where('id', $id)->where('active', true)->first();
        
        if ($arrival) {
            // Eager load the tier relationship
            return $arrival->tier;
        } else {
            return response('No Data Found', 404);
        }
    }

}
