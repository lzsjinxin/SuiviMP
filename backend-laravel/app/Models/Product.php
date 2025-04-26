<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_series
 * @property integer $id_status
 * @property string $title
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property ProductOperation[] $productOperations
 * @property ProductMaterial[] $productMaterials
 * @property Shipping[] $shippings
 * @property ProductSeries $productSeries
 * @property ProductStatus $productStatus
 * @property Operation[] $operations
 */
class Product extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'product';

    /**
     * @var array
     */
    protected $fillable = ['id_series', 'id_status', 'title', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productOperations()
    {
        return $this->hasMany('App\Models\ProductOperation', 'id_product');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productMaterials()
    {
        return $this->hasMany('App\Models\ProductMaterial', 'id_product');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippings()
    {
        return $this->hasMany('App\Models\Shipping', 'id_product');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productSeries()
    {
        return $this->belongsTo('App\Models\ProductSeries', 'id_series');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productStatus()
    {
        return $this->belongsTo('App\Models\ProductStatus', 'id_status');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operations()
    {
        return $this->hasMany('App\Models\Operation', 'id_product');
    }
}
