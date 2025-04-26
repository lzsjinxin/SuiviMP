<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $adresse
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property Material[] $materials
 */
class Stock extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'stock';

    /**
     * @var array
     */
    protected $fillable = ['name', 'adresse', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function materials()
    {
        return $this->hasMany('App\Models\Material', 'id_stock');
    }

    public function tier()
    {
        return $this->belongsTo('App\Models\Tier', 'id_tier');
    }
}
