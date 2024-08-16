<?php

namespace App\Http\Controllers;

use App\DomainData\MealDto;
use App\Services\MealService;
use App\Traits\Validators;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MealController extends Controller
{
    use MealDto, Validators;

    public function __construct(private readonly MealService $mealService)
    {
    }

    public function getMeals(array $request, \stdClass &$output): void
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


        $this->mealService->getAll($request, $output);

    }

    /**
     * @throws ValidationException
     */
    public function createMeal(array $request, \stdClass &$output): void
    {
        $rules = $this->getRules(['description', 'price', 'discount', 'available_quantity']);


        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->mealService->create($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function updateMeal(array $request, \stdClass &$output): void
    {
        $rules = $this->getRules(['description', 'price', 'discount', 'available_quantity']);
        $rules['id'] = ['required', 'exists:meals,id'];


        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->mealService->update($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function deleteMeals(array $request, \stdClass &$output): void
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

        $this->mealService->delete($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function getMealById(array $request, \stdClass &$output): void
    {
        $validator = Validator::make($request, [
            'id' => ['required'],
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

        $this->mealService->getById($request, $output);

    }
}
