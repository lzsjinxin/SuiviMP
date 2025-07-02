<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function getAll(){
    return Department::with(['users' => function($query) {
            $query->where('active', true);
        }])
        ->where('active', true)
        ->orderBy('id', 'desc')
        ->get();
}

    public function create(Request $request)
        {
            $unit = Department::create([
                'name' => $request["name"],
                'useradd' => Auth::guard('sanctum')->id(),
            ]);

            return response()->json($unit, 201);
    }
    public function getbyId($id){
        if(!is_numeric($id)){
         return response('No Data Found',404);
        }
        //Lookup the value
        if (!is_null(Department::where('id', $id)->where('active', true)->first())){
            return Department::where('id', $id)->with(['users' => function($query) {
            $query->where('active', true);
        }])
        ->where('active', true)
        ->orderBy('id', 'desc')
        ->get();
        }else{

            return response('No Data Found',404);
        }
    }

    public function update(Request $request, $id){


            //Lookup the value
            if (!is_null(Department::where('id', $id)->where('active', true)->first())){
                //Validation Success
                $requestBody = json_decode($request->getContent());

                $department = Department::where('id', $id)->where('active', true)->first();

                $department->name = $requestBody->name;


                $department->userupdate = Auth::guard('sanctum')->id();

                $department->save();

                return response($department,200);

                }
                return response('',404);
    }

    public function logicalDelete($id){
         //Lookup the value
         if (!is_null(Department::where('id', $id)->where('active', true)->first())){
            //If value is found
                //Validation Success

                $department = Department::where('id', $id)->where('active', true)->first();

                $department->active = false;

                $department->save();

                return response("Deleted",204);

        }else{
            //If value Not Found


            return response('No Data Found',404);
        }
    }
}
