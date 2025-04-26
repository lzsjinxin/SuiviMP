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
 * @property Product $product
 * @property Material $material
 * @property ProductOperations $productOperation
 */
class Operation extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id_product', 'id_material', 'id_product_operations', 'start', 'end', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'id_product');
    }

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
    public function productOperation()
    {
        return $this->belongsTo('App\Models\ProductOperation', 'id_product_operations', 'id_product_operations');
    }
}
