<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id_operation
 * @property string $name
 * @property string $time_expected
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property ProductOperations[] $productOperations
 */
class OperationDefinitions extends Model
{
    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'id_operation';

    /**
     * @var array
     */
    protected $fillable = ['name', 'time_expected', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productOperations()
    {
        return $this->hasMany('App\Models\ProductOperation', 'id_operations', 'id_operation');
    }
}
