<?php

namespace App\Http\Controllers;

use App\DomainData\OrderDetailDto;
use App\Services\OrderDetailService;

class OrderDetailController extends Controller
{
    use OrderDetailDto;

    public function __construct(private readonly OrderDetailService $orderDetailService)
    {
    }
}
