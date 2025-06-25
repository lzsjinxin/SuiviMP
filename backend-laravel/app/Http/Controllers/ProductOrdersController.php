<?php

namespace App\Http\Controllers;

use App\Models\FabricationOrderDetails;
use App\Models\ProductOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductOrdersController extends Controller
{
    /**
     * POST /fabricationOrderDetails/{id}/generate
     * Creates all missing product_instances for this detail line.
     * Response: { created: 5, serials: [ … ] }
     */
    public function generate(int $id)
    {
        $detail = FabricationOrderDetails::with('product')->find($id);
        if (! $detail || ! $detail->active) {
            return response('No Data Found', 404);
        }

        $already = $detail->productOrders()->count();
        $missing = $detail->quantity - $already;

        if ($missing <= 0) {
            return response()->json(['message' => 'Aucune unité manquante'], 200);
        }

        $serials = [];
        DB::transaction(function () use ($detail, $missing, &$serials) {
            for ($seq = 1; $seq <= $missing; $seq++) {
                $serial = ProductOrders::nextSerial();
                ProductOrders::create([
                    'id_fabrication_orders' => $detail->id,
                    'serial_number'         => $serial,
                    'sequence'              => $seq,
                ]);
                $serials[] = $serial;
            }
        });

        return response()->json([
            'created' => $missing,
            'serials' => $serials,
        ], 201);
    }

}
