<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property integer $id
 * @property integer $id_dept
 * @property string $fname
 * @property string $name
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property Department $department
 */
class User extends Authenticatable
{

    use HasApiTokens;
    /**
     * @var array
     */
    protected $table = "users";

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


    protected $fillable = ['id_dept', 'fname', 'name', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active','uuid','password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'uuid'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'id_dept');
    }
}
