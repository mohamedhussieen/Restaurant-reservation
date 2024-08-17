<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TableAvailabilityController extends Controller{
    use ApiResponse;

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'table_id' => 'required|integer',
            'datetime' => 'required|date',
            'guests' => 'required|integer'
        ]);

        $table = Table::find($request->table_id);
        if (!$table) {
            return$this->error('Table not found.', 404);
        }

        $isAvailable = !$table->reservations()
            ->where(function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('from_time', '<=', $request->datetime)
                          ->where('to_time', '>=', $request->datetime);
                });
            })->exists();

        if (!$isAvailable || $table->capacity < $request->guests) {
            return$this->error('Table is not available at the requested time or does not have enough capacity.', 409);
        }

        return$this->success(null, 'Table is available.', 200);
    }
}
