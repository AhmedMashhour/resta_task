<?php

namespace App\Models;

use App\DomainData\OrderDetailDto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use HasFactory,OrderDetailDto,SoftDeletes;

    public const ORDER_DETAIL_STATUS_PENDING = 'pending';
    public const ORDER_DETAIL_STATUS_WASTE = 'waste';
    public const ORDER_DETAIL_STATUS_CANCELED = 'canceled';
    public const ORDER_DETAIL_STATUS_PAID = 'paid';
    protected $fillable = [];

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function meal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
