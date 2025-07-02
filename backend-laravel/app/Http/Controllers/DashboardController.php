<?php

namespace App\Http\Controllers;

use App\Models\{
    Department,
    FabricationOrder,
    Material,
    MaterialMovementHistory,
    Operation,
    OperationDefinition,
    ProductOrders,
    User
};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /* ----------------------------------------------------------------- *
     *  Engineering
     * ----------------------------------------------------------------- */
    public function engineering(): JsonResponse
    {
        // ECN table not present → use FabricationOrder in "Created" status
        $openEcns = FabricationOrder::where('status', 'Created')->count();

        // "CAD load" → % of products that have at least 1 operation assigned
        $totalProd = DB::table('product')->count();
        $withOps   = DB::table('product_operations')
            ->distinct('id_product')
            ->count('id_product');
        $cadLoad   = $totalProd ? round($withOps / $totalProd * 100, 1) : 0;

        // Simple bar-chart : products per series
        $chart = DB::table('product_series as s')
            ->selectRaw('s.series as label, count(p.id) as qty')
            ->leftJoin('product as p', 'p.id_series', '=', 's.id')
            ->groupBy('s.series')
            ->orderBy('qty','desc')
            ->take(5)
            ->get();

        return response()->json([
            'open_ecns'   => $openEcns,
            'cad_load'    => $cadLoad,
            'chartOpt'    => [
                'chart'  => ['toolbar' => ['show'=>false]],
                'xaxis'  => ['categories' => $chart->pluck('label')],
                'dataLabels' => ['enabled'=>false],
            ],
            'chartSeries' => [[ 'name'=>'Produits', 'data'=> $chart->pluck('qty') ]],
        ]);
    }

    /* ----------------------------------------------------------------- *
     *  Production
     * ----------------------------------------------------------------- */
    public function production(): JsonResponse
    {
        // WIP = operations not finished
        $wipTotal = Operation::whereNull('end')->count();

        // On-time = product_orders completed ≤ requested_delivery_date
        $finished = ProductOrders::whereNotNull('completion_date')
            ->with('fabricationOrder:id,requested_delivery_date')
            ->get(['id','completion_date','id_fabrication_orders']);

        $ontime = $finished->count()
            ? round(
                $finished->filter(fn($po) =>
                    $po->completion_date <= $po->fabricationOrder?->requested_delivery_date
                )->count() / $finished->count() * 100 , 1)
            : null;

        // Next critical op = first operation_definition that still has open rows
        $nextOpId = Operation::whereNull('end')
            ->orderBy('start')
            ->value('id_product_operations');   // bridge row
        $nextOp   = $nextOpId
            ? DB::table('product_operations as po')
                ->join('operation_definitions as d','d.id','=','po.id_operations')
                ->where('po.id',$nextOpId)
                ->value('d.name')
            : null;

        // Heatmap : WIP by operation_definition vs hour
        $heat = Operation::selectRaw(
            'd.name, EXTRACT(hour FROM start) as hr, count(*) as qty'
        )
            ->join('product_operations as po','po.id','=','operations.id_product_operations')
            ->join('operation_definitions as d','d.id','=','po.id_operations')
            ->whereNull('end')
            ->groupBy('d.name','hr')
            ->get();

        $series = $heat->groupBy('name')->map(function($rows){
            $pts = array_fill(0,24,0);
            foreach($rows as $r) $pts[$r->hr] = $r->qty;
            return ['name'=>$rows[0]->name, 'data'=>$pts];
        })->values();

        return response()->json([
            'wip_total'  => $wipTotal,
            'ontime'     => $ontime,
            'next_op'    => $nextOp,
            'heatOpt'    => [
                'chart'=>['toolbar'=>['show'=>false]],
                'xaxis'=>['categories'=>range(0,23)],
            ],
            'heatSeries' => $series,
        ]);
    }

    /* ----------------------------------------------------------------- *
     *  Quality Control
     * ----------------------------------------------------------------- */
    public function qc(): JsonResponse
    {
        // Waiting = product_orders whose last op is QC and not finished
        $qcDefIds = OperationDefinition::where('name','ILIKE','%quality%')->pluck('id');

        $waiting = Operation::whereIn('id_product_operations', function($q) use ($qcDefIds) {
            $q->select('id')
                ->from('product_operations')
                ->whereIn('id_operations',$qcDefIds);
        })
            ->whereNull('end')
            ->count();

        // FPY = finished QC operations count / total QC operations
        $qcOps     = Operation::whereIn('id_product_operations', function($q) use ($qcDefIds) {
            $q->select('id')
                ->from('product_operations')
                ->whereIn('id_operations',$qcDefIds);
        });
        $totalQc   = $qcOps->count();
        $passedQc  = $qcOps->whereNotNull('end')->count();
        $fpy       = $totalQc ? round($passedQc/$totalQc*100,1) : null;

        // Pareto sample: top 5 material types used in failed QC (not recorded→null)
        return response()->json([
            'waiting'      => $waiting,
            'fpy'          => $fpy,
            'paretoOpt'    => null,
            'paretoSeries' => null,
        ]);
    }

    /* ----------------------------------------------------------------- *
     *  Logistics
     * ----------------------------------------------------------------- */
    public function logistics(): JsonResponse
    {
        /* ------------------------------------------------------------------
         * 1) Basic counts already shown
         * ---------------------------------------------------------------- */
        $outgoing = Material::where('id_status', 6)->count();                    // Transfered
        $incoming = DB::table('arrival')->where('status','In Transit')->count(); // Arrivals en route

        /* ------------------------------------------------------------------
         * 2) Location utilisation
         * ---------------------------------------------------------------- */
        $locTotal     = DB::table('location')->count();
        $locWithStock = Material::select('id_current_location')
            ->whereNotNull('id_current_location')
            ->distinct()->count();

        $freePct   = $locTotal ? round(($locTotal - $locWithStock) / $locTotal * 100,1) : null;
        $fillRate  = $locTotal ? round($locWithStock / $locTotal * 100,1) : null;

        /* ------------------------------------------------------------------
         * 3) Stock coverage (days)
         *    Needs a daily usage rate; if your schema has none we fallback =1
         * ---------------------------------------------------------------- */
        $coverageRow = DB::table('material as m')
            ->join('material_type as t','t.id','=','m.id_type')
            ->selectRaw('SUM(m.qty) as qty_total, SUM(coalesce(1)) as usage_total')
            ->first();

        $coverageDays = $coverageRow && $coverageRow->usage_total > 0
            ? round($coverageRow->qty_total / $coverageRow->usage_total, 1)
            : null;

        /* ------------------------------------------------------------------
         * 4) Lots expiring in ≤ 30 j
         * ---------------------------------------------------------------- */
        $expiringLots = DB::table('material_batches')
            ->whereDate('expiry_date','<=', now()->addDays(30))
            ->count();

        /* -----------------------------------------------------------
    * 6) Matériaux par emplacement
    * ----------------------------------------------------------- */
        $locationCounts = DB::table('location as l')
            ->leftJoin('material as m', 'm.id_current_location', '=', 'l.id')
            ->select('l.name', DB::raw('COUNT(m.id) as qty'))
            ->groupBy('l.name')
            ->orderBy('qty','desc')
            ->get();          // →  [ { name:"Rack A1", qty: 12 }, … ]

        return response()->json([
            'outgoing'      => $outgoing,
            'incoming'      => $incoming,
            'fill_rate'     => $fillRate,
            'free_pct'      => $freePct,
            'locationSeries' => $locationCounts->pluck('qty'),
            'locationLabels' => $locationCounts->pluck('name'),
            'coverage_days' => $coverageDays,
            'expiring_lots' => $expiringLots,
        ]);
    }

    /* ----------------------------------------------------------------- *
     *  Administration
     * ----------------------------------------------------------------- */
    public function admin(): JsonResponse
    {
        return response()->json([
            'users'       => User::where('active',true)->count(),
            // Late tasks table does not exist yet
            'late_tasks'  => null,
        ]);
    }
}
