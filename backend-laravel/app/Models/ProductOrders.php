<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_fabrication_orders
 * @property string $serial_number
 * @property string $start_date
 * @property string $completion_date
 * @property string $status
 * @property integer $useradd
 * @property integer $userupdate
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $active
 * @property FabricationOrderDetail $fabricationOrderDetail
 * @property Operation[] $operations
 */
class ProductOrders extends Model
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
    protected $fillable = ['id_fabrication_orders', 'serial_number', 'start_date', 'completion_date', 'status', 'useradd', 'userupdate', 'created_at', 'updated_at', 'active'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fabricationOrderDetail()
    {
        return $this->belongsTo('App\Models\FabricationOrderDetail', 'id_fabrication_orders');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operations()
    {
        return $this->hasMany('App\Models\Operation', 'id_product_order');
    }

    /** next SN → P-YYMM-#### */
    public static function nextSerial(): string
    {
        $prefix = 'P' . now()->format('ym');
        $last   = static::where('serial_number', 'like', "$prefix%")
            ->orderByDesc('serial_number')
            ->value('serial_number');
        $seq = $last ? intval(substr($last, -4)) + 1 : 1;
        return sprintf('%s-%04d', $prefix, $seq);
    }
}
