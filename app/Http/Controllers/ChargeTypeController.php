<?php

namespace App\Http\Controllers;

use App\DomainData\ChargeTypeDto;
use App\Services\ChargeTypeService;
use App\Traits\Validators;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ChargeTypeController extends Controller
{
    use ChargeTypeDto, Validators;

    public function __construct(private readonly ChargeTypeService $chargeTypeService)
    {
    }

    public function getChargeTypes(array $request, \stdClass &$output): void
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


        $this->chargeTypeService->getAll($request, $output);

    }

    /**
     * @throws ValidationException
     */
    public function createChargeType(array $request, \stdClass &$output): void
    {
        $rules = $this->getRules(['title', 'vat', 'service']);


        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->chargeTypeService->create($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function updateChargeType(array $request, \stdClass &$output): void
    {
        $rules = $this->getRules(['title', 'vat', 'service']);
        $rules['id'] = ['required', 'exists:charge_types,id'];


        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->chargeTypeService->update($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function deleteChargeTypes(array $request, \stdClass &$output): void
    {

        $validator = Validator::make($request, [
            'ids' => ['required', 'array'],
            'ids.*' => ['required','exists:charge_types,id'],
        ]);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->chargeTypeService->delete($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function getChargeTypeById(array $request, \stdClass &$output): void
    {
        $validator = Validator::make($request, [
            'id' => ['required']
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

        $this->chargeTypeService->getById($request, $output);

    }
}
