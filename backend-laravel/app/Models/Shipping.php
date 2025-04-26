<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_product
 * @property integer $id_tier
 * @property string $shipping_date
 * @property string $arrival_date
 * @property string $incoterm
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property Product $product
 */
class Shipping extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'shipping';

    /**
     * @var array
     */
    protected $fillable = ['id_product', 'shipping_date', 'arrival_date', 'incoterm', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active','id_tier'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'id_product');
    }
}
