<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_tier
 * @property string $series
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property Product[] $products
 * @property Tier $tier
 */
class ProductSeries extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id_tier', 'series', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('App\Models\Product', 'id_series');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tier()
    {
        return $this->belongsTo('App\Models\Tier', 'id_tier');
    }
}
