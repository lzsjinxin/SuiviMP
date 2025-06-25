<?php

namespace App\Http\Controllers;

use App\Models\FabricationOrderDetails;
use App\Models\ProductOrders;
use Illuminate\Http\Request;

class FabricationOrderDetailsController extends Controller
{
    /**
     * Return how many concrete product instances (product_orders)
     * have been generated for a given fabrication-order detail line.
     *
     * GET /fabricationOrderDetails/{id}/count-product-orders
     * Response: { "count": 12 }
     */
    public function countProductOrders(int $id)
    {
        // Validate & fetch the detail row (active only)
        $detail = FabricationOrderDetails::where('id', $id)
            ->where('active', true)
            ->first();

        if (! $detail) {
            return response('No Data Found', 404);
        }

        // Count rows in product_orders for this detail line
        // (column name id_fabrication_orders matches your schema)
        $count = ProductOrders::where('id_fabrication_orders', $id)->count();

        return response()->json(['count' => $count]);
    }
}
