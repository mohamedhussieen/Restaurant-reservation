<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'pricing_strategy' => 'required|in:standard,service_only'
        ]);

        $order = Order::findOrFail($request->order_id);
        $subtotal = $order->total;
        $order->paid = true;
        $order->save();
        $invoice = [];

        switch ($request->pricing_strategy) {
            case'standard':
                $taxes = $subtotal * 0.14;
                $serviceCharge = $subtotal * 0.20;
                $total = $subtotal + $taxes + $serviceCharge;
                $invoice = [
                    'subtotal' => $subtotal,
                    'taxes' => $taxes,
                    'service_charge' => $serviceCharge,
                    'total' => $total
                ];
                break;

            case'service_only':
                $serviceCharge = $subtotal * 0.15;
                $total = $subtotal + $serviceCharge;
                $invoice = [
                    'subtotal' => $subtotal,
                    'service_charge' => $serviceCharge,
                    'total' => $total
                ];
                break;

            default:
                return$this->error('Invalid pricing strategy.', 400);
        }

        return$this->success($invoice, 'Invoice generated successfully.');
    }
}
