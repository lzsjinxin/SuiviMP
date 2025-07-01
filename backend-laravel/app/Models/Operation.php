<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_product
 * @property integer $id_material
 * @property integer $id_product_operations
 * @property string $start
 * @property string $end
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property float $qty_used
 * @property Material $material
 * @property Product $product
 * @property ProductOperation $productOperation
 */
class Operation extends Model
{

    protected $table="operations";

    /**
     * Activate Timestamps.
     *
     * @var boolean
     */
    public $timestamps = true;
    /**
     * Set Active true by default.
     * @var array
     */
    protected $attributes = ['active' => true];


    /**
     * @var array
     */
    protected $fillable = ['id_product_order', 'id_material', 'id_product_operations', 'start', 'end', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active', 'qty_used'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function material()
    {
        return $this->belongsTo('App\Models\Material', 'id_material');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productOrders()
    {
        return $this->belongsTo('App\Models\ProductOrders', 'id_product_order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productOperation()
    {
        return $this->belongsTo('App\Models\ProductOperation', 'id_product_operations');
    }

    public function userAdd()   { return $this->belongsTo(User::class,'useradd'); }
}
