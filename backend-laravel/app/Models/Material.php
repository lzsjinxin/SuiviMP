<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_current_location
 * @property integer $id_status
 * @property integer $id_arrival
 * @property integer $id_type
 * @property integer $id_unit
 * @property integer $id_batch
 * @property integer $num
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property float $qty
 * @property MaterialMovementHistory[] $materialMovementHistories
 * @property Arrival $arrival
 * @property MaterialBatch $materialBatch
 * @property MaterialStatus $materialStatus
 * @property MaterialType $materialType
 * @property Location $location
 * @property Unit $unit
 * @property Operation[] $operations
 * @property ProductMaterial[] $productMaterials
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
    protected $fillable = ['id_current_location', 'id_status', 'id_arrival', 'id_type', 'id_unit', 'id_batch', 'num', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active', 'qty'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function materialMovementHistories()
    {
        return $this->hasMany('App\Models\MaterialMovementHistory', 'id_material');
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
    public function materialBatch()
    {
        return $this->belongsTo('App\Models\MaterialBatch', 'id_batch');
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
    public function materialType()
    {
        return $this->belongsTo('App\Models\MaterialType', 'id_type');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'id_current_location');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'id_unit');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operations()
    {
        return $this->hasMany('App\Models\Operation', 'id_material');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productMaterials()
    {
        return $this->hasMany('App\Models\ProductMaterial', 'id_material');
    }
}
