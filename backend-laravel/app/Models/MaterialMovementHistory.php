<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_material
 * @property integer $id_movement_type
 * @property integer $id_location_from
 * @property integer $id_location_to
 * @property string $mouvement_date
 * @property integer $id_user
 * @property boolean $active
 * @property Location $location
 * @property Location $location
 * @property Material $material
 * @property MovementType $movementType
 */
class MaterialMovementHistory extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'material_movement_history';


    /**
     * Set Active true by default.
     * @var array
     */
    protected $attributes = ['active' => true];


    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['id_material', 'id_movement_type', 'id_location_from', 'id_location_to', 'mouvement_date', 'id_user', 'active'];


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
    public function movementType()
    {
        return $this->belongsTo('App\Models\MovementType', 'id_movement_type');
    }
}
