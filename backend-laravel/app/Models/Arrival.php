<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_tier
 * @property string $date
 * @property string $vehicule_registration
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property Material[] $materials
 * @property Tier $tier
 * @property string $status
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
     * @var array
     */

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

    protected $fillable = ['id_tier', 'date', 'vehicule_registration', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active','status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function materials()
    {
        return $this->hasMany('App\Models\Material', 'id_arrival');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tier()
    {
        return $this->belongsTo('App\Models\Tier', 'id_tier');
    }
}
//skjld