<?php

namespace App\Models;

use App\DomainData\ChargeTypeDto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChargeType extends Model
{
    use HasFactory,ChargeTypeDto,SoftDeletes;

    protected $fillable=[];

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }
}
