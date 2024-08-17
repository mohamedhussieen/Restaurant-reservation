<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Reservation;
use App\Models\WaitingList;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TableReservationController extends Controller{
    use ApiResponse;

    public function reserveTable(Request $request)
    {
        $request->validate([
            'table_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'from_time' => 'required|date',
            'to_time' => 'required|date',
            'guests' => 'required|integer'
        ]);

        $table = Table::with('reservations')->find($request->table_id);
        if (!$table) {
            return$this->error('Table not found.', 404);
        }
        $overlap = Reservation::where('table_id', $request->table_id)
            ->where(function ($query) use ($request) {
            $query->whereBetween('from_time', [$request->from_time, $request->to_time])
                  ->orWhereBetween('to_time', [$request->from_time, $request->to_time])
                  ->orWhere(function ($query) use ($request) {
                      $query->where('from_time', '<', $request->from_time)
                            ->where('to_time', '>', $request->to_time);
                  });
        })->exists();  if ($overlap) {
            return$this->error('This table is already reserved for the requested time.', 409);
        }

        if ($table->capacity < $request->guests) {
            return$this->error('Table does not have enough capacity.', 409);
        }

        $reservation = new Reservation([
            'table_id' => $request->table_id,
            'customer_id' => $request->customer_id,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time
        ]);
        $reservation->save();

        return$this->success($reservation, 'Table reserved successfully.');
    }
}
