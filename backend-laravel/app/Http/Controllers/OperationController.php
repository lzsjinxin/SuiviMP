<?php
// app/Http/Controllers/OperationController.php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialMovementHistory;
use App\Models\Operation;
use App\Models\ProductOperation;
use App\Models\ProductOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OperationController extends Controller
{
    /** POST /ops/declare/start
     *  Body: { po_id, op_def_id, mat_id, qty_used }
     */
    public function start(Request $r)
    {
        $data = $r->validate([
            'po_id'     => ['required', 'exists:product_orders,id'],
            'op_def_id' => ['required', 'exists:operation_definitions,id'],
            'mat_id'    => ['required', 'exists:material,id'],
            'qty_used'  => ['required', 'numeric', 'min:0.001'],
        ]);

        /* -----------------------------------------------------------
         * 1) Resolve the product_operations row for this PO + op_def
         * ----------------------------------------------------------- */
        $po   = ProductOrders::findOrFail($data['po_id']);
        $prod = $po->fabricationOrderDetails->id_product;   // product ID

        $bridge = ProductOperation::where('id_product',    $prod)
            ->where('id_operations', $data['op_def_id'])
            ->firstOrFail(); // id_product_operations

        /* -----------------------------------------------------------
         * 2) Avoid duplicate “start” if not yet finished
         * ----------------------------------------------------------- */
        $exists = Operation::where('id_product_order', $data['po_id'])
            ->where('id_product_operations', $bridge->id)
            ->whereNull('end')
            ->first();

        if ($exists) {
            return response()->json(['message' => 'Operation deja commencé'], 409);
        }

        $mat = Material::findOrFail($data['mat_id']);

        if(!$mat->active){
            return response()->json(['message' => 'cette Matière Premiere n\'existe pas'], 409);
        }

        if($mat->qty < $data['qty_used']){
            return response()->json(['message' => 'Qte de Mat insuffisante'], 409);
        }else{

            MaterialMovementHistory::create([
                'id_material' => $mat->id,
                'id_movement_type' => 2,
                'id_location_from' =>$mat->id_current_location,
                'id_location_to' => 7,
                'mouvement_date' =>date('Y-m-d H:i:s'),
                'id_user'=> Auth::guard('sanctum')->id()
            ]);
            if($mat->qty == $data['qty_used']){
                $mat->update([
                    'id_status'       => 3,
                    'id_current_location' => 7,
                    'qty' =>0,
                    'userupdate'=> Auth::guard('sanctum')->id(),
                ]);
            }else{
                $mat->update([
                    'id_status'       => 2,
                    'id_current_location' => 7,
                    'qty' =>$mat->qty - $data['qty_used'],
                    'userupdate'=> Auth::guard('sanctum')->id(),
                ]);
            }
        }


        /* -----------------------------------------------------------
         * 3) Create the operation row
         * ----------------------------------------------------------- */
        Operation::create([
            'id_product_order'      => $data['po_id'],
            'id_material'           => $data['mat_id'],
            'id_product_operations' => $bridge->id,
            'qty_used'              => $data['qty_used'],
            'start'                 => now(),
            'useradd'               => Auth::guard('sanctum')->id(),
        ]);

        if(is_null($po->start_date)){
            $po->update([
                'start_date' => now(),
                'userupdate' => Auth::guard('sanctum')->id(),
            ]);
        }
        elseif ($po->status == "Planned") {
            $po->update([
                'status' => "Started",
                'userupdate' => Auth::guard('sanctum')->id(),
            ]);
        }




        return response()->json(['status' => 'started'], 201);
    }

    /** POST /ops/declare/finish
     *  Body: { po_id, op_def_id }
     */
    public function finish(Request $r)
    {
        $data = $r->validate([
            'po_id'     => ['required', 'exists:product_orders,id'],
            'op_def_id' => ['required', 'exists:operation_definitions,id'],
        ]);

        /* resolve the same bridge id as above */
        $po   = ProductOrders::findOrFail($data['po_id']);
        $prod = $po->fabricationOrderDetails->id_product;


        $bridgeId = ProductOperation::where('id_product',    $prod)
            ->where('id_operations', $data['op_def_id'])
            ->value('id');                           // int|NULL

        if (! $bridgeId) {
            return response()->json(['message' => 'Bridge not found'], 404);
        }

        /* update the running operation */
        $op = Operation::where('id_product_order', $data['po_id'])
            ->where('id_product_operations', $bridgeId)
            ->whereNull('end')
            ->firstOrFail();

        $mat =  Material::findOrFail($op->id_material);
        $movement_history = MaterialMovementHistory::where('id_movement_type', 2)
            ->where('id_material', $mat->id)->orderBy('id', 'desc')->firstOrFail();

        /* -----------------------------------------------------------------
         * 3) Close the running operation
         * ----------------------------------------------------------------*/
        DB::transaction(function () use ($op, $mat, $movement_history, $po) {

            // 3-A  : material back to original location
            MaterialMovementHistory::create([
                'id_material'       => $mat->id,
                'id_movement_type'  => 1,                           // Retour
                'id_location_from'  => $mat->id_current_location,
                'id_location_to'    => $movement_history->id_location_from,
                'mouvement_date'    => now(),
                'id_user'           => Auth::guard('sanctum')->id(),
            ]);

            $mat->update([
                'id_current_location' => $movement_history->id_location_from,
                'id_status'           => 1,                        // Available
                'userupdate'          => Auth::guard('sanctum')->id(),
            ]);

            // 3-B  : mark THIS operation done
            $op->update([
                'end'        => now(),
                'userupdate' => Auth::guard('sanctum')->id(),
            ]);

            /* -------------------------------------------------------------
             * 4)  Product-order auto-finish
             * ------------------------------------------------------------ */
            $stillOpen = Operation::where('id_product_order', $po->id)
                ->whereNull('end')
                ->exists();

            if (! $stillOpen) {
                $po->update([
                    'completion_date' => now(),
                    'status'          => 'Finished',
                    'userupdate'      => Auth::guard('sanctum')->id(),
                ]);
            }

            /* -------------------------------------------------------------
             * 5)  Fabrication-order auto-finish
             * ------------------------------------------------------------ */
            $foId      = $po->id_fabrication_orders;
            $unfinished = \App\Models\ProductOrders::where('id_fabrication_orders', $foId)
                ->where('status', '!=', 'Finished')
                ->exists();

            if (! $unfinished) {
                \App\Models\FabricationOrders::where('id', $foId)->update([
                    'actual_delivery_date' => now(),
                    'status'               => 'Finished',
                    'userupdate'           => Auth::guard('sanctum')->id(),
                ]);
            }
        });   // end DB transaction

        return response()->json(['status' => 'finished']);

    }

    public function check(int $po_id, int $op_def_id)
    {
        // 1) resolve bridge row id_product_operations
        $po   = ProductOrders::findOrFail($po_id);
        $prod = $po->fabricationOrderDetails->id_product;

        $bridgeId = ProductOperation::where('id_product',    $prod)
            ->where('id_operations', $op_def_id)
            ->value('id');                    // may be null

        if (! $bridgeId) {
            return response()->json(['done' => false]);   // op not in flow
        }

        // 2) check if a finished row exists
        $done = Operation::where('id_product_order',      $po_id)
            ->where('id_product_operations', $bridgeId)
            ->whereNotNull('end')            // finished
            ->exists();

        return response()->json(['done' => $done]);
    }
}
