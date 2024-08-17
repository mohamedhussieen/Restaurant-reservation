<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function store(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
        'orders.*.meal_id' => 'required|integer|exists:meals,id',
        'orders.*.quantity' => 'required|integer|min:1',
            'table_id' => 'required|integer|exists:tables,id',
            'reservation_id' => 'required|integer|exists:reservations,id',
            'customer_id' => 'required|integer|exists:customers,id',
        ]);

        $meals = Meal::findMany($request->meal_ids);
        $total = 0;
        foreach ($request->orders as$order) {
            $meal = Meal::findOrFail($order['meal_id']);
            if ($meal->available_quantity < $order['quantity']) {
                return$this->error("Insufficient quantity for meal ID: {$meal->id}", 422);
            }
            $meal->decrement('available_quantity', $order['quantity']);
            $price = $meal->price * $order['quantity'];
            $discountedPrice = $price - ($price * $meal->discount / 100);
            $total += $discountedPrice;
        }

        $order = new Order([
            'user_id' => Auth::id(),
            'total' => $total,
            'table_id' => $request->table_id,
            'reservation_id' => $request->reservation_id,
            'customer_id' => $request->customer_id,
            'paid' => $request->paid,
            'date' => now(),
        ]);
        $order->save();

        foreach ($request->orders as$item) {
            OrderDetail::create([
                'order_id' => $order->id,
                'meal_id' => $item['meal_id'],
                'amount_to_pay' => $item['quantity'],
                'price' => Meal::find($item['meal_id'])->price,
                'discount' => Meal::find($item['meal_id'])->discount
            ]);
        }

        return$this->success($order, 'Order placed successfully.');
    }
}
