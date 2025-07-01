<?php

namespace App\Http\Controllers;

use App\Models\MaterialStatus;
use App\Models\MovementType;
use Illuminate\Http\Request;
use App\Http\Controllers\ArrivalController;
use App\Models\Material;
use App\Models\MaterialMovementHistory;
use Illuminate\Support\Facades\Validator;
class MaterialController extends Controller
{
    public function getAll(){
        return Material::with(['arrival','materialBatch', 'location', 'materialStatus', 'materialType', 'unit'])
        ->whereHas('materialBatch', fn($query) => $query->where('active', true))
        ->whereHas('materialStatus', fn($query) => $query->where('active', true))
        ->whereHas('materialType', fn($query) => $query->where('active', true))
        ->whereHas('unit', fn($query) => $query->where('active', true))
        ->whereHas('arrival', fn($query) => $query->where('active', true))
        ->where('active', true)
        ->orderBy('id','desc')
        ->get();
    }

    public function getTransfers(){
        return MaterialMovementHistory::with([
            'material' => fn($query) => $query->where('active', true),
            'movementType', // No need to filter here since whereHas already does
            'locationFrom' => fn($query) => $query->where('active', true),
            'locationTo' => fn($query) => $query->where('active', true),
        ])
            ->where('active', true)
            ->whereHas('movementType', fn($query) =>
            $query->where('active', true)
                ->whereIn('definition', ['Transfer', 'Réception'])
            )
            ->orderBy('id', 'desc')
            ->get();
    }

        public function getAvailable(){
        return Material::with(['arrival','materialBatch', 'location', 'materialStatus', 'materialType', 'unit'])
        ->whereHas('materialBatch', fn($query) => $query->where('active', true))
        ->whereHas('materialStatus', fn($query) => $query->where('active', true)->where('status','Available'))
        ->whereHas('materialType', fn($query) => $query->where('active', true))
        ->whereHas('unit', fn($query) => $query->where('active', true))
        ->whereHas('arrival', fn($query) => $query->where('active', true))
        ->where('active', true)
        ->orderBy('id','desc')
        ->get();
    }

    public function getAvailablebyLocationId($id){
        if(!is_numeric($id)){
            return response('No Data Found',404);
        }
        return Material::with(['arrival','materialBatch', 'location', 'materialStatus', 'materialType', 'unit'])
            ->whereHas('materialBatch', fn($query) => $query->where('active', true))
            ->whereHas('materialStatus', fn($query) => $query->where('active', true)->where('status','Available'))
            ->whereHas('materialType', fn($query) => $query->where('active', true))
            ->whereHas('unit', fn($query) => $query->where('active', true))
            ->whereHas('arrival', fn($query) => $query->where('active', true))
            ->where('id_current_location', $id)
            ->where('active', true)
            ->orderBy('id','desc')
            ->get();
    }
    public function getTransferedbyLocationId($id){
        if(!is_numeric($id)){
            return response('No Data Found',404);
        }
        return Material::with(['arrival','materialBatch', 'location', 'materialStatus', 'materialType', 'unit'])
            ->whereHas('materialBatch', fn($query) => $query->where('active', true))
            ->whereHas('materialStatus', fn($query) => $query->where('active', true)->where('status','Transfered'))
            ->whereHas('materialType', fn($query) => $query->where('active', true))
            ->whereHas('unit', fn($query) => $query->where('active', true))
            ->whereHas('arrival', fn($query) => $query->where('active', true))
            ->where('id_current_location', $id)
            ->where('active', true)
            ->orderBy('id','desc')
            ->get();
    }

    public function getbyId($id){
        if(!is_numeric($id)){
         return response('No Data Found',404);
        }
        //Lookup the value
        if (!is_null(Material::where('id', $id)->where('active', true)->first())){
            return Material::with(['materialBatch', 'location', 'materialStatus', 'materialType', 'unit'])
            ->whereHas('materialBatch', fn($query) => $query->where('active', true))
            ->whereHas('materialStatus', fn($query) => $query->where('active', true))
            ->whereHas('materialType', fn($query) => $query->where('active', true))
            ->whereHas('unit', fn($query) => $query->where('active', true))
            ->where('active', true)
            ->where('id',$id )
            ->orderBy('id','desc')
            ->get();
        }else{
            return response('No Data Found',404);
        }
    }

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
                'id_status'=> 1, // TODO: Always make this in db "Available" in material_status table
                'id_arrival'=> $idArrival,
                'id_type'=> $idType,
                'id_unit'=> $idUnit,
                'id_batch'=>$idBatch,
                'num' => $material['num'],
                'qty' => $material['qty'],
                'useradd'=>$user,
            ]);

            array_push($insertedMateriaids,$materialModel['id']);
        }
        //Insert Mouvement History table rows for each Material
        foreach($insertedMateriaids as $id){
            MaterialMovementHistory::create([
                'id_material' =>$id,
                'id_movement_type'=> 1, // TODO:Change this to "reception" if changed in movement_type table
                'id_location_from' => 6, // TODO: Change this to "reception" if changed in location table
                'id_location_to' => $idLocation,
                'mouvement_date' => date('Y-m-d H:i:s'),
                'id_user'=> $user
            ]);
        }



        return response()->json($insertedMateriaids, 201);
    }


    public function update(Request $request, $id){

        if(!is_numeric($id)){
         return response('No Data Found',404);
        }

         //Lookup the value
         if (!is_null(Material::where('id', $id)->where('active', true)->first())){
            //If value is found
        $dbMaterial = Material::where('id', $id)->where('active', true)->first();
        $idArrival = $request["idArrival"];
        $idBatch = $request["idBatch"];
        $idLocation = $request["idLocation"];
        $idUnit = $request["idUnit"];
        $idType = $request["idType"];
        $materials = $request["materials"];
        $user = $request["user"];
        //Update Materials table rows
        foreach ($materials as $material) {
            $dbMaterial->id_current_location = $idLocation;
            $dbMaterial->id_arrival = $idArrival;
            $dbMaterial->id_unit = $idUnit;
            $dbMaterial->id_type = $idType;
            $dbMaterial->id_batch = $idBatch;
            $dbMaterial->num = $material['num'];
            $dbMaterial->qty = $material['qty'];
            $dbMaterial->userupdate = $user;
            $dbMaterial->save();
        }

        return response()->json($dbMaterial, 200);


        }else{
            //If value Not Found


            return response('No Data Found',404);
        }
    }

    public function logicalDelete($id){
         //Lookup the value
         if (!is_null(Material::where('id', $id)->where('active', true)->first())){
            //If value is found
                //Validation Success

                $arrival = Material::where('id', $id)->where('active', true)->first();

                $arrival->active = false;

                $arrival->save();

                return response("Deleted",204);

        }else{
            //If value Not Found


            return response('No Data Found',404);
        }
    }

    public function initiateTransfer(Request $request){
        $materials = $request["materials"];
        $id_location_from =  $request["id_location_from"];
        $id_location_to =  $request["id_location_to"];
        $user = $request["user"];
        $idStatusTransfered = MaterialStatus::where("status","Transfered")->where("active",true)->first()->id;
        $idMovementTypeTransfered = MovementType::where("definition","Transfer")->where("active",true)->first()->id;

        foreach ($materials as $material) {
            //Get Material Object
            $dbMaterial = Material::where('id', $material)->first();
            //Change the material Status
            $dbMaterial->id_status = $idStatusTransfered;
            $dbMaterial->userupdate = $user;
            $dbMaterial->save();

            //Insert Movement History Line
            MaterialMovementHistory::create([
                'id_material' =>$material,
                'id_movement_type'=> $idMovementTypeTransfered,
                'id_location_from' => $id_location_from,
                'id_location_to' => $id_location_to,
                'mouvement_date' => date('Y-m-d H:i:s'),
                'id_user'=> $user
            ]);
        }
        return response()->json($materials, 201);
    }
    public function receive(Request $request){
        $materials = $request["materials"];
        $user = $request["user"];
        $idStatusReceived = MaterialStatus::where("status","Available")->where("active",true)->first()->id;
        $idMovementTypeTransfered = MovementType::where("definition","Transfer")->where("active",true)->first()->id;
        $idMovementTypeReceive = MovementType::where("definition","Réception")->where("active",true)->first()->id;

        foreach ($materials as $material) {
            //Get Material Object
            $dbMaterial = Material::where('id', $material)->first();
            //Change the material Status
            $dbMaterial->id_status = $idStatusReceived;
            $dbMaterial->userupdate = $user;
            $dbMaterial->save();
            //Get Last Line from Material Movement History
            $dbid_location_from = MaterialMovementHistory::where("id_material",$material)
                                    ->where('id_movement_type',$idMovementTypeTransfered)->
                                    orderBy('id', 'desc')
                                    ->first()
                                    ->id_location_from;
            $dbid_location_to = MaterialMovementHistory::where("id_material",$material)->where('id_movement_type',$idMovementTypeTransfered)->                                 orderBy('id', 'desc')
                ->first()
                ->id_location_to;

            //Insert Movement History Line
            MaterialMovementHistory::create([
                'id_material' =>$material,
                'id_movement_type'=> $idMovementTypeReceive,
                'id_location_from' => $dbid_location_from,
                'id_location_to' => $dbid_location_to,
                'mouvement_date' => date('Y-m-d H:i:s'),
                'id_user'=> $user
            ]);
        }
        return response()->json($materials, 201);
    }
}
