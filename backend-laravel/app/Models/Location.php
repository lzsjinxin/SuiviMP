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
 * @property MaterialMovementHistory[] $materialMovementHistories
 * @property MaterialMovementHistory[] $materialMovementHistories
 * @property Material[] $materials
 */
class Location extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'location';

    /**
     * @var array
     */
    protected $fillable = ['name', 'adresse', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];


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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function materials()
    {
        return $this->hasMany('App\Models\Material', 'id_current_location');
    }
}
