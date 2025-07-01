<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_tier
 * @property string $order_number
 * @property string $order_date
 * @property string $requested_delivery_date
 * @property string $actual_delivery_date
 * @property string $status
 * @property string $priority
 * @property string $notes
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property Tier $tier
 * @property FabricationOrderDetail[] $fabricationOrderDetails
 */
class FabricationOrder extends Model
{


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
    protected $fillable = ['id_tier', 'order_number', 'order_date', 'requested_delivery_date', 'actual_delivery_date', 'status', 'priority', 'notes', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tier()
    {
        return $this->belongsTo('App\Models\Tier', 'id_tier');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fabricationOrderDetails()
    {
        return $this->hasMany('App\Models\FabricationOrderDetails', 'id_fabrication_order');
    }
}
