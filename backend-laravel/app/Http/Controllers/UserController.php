<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function getAll(){
        return User::with(['department' => function($query) {
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
        if (!is_null(value: User::where('id', $id)->where('active', true)->first())){
            return User::where('id', $id)->with(['department' => function($query) {
            $query->where('active', true);
        }])
        ->where('active', true)
        ->orderBy('id', 'desc')
        ->get();
        }else{

            return response('No Data Found',404);
        }
    }

    public function getByUuid(string $uuid)
    {
        // Reject obviously bad UUIDs right away
        if (! Str::isUuid($uuid)) {
            return response()->json(['message' => 'Code Qr Non valide'], 400);
        }

        $user = User::with(['department' => fn ($q) => $q->where('active', true)])
            ->where([
                'uuid'   => $uuid,
                'active' => true,
            ])
            ->first();

        if (! $user) {
            return response()->json(['message' => 'Cet Utilisateur n\'existe pas'], 404);
        }

        return $user;
    }

    public function update(Request $request, $id){


            //Lookup the value
            if (!is_null(User::where('id', $id)->where('active', true)->first())){
                //Validation Success
                $requestBody = json_decode($request->getContent());

                $user = User::where('id', $id)->where('active', true)->first();

                $user->id_dept = $requestBody->id_dept;

                $user->fname = $requestBody->fname;

                $user->name = $requestBody->name;

                $user->userupdate = $requestBody->user;

                $user->save();

                return response($user,200);

                }
                return response('',404);
    }


     public function create(Request $request)
        {
            $user = User::create([
                'id_dept' => $request["id_dept"],
                'fname' => $request["fname"],
                'name' => $request["name"],
                'uuid'=> Str::uuid(),
                'useradd' => $request["user"],
            ]);

            return response()->json($user, 201);
    }




    public function logicalDelete($id){
         //Lookup the value
         if (!is_null(User::where('id', $id)->where('active', true)->first())){
            //If value is found
                //Validation Success

                $user = User::where('id', $id)->where('active', true)->first();

                $user->active = false;

                $user->save();

                return response("Deleted",204);

        }else{
            //If value Not Found


            return response('No Data Found',404);
        }
    }
}
