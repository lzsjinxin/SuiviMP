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
}
