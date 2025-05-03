<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $time_expected
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property float $qty_needed
 * @property ProductOperation[] $productOperations
 */
class OperationDefinition extends Model
{
    /**
     * @var array
     */
    protected $table = "operation_definitions";
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

    protected $fillable = ['name', 'time_expected', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active', 'qty_needed'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productOperations()
    {
        return $this->hasMany('App\Models\ProductOperation', 'id_operations');
    }
}
