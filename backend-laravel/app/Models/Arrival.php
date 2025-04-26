<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $date
 * @property string $vehicle_registration
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property Tier $tier
 * @property Material[] $materials
 */
class Arrival extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'arrival';

    /**
     * Activate Timestamps.
     * 
     * @var boolean
     */
    public $timestamps = true;
    /**
     * Fillable db columns.
     * @var array
     */
    protected $fillable = ['date', 'vehicule_registration', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];


    /**
     * Set Active true by default.
     * @var array
     */
    protected $attributes = ['active' => true];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function materials()
    {
        return $this->hasMany('App\Models\Material', 'id_arrival');
    }

    public function tier()
    {
        return $this->belongsTo('App\Models\Tier', 'id_tier');
    }
}
