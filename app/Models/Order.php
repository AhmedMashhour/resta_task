<?php

namespace App\Models;

use App\DomainData\OrderDto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory,OrderDto;

    public const ORDER_STATUS_PENDING = 'pending';
    public const ORDER_STATUS_WASTE = 'waste';
    public const ORDER_STATUS_CANCELED = 'canceled';
    public const ORDER_STATUS_PAID = 'paid';

    protected $fillable = [];

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function table(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Table::class)->withTrashed();
    }


    public function reservation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Reservation::class)->withTrashed();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function chargeType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChargeType::class)->withTrashed();
    }

    public function orderDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}
