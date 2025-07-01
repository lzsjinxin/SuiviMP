<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FabricationOrder;
use App\Models\FabricationOrderDetails;
use Illuminate\Http\Request;

class FabricationOrdersController extends Controller
{

    public function getAll(){
        return FabricationOrder::with([
            'tier' => function($query) {
                $query->where('active', true);
            },
            'fabricationOrderDetails'=>function($query){
            $query->with(['product' => function($q){
                $q->where('active', true);
            }])->where('active', true);
            }])->where('active', true)->orderBy('created_at', 'DESC')->get();
    }

    public function getbyId($id)
    {
        if (! is_numeric($id)) {
            return response('No Data Found', 404);
        }

        $fo = FabricationOrder::with([
            'tier' => fn ($q) => $q->where('active', true),

            // load the product definition for every detail line
            'fabricationOrderDetails' => function ($q) {
                $q->where('active', true)
                    ->with('product:id,title');          // ← eager-load only id + title
            },
        ])
            ->where('id', $id)
            ->where('active', true)
            ->first();                                    // single row (not array)

        if (! $fo) {
            return response('No Data Found', 404);
        }

        /* expose `product_title` and clean the payload */
        $fo->fabricationOrderDetails->transform(function ($d) {
            $d->product_title = $d->product->title ?? null;
            unset($d->product);                           // optional: hide nested object
            return $d;
        });

        // alias relation name -> `details`
        $fo->setRelation('details', $fo->fabricationOrderDetails)
            ->makeHidden('fabrication_order_details');

        return response()->json($fo);
    }


    public function getLatest(){
        return FabricationOrder::where('active',true)->orderBy("id","desc")->limit(1)->get();
    }


    public function create(Request $request){

        $idTier = $request["id_tier"];
        $orderNumber = $request["order_number"];
        $orderDate = $request["order_date"];
        $requestedDate = $request["requested_date"];
        $priority = $request["priority"];
        $notes = $request["notes"];
        $products = $request["products"];
        $user = $request["user"];

          $fabricationOrder =   FabricationOrder::create([
                'id_tier' => $idTier,
                'order_number' => $orderNumber,
                'order_date' => $orderDate,
                'requested_delivery_date' => $requestedDate,
                'status'=>'Created',
                'priority' => $priority,
                'notes' => $notes,
                'useradd'=>$user
            ]);
          $fabricationOrder->save();
          $newOrderId = $fabricationOrder->id;

          foreach ($products as $product) {
              $foDetail = FabricationOrderDetails::create([
                  'id_fabrication_order'=>$newOrderId,
                  'id_product'=>$product['id'],
                  'quantity'=>$product['qty'],
                  'useradd'=>$user
              ]);
              $foDetail->save();
          }
        return response()->json($fabricationOrder,201);



    }
}
