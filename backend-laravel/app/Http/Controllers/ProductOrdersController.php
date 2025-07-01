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

    public function show(int $id)
    {
        /* ----------------------------------------------------------
         * 1) Load the concrete unit + its product + flow + done ops
         * --------------------------------------------------------- */
        $po = ProductOrders::with([
            'fabricationOrderDetails.product.productOperations.operationDefinition',
            'operations'  // rows in operations table
        ])->findOrFail($id);

        $product = $po->fabricationOrderDetails->product;

        /* Full ordered flow */
        $flow = $product->productOperations
            ->sortBy('operation_order')
            ->map(fn ($row) => [
                'id'       => $row->operationDefinition->id,
                'code'     => $row->operationDefinition->code ?? '',
                'name'     => $row->operationDefinition->name,
                'sequence' => $row->operation_order,
            ])->values();


//        /* IDs of finished ops */
        $doneIds = $po->operations
            ->whereNotNull('end')
            ->pluck('id_product_operations')           // bridge IDs
            ->map(function ($bridgeId) use ($product) {
                $row = $product->productOperations->firstWhere('id', $bridgeId);
                return $row?->operationDefinition->id;
            })
            ->filter()
            ->values();

        /* ----------------------------------------------------------
         * 2) Return compact JSON
         * --------------------------------------------------------- */
        return response()->json([
            'id'              => $po->id,
            'serial_number'   => $po->serial_number,
            'status'          => $po->status ?? 'in_progress',
            'operation_flow'  => $flow,
            'operations_done' => $doneIds,
        ]);
    }

}
