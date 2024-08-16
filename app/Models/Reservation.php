<?php

namespace App\Models;

use App\DomainData\ReservationDto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory, ReservationDto;

    public const RESERVATION_STATUS_UPCOMING = 'upcoming';
    public const RESERVATION_STATUS_CANCELED = 'canceled';
    public const RESERVATION_STATUS_CHECKED_IN = 'checked_in';
    public const RESERVATION_STATUS_CHECKED_OUT = 'checked_out';


    protected $fillable = [];

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function table(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

}
