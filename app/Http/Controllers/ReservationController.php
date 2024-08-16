<?php

namespace App\Http\Controllers;

use App\DomainData\CustomerDto;
use App\DomainData\ReservationDto;
use App\Services\ReservationService;
use App\Traits\Validators;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    use Validators, ReservationDto;

    public function __construct(private readonly ReservationService $reservationService)
    {
    }

    public function getReservations(array $request, \stdClass &$output): void
    {
        $validator = Validator::make($request, [
            'page' => ['required', 'min:1', 'integer'],
            'page_size' => ['required', 'min:1', 'integer']
        ]);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        if (!isset($request['related_objects']))
            $request['related_objects'] = [];


        $this->reservationService->getAll($request, $output);

    }

    /**
     * @throws ValidationException
     */
    public function createReservation(array $request, \stdClass &$output): void
    {
        $rules = $this->getRules(['from_time', 'to_time', 'table_id', 'number_of_guests', 'customer_id']);

        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->reservationService->create($request, $output);
    }

    public function checkInReservation(array $request, \stdClass &$output): void
    {
        $rules['id'] = ['required', 'exists:reservations,id'];


        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->reservationService->checkInReservation($request, $output);
    }

    public function checkOutReservation(array $request, \stdClass &$output): void
    {
        $rules['id'] = ['required', 'exists:reservations,id'];

        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->reservationService->checkOutReservation($request, $output);
    }

    /**
     * @throws ValidationException
     */

    /**
     * @throws ValidationException
     */
    public function deleteReservations(array $request, \stdClass &$output): void
    {

        $validator = Validator::make($request, [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:reservations,id'],
        ]);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->reservationService->delete($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function getReservationById(array $request, \stdClass &$output): void
    {
        $validator = Validator::make($request, [
            'id' => ['required'],
            'related_objects' => ['nullable', 'array'],
            'related_objects.*' => ['in:orders']
        ]);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        if (!isset($request['related_objects']))
            $request['related_objects'] = [];

        if (!isset($request['related_objects_count']))
            $request['related_objects_count'] = [];

        $this->reservationService->getById($request, $output);

    }
}
