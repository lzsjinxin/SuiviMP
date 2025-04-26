<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $adresse
 * @property string $city
 * @property string $country
 * @property string $ice
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property string $type
 * @property Material[] $materials
 * @property ProductSeries[] $productSeries
 */
class Tier extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'tier';

    /**
     * @var array
     */
    protected $fillable = ['name', 'adresse', 'city', 'country', 'ice', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active','type'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function materials()
    {
        return $this->hasMany('App\Models\Material', 'id_supplier');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productSeries()
    {
        return $this->hasMany('App\Models\ProductSeries', 'id_tier');
    }
}
