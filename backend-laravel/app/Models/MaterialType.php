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
}
