<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property User[] $users
 */
class Department extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'department';

    /**
     * @var array
     */
    protected $fillable = ['name', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\Models\User', 'id_dept');
    }
}
