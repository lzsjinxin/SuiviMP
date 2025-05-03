<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_product
 * @property integer $id_operations
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property integer $operation_order
 * @property OperationDefinition $operationDefinition
 * @property Product $product
 * @property Operation[] $operations
 */
class ProductOperation extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id_product', 'id_operations', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active', 'operation_order'];


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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function operationDefinition()
    {
        return $this->belongsTo('App\Models\OperationDefinition', 'id_operations');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'id_product');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operations()
    {
        return $this->hasMany('App\Models\Operation', 'id_product_operations');
    }
}
