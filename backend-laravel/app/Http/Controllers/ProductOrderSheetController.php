<?php

namespace App\Http\Controllers;

use App\Models\ProductOrders;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductOrderSheetController extends Controller
{
    public function download(int $detailId)
    {
        $rels = [
            'fabricationOrderDetails.product.productOperations.operationDefinition',
            'operations.material.materialBatch',
            'operations.userAdd',
        ];

        // 1) Fetch ALL product orders for this fabrication detail
        $pos = ProductOrders::query()
            ->with($rels)
            ->where('id_fabrication_orders', $detailId)
            ->orderBy('id')                 // stable order (or created_at if you have it)
            ->get();

        if ($pos->isEmpty()) {
            abort(404, 'No product orders found for this fabrication detail.');
        }

        // 2) Resolve the product (shared by all POs via the same fabricationOrderDetails)
        $product = optional($pos->first()->fabricationOrderDetails)->product;
        if (!$product) {
            return response('Product definition missing', 404);
        }

        // Fetch & sort the product's operation definitions once
        $opsDef = $product->productOperations->sortBy('operation_order');

        // 3) Build per‑PO rows (planned vs actual)
        $items = $pos->map(function ($po) use ($opsDef) {
            $rows = $opsDef->map(function ($bridge) use ($po) {
                $opRow = $po->operations->firstWhere('id_product_operations', $bridge->id);
                return [
                    'def'   => $bridge->operationDefinition,
                    'order' => $bridge->operation_order,
                    'op'    => $opRow,  // may be null (not executed yet)
                ];
            });

            return compact('po', 'rows');
        });

        // 4) Render one HTML containing all POs (multi‑page)
        $html = view('pdf.product_order_sheet_multi', [
            'product' => $product,
            'items'   => $items,
        ])->render();

        return Pdf::loadHtml($html)
            ->setPaper('A4')
            ->download("PO-detail-{$detailId}.pdf");
    }

}
