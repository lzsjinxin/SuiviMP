<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_fabrication_order
 * @property integer $id_product
 * @property integer $quantity
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property ProductOrder[] $productOrders
 * @property FabricationOrder $fabricationOrder
 * @property Product $product
 */
class FabricationOrderDetails extends Model
{

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
    protected $fillable = ['id_fabrication_order', 'id_product', 'quantity', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productOrders()
    {
        return $this->hasMany('App\Models\ProductOrder', 'id_fabrication_orders');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fabricationOrder()
    {
        return $this->belongsTo('App\Models\FabricationOrder', 'id_fabrication_order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'id_product');
    }
}
