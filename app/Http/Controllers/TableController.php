<?php

namespace App\Http\Controllers;

use App\DomainData\ReservationDto;
use App\DomainData\TableDto;
use App\Services\TableService;
use App\Traits\Validators;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    use TableDto, Validators, ReservationDto {
        ReservationDto::getRules insteadof TableDto;

        ReservationDto::getRules as public reservationGetRules;
    }

    public function __construct(private readonly TableService $tableService)
    {
    }

    public function getTablesAvailableForReservation(array $request, \stdClass &$output): void
    {

        $rules = $this->reservationGetRules(['from_time', 'to_time', 'number_of_guests']);

        $validator = Validator::make($request, $rules);
        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        if (!isset($request['related_objects']))
            $request['related_objects'] = [];


        $this->tableService->getTablesAvailableForReservation($request, $output);

    }
}
