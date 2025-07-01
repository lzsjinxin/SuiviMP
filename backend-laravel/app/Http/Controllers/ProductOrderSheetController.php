<?php

namespace App\Http\Controllers;

use App\Models\ProductOrders;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductOrderSheetController extends Controller
{
    public function download(int $id)
    {
        /* 1) Eager-load everything we need */
        $po = ProductOrders::with([
            'fabricationOrderDetails.product.productOperations.operationDefinition',
            'operations.material.materialBatch',   // code, batch, etc.
            'operations.userAdd',                  // relation on Operation → User
        ])->findOrFail($id);

        $product = $po->fabricationOrderDetails?->product;
        if (!$product) {
            return response('Product definition missing', 404);
        }

        /* 2) Build rows: definition + (optional) actual operation row */
        $rows = $product->productOperations
            ->sortBy('operation_order')
            ->map(function ($bridge) use ($po) {
                $opRow = $po->operations->firstWhere('id_product_operations', $bridge->id);
                return [
                    'def'       => $bridge->operationDefinition,
                    'order'     => $bridge->operation_order,
                    'op'        => $opRow,                               // may be null
                ];
            });

        /* 3) Pass to Blade and render PDF */
        $html = view('pdf.product_order_sheet', [
            'po'      => $po,
            'product' => $product,
            'rows'    => $rows,
        ])->render();

        return Pdf::loadHtml($html)
            ->setPaper('A4')
            ->download("PO-{$po->serial_number}.pdf");
    }
}
