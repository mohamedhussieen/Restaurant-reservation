<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Traits\ApiResponse;

class MenuController extends Controller{
    use ApiResponse;

   public function listMenuItems()
    {
        $meals = Meal::where('available_quantity', '>', 0)->get();

        if ($meals->isEmpty()) {
            return$this->error('No meals available at the moment.', 404);
        }

        return$this->success($meals, 'Menu items listed successfully.');
    }
}
