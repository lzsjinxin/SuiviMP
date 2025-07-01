<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $type
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property Material[] $materials
 * @property OperationDefinition[] $operationDefinition
 */
class MaterialType extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'material_type';

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
    protected $fillable = ['type', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function materials()
    {
        return $this->hasMany('App\Models\Material', 'id_type');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operationDefinition(){
        return $this->hasMany('App\Models\OperationDefinition','id_material_type');
    }
}
