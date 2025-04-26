<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_supplier
 * @property integer $id_stock
 * @property integer $id_status
 * @property integer $id_arrival
 * @property integer $id_type
 * @property integer $id_unit
 * @property string $num
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property string $batch_num
 * @property ProductMaterial[] $productMaterials
 * @property Tier $tier
 * @property MaterialStatus $materialStatus
 * @property Arrival $arrival
 * @property MaterialType $materialType
 * @property Unit $unit
 * @property Stock $stock
 * @property Operation[] $operations
 */
class Material extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'material';

    /**
     * @var array
     */
    protected $fillable = ['id_supplier', 'id_stock', 'id_status', 'id_arrival', 'id_type', 'id_unit', 'num', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active','batch_num'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productMaterials()
    {
        return $this->hasMany('App\Models\ProductMaterial', 'id_material');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tier()
    {
        return $this->belongsTo('App\Models\Tier', 'id_supplier');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function materialStatus()
    {
        return $this->belongsTo('App\Models\MaterialStatus', 'id_status');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function arrival()
    {
        return $this->belongsTo('App\Models\Arrival', 'id_arrival');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function materialType()
    {
        return $this->belongsTo('App\Models\MaterialType', 'id_type');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'id_unit');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock()
    {
        return $this->belongsTo('App\Models\Stock', 'id_stock');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operations()
    {
        return $this->hasMany('App\Models\Operation', 'id_material');
    }
}
