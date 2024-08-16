<?php

namespace App\Http\Controllers;

use App\DomainData\CustomerDto;
use App\Services\CustomerService;
use App\Traits\Validators;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    use CustomerDto, Validators;

    public function __construct(private readonly CustomerService $customerService)
    {
    }

    public function getCustomers(array $request, \stdClass &$output): void
    {
        $validator = Validator::make($request, [
            'page' => ['required', 'min:1', 'integer'],
            'page_size' => ['required', 'min:1', 'integer'],
            'related_objects' => ['nullable', 'array'],
            'related_objects.*' => ['in:orders,reservations']
        ]);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        if (!isset($request['related_objects']))
            $request['related_objects'] = [];


        $this->customerService->getAll($request, $output);

    }

    /**
     * @throws ValidationException
     */
    public function createCustomer(array $request, \stdClass &$output): void
    {
        $rules = $this->getRules(['name', 'phone']);


        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->customerService->create($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function updateCustomer(array $request, \stdClass &$output): void
    {
        $rules = $this->getRules(['name', 'phone']);
        $rules['id'] = ['required', 'exists:customers,id'];


        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->customerService->update($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function deleteCustomers(array $request, \stdClass &$output): void
    {

        $validator = Validator::make($request, [
            'ids' => ['required', 'array'],
            'ids.*' => ['required'],
        ]);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->customerService->delete($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function getCustomerById(array $request, \stdClass &$output): void
    {
        $validator = Validator::make($request, [
            'id' => ['required'],
            'related_objects' => ['nullable', 'array'],
            'related_objects.*' => ['in:orders,reservations']
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

        $this->customerService->getById($request, $output);

    }
}
