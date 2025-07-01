<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MaterialSheetController extends Controller
{
     /** GET /api/materials/{id}/sheet */
    public function download(int $id)
    {
        /* 1. Pull material + its movement history */
        $mat = Material::with([
            // 1️⃣ Load the movement histories, ordered by date …
            'materialMovementHistories' => function ($query) {
                $query->orderBy('mouvement_date', 'asc')   // or 'movement_date' if that’s the real column
                // 2️⃣ … and within each history record load its active location only
                ->with(['locationFrom','locationTo','movementType']);
            },

            // 3️⃣ Also load the material’s own location
            'location',
            'unit',
            'arrival',
            'materialBatch',
            'materialStatus',
            'materialType'
        ])->findOrFail($id);

        /* 2. Render Blade view to HTML */
        $html = view('pdf.material_sheet', compact('mat'))->render();

        /* 3. Convert to PDF */
        return Pdf::loadHtml($html)
                  ->setPaper('A4')
                  ->download("MAT-{$mat->id}.pdf");
    }
}
