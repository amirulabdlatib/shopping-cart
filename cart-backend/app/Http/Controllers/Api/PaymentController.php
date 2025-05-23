<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use ErrorException;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function payStripe(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        try{
            $checkout_session = Session::create([
                'line_items'=>[[
                    'price_data' => [
                        'currency'=> 'usd',
                        'product_data' => [
                            'name' => 'Vue Shop Orrders'
                        ],
                        'unit_amount'=> $this->calculateOrderTotal($request->cartItems)
                    ],
                    'quantity' => 1
                ]],
                'mode' => 'payment',
                'success_url' => $request->success_url
            ]);

            return response()->json([
                'url'=>$checkout_session->url
            ]);

        }catch(ErrorException $e){
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function calculateOrderTotal($items)
    {
        $total = 0;
        foreach($items as $item){
            $total += $this->calculateTotal($item['product_price'],$item['quantity']);
            return $total * 100;
        }
    }

    private function calculateTotal($price,$quantity)
    {
        $total = $price * $quantity;
        return $total;
    }
}
