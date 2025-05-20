<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ArrivalController;
use App\Models\Material;
use App\Models\MaterialMovementHistory;
use Illuminate\Support\Facades\Validator;
class MaterialController extends Controller
{
    public function getAll(){
        return Material::with(['materialBatch', 'location', 'materialStatus', 'materialType', 'unit'])
        ->whereHas('materialBatch', fn($query) => $query->where('active', true))
        ->whereHas('materialStatus', fn($query) => $query->where('active', true))
        ->whereHas('materialType', fn($query) => $query->where('active', true))
        ->whereHas('unit', fn($query) => $query->where('active', true))
        ->where('active', true)
        ->orderBy('id','desc')
        ->get();
    }

    // public function getbyId($id){

    //     //Lookup the value
    //     if (!is_null(Arrival::where('arrival.id', $id)->where('active', true)->first())){
    //         return Arrival::select(

    //             'arrival.id',
        
    //             'arrival.date',
        
    //             'arrival.vehicule_registration',
        
    //             'arrival.useradd',
        
    //             'arrival.userupdate',
        
    //             'arrival.created_at',
        
    //             'arrival.updated_at',
                
    //             'arrival.id_tier',
        
    //             'tier.name as tier',

    //             'arrival.status',
        
    //             \DB::raw("CONCAT(users.fname, ' ', users.name) as user")
        
    //         )
        
    //         ->leftJoin('users', 'users.id', '=', 'arrival.useradd')
        
    //         ->join('tier', 'tier.id', '=', 'arrival.id_tier')
        
    //         ->where('arrival.active', true)

    //         ->where('arrival.id', $id)

    //         ->orderBy('id','DESC')
        
    //         ->first();
    //     }else{
    //         return response('No Data Found',404);
    //     }
    // }

    public function create(Request $request)
    {
        $idArrival = $request["idArrival"];
        $idBatch = $request["idBatch"];
        $idLocation = $request["idLocation"];
        $idUnit = $request["idUnit"];
        $idType = $request["idType"];
        $materials = $request["materials"];
        $user = $request["user"];
        $insertedMateriaids = array();
        
        //Check Arrival Status
        $arrivalController = new ArrivalController();

        $arrival = $arrivalController->getbyId($idArrival);

        if ($arrival['status'] == 'In Transit') {
            $arrivalController->setArrivalStatus($idArrival, "Partially Received");
        }
        //Insert Materials table rows
        foreach ($materials as $material) {
            $materialModel = Material::create([
                'id_current_location' => $idLocation,
                'id_status'=> 1, //Always make this in db "Available" in material_status table
                'id_arrival'=> $idArrival,
                'id_type'=> $idType,
                'id_unit'=> $idUnit,
                'id_batch'=>$idBatch,
                'num' => $material['num'],
                'qty' => $material['qty'],
                'userAdd'=>$user,
            ]);

            array_push($insertedMateriaids,$materialModel['id']);
        }
        //Insert Mouvment History table rows for each Material
        foreach($insertedMateriaids as $id){
            MaterialMovementHistory::create([
                'id_material' =>$id,
                'id_movement_type'=> 1, //Change this to "reception" if changed in movement_type table
                'id_location_from' => 6, //Change this to "reception" if changed in location table
                'id_location_to' => $idLocation,
                'mouvement_date' => date('Y-m-d H:i:s'),
                'id_user'=> $user
            ]);
        }


    
        return response()->json($insertedMateriaids, 201);
    } 
    

    // public function update(Request $request, $id){


    //      //Lookup the value
    //      if (!is_null(Arrival::where('id', $id)->where('active', true)->first())){ 
    //         //If value is found

    //         //Validate input
    //         $validation =  Validator::make($request->all(), [
            
    //             'date' => 'required|date',
                
    //             'vehicule_registration' => 'required|string',
                
    //             'user' => 'required|integer',

    //             'id_tier' => 'required|integer'
                
    //         ]);
            
            
    //         if ($validation->fails()) {
    //             //Validation Fail
    //             return response($validation->messages(),400);
    //         } else {
    //             //Validation Success
    //             $requestBody = json_decode($request->getContent());

    //             $arrival = Arrival::where('id', $id)->where('active', true)->first();
    
    //             $arrival->date = $requestBody->date;
            
    //             $arrival->vehicule_registration = $requestBody->vehicule_registration;
        
    //             $arrival->userupdate = $requestBody->user;

    //             $arrival->id_tier = $requestBody->id_tier;
        
    //             $arrival->save();
    
    //             return response($arrival,200);
    
    //             }
    //     }else{
    //         //If value Not Found


    //         return response('No Data Found',404);
    //     }
    // }

    // public function logicalDelete($id){
    //      //Lookup the value
    //      if (!is_null(Arrival::where('id', $id)->where('active', true)->first())){ 
    //         //If value is found
    //             //Validation Success

    //             $arrival = Arrival::where('id', $id)->where('active', true)->first();
                
    //             $arrival->active = false;
        
    //             $arrival->save();
    
    //             return response("Deleted",204);
    
    //     }else{
    //         //If value Not Found


    //         return response('No Data Found',404);
    //     }
    // }
}
