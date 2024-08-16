<?php

namespace App\Http\Controllers;

use App\DomainData\WaitingListDto;
use App\Services\WaitingListService;
use App\Traits\Validators;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WaitingListController extends Controller
{
    use WaitingListDto, Validators;

    public function __construct(private readonly WaitingListService $waitingListService)
    {
    }

    public function getWaitingLists(array $request, \stdClass &$output): void
    {
        $validator = Validator::make($request, [
            'page' => ['required', 'min:1', 'integer'],
            'page_size' => ['required', 'min:1', 'integer'],
            'related_objects' => ['nullable', 'array'],
            'related_objects.*' => ['in:customers']
        ]);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        if (!isset($request['related_objects']))
            $request['related_objects'] = [];


        $this->waitingListService->getAll($request, $output);

    }

    /**
     * @throws ValidationException
     */
    public function createWaitingList(array $request, \stdClass &$output): void
    {
        $rules = $this->getRules(['customer_id', 'number_of_guests', 'from_time', 'to_time']);


        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->waitingListService->create($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function updateWaitingList(array $request, \stdClass &$output): void
    {
        $rules = $this->getRules(['customer_id', 'number_of_guests', 'from_time', 'to_time']);
        $rules['id'] = ['required', 'exists:waiting_lists,id'];


        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->waitingListService->update($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function deleteWaitingLists(array $request, \stdClass &$output): void
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

        $this->waitingListService->delete($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function getWaitingListById(array $request, \stdClass &$output): void
    {
        $validator = Validator::make($request, [
            'id' => ['required'],
            'related_objects' => ['nullable', 'array'],
            'related_objects.*' => ['in:customers']
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

        $this->waitingListService->getById($request, $output);

    }
}
