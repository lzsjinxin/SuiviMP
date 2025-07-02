<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tier;
use Illuminate\Support\Facades\Auth;

class TierController extends Controller
{
    public function getAll(){
        return Tier::with([
        'productSeries' => function($query) {
            $query->where('active', true);
        },
        'arrivals' => function($query) {
            $query->where('active', true);
        },
        'fabricationOrders'=>function($query) {
            $query->where('active', true);
        }
        ])->where('active', true)->orderBy('id','DESC')->get();
    }

    public function getAllSuppliers(){
        return Tier::where('active', true)->where('type','S')->orWhere('type' ,'CS')->orderBy('id','DESC')->get();
    }
    public function getAllClients(){
        return Tier::where('active', true)->where('type','C')->orWhere('type' ,'CS')->orderBy('id','DESC')->get();
    }
    public function getbyId($id){
         if(!is_numeric($id)){
         return response('No Data Found',404);
        }
        //Lookup the value
        if (!is_null(Tier::where('id', $id)->where('active', true)->first())){
            return Tier::where('id', $id)->with([
        'productSeries' => function($query) {
            $query->where('active', true);
        },
        'arrivals' => function($query) {
            $query->where('active', true);
        },
        'fabricationOrders'=>function($query) {
            $query->where('active', true);
        }
        ])->where('active', true)->orderBy('id','DESC')->get();
        }else{

            return response('No Data Found',404);
        }
    }

    public function create(Request $request){
    $tier = Tier::create([
                'type' => $request["type"],
                'name' => $request["name"],
                'adresse' => $request["adresse"],
                'country' => $request["country"],
                'city' => $request["city"],
                'ice' => $request["ice"],
                'useradd' => Auth::guard('sanctum')->id(),
            ]);

            return response()->json($tier, 201);
    }

    public function update(Request $request, $id){

            //Lookup the value
            if (!is_null(Tier::where('id', $id)->where('active', true)->first())){
                //Validation Success
                $requestBody = json_decode($request->getContent());

                $tier = Tier::where('id', $id)->where('active', true)->first();

                $tier->type = $requestBody->type;

                $tier->name = $requestBody->name;

                $tier->adresse = $requestBody->adresse;

                $tier->country = $requestBody->country;

                $tier->city = $requestBody->city;

                $tier->ice = $requestBody->ice;

                $tier->userupdate = Auth::guard('sanctum')->id();

                $tier->save();

                return response($tier,200);

                }
                return response('',404);
    }

    public function logicalDelete($id){
 //Lookup the value
         if (!is_null(Tier::where('id', $id)->where('active', true)->first())){
            //If value is found
                //Validation Success

                $tier = Tier::where('id', $id)->where('active', true)->first();

                $tier->active = false;

                $tier->save();

                return response("Deleted",204);

        }else{
            //If value Not Found


            return response('No Data Found',404);
        }
        }

}
