<?php

namespace App\Repositories;

class MealRepository extends Repository
{

    public function getMealWithServedMealsByDate(int $mealId, string $date)
    {

        return $this->getModel->where('id',$mealId)
            ->withCount(['orderDetails' => function($query) use ($mealId,$date) {
                $query->where('meal_id',$mealId)
                    ->whereDate('created_at', $date);
            }]);
    }

}
