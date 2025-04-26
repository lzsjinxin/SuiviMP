<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id_product_operations
 * @property integer $id_product
 * @property integer $id_operations
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property Product $product
 * @property OperationDefinitions $operationDefinition
 * @property Operation[] $operations
 */
class ProductOperations extends Model
{
    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'id_product_operations';

    /**
     * @var array
     */
    protected $fillable = ['id_product', 'id_operations', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

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
    public function operationDefinition()
    {
        return $this->belongsTo('App\Models\OperationDefinition', 'id_operations', 'id_operation');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operations()
    {
        return $this->hasMany('App\Models\Operation', 'id_product_operations', 'id_product_operations');
    }
}
