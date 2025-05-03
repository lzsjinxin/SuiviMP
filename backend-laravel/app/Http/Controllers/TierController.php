<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tier;
class TierController extends Controller
{
    public function getAll(){
        return Tier::where('active', true)->orderBy('id','DESC')->get();
    }

    public function getbyId($id){
    }

    public function create(Request $request){          
    }

    public function update(Request $request, $id){
    }

    public function logicalDelete($id){

        }

}
