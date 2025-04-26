<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $title
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property Material[] $materials
 */
class Unit extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'unit';

    /**
     * @var array
     */
    protected $fillable = ['title', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function materials()
    {
        return $this->hasMany('App\Models\Material', 'id_unit');
    }
}
