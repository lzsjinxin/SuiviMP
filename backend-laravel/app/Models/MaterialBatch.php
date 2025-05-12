<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $batch_number
 * @property boolean $can_be_expired
 * @property string $expiry_date
 * @property Material[] $materials
 */
class MaterialBatch extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['batch_number', 'can_be_expired', 'expiry_date', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

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
        return $this->hasMany('App\Models\Material', 'id_batch');
    }
}
