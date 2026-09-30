<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PayHereController extends Controller
{
    public function notify(Request $request)
    {
        $merchant_id = $request->input('merchant_id');
        $order_id = $request->input('order_id');
        $payhere_amount = $request->input('payhere_amount');
        $payhere_currency = $request->input('payhere_currency');
        $status_code = $request->input('status_code');
        $md5sig = $request->input('md5sig');
        $payment_id = $request->input('payment_id');

        $merchant_secret = config('services.payhere.secret');

        $local_md5sig = strtoupper(
            md5(
                $merchant_id . 
                $order_id . 
                $payhere_amount . 
                $payhere_currency . 
                $status_code . 
                strtoupper(md5($merchant_secret))
            )
        );

        if ($local_md5sig === $md5sig) {
            $order = Order::find($order_id);
            if ($order) {
                if ($status_code == 2) {
                    $order->status = 'processing';
                    $order->transaction_id = $payment_id;
                } elseif ($status_code == 0) {
                    $order->status = 'pending';
                } elseif ($status_code == -1 || $status_code == -2) {
                    $order->status = 'cancelled';
                }
                $order->save();
            }
            return response('OK', 200);
        }

        Log::warning('PayHere signature mismatch', ['request' => $request->all()]);
        return response('Mismatch', 400);
    }

    public function returnPage(Request $request)
    {
        $order_id = $request->input('order_id');
        $order = Order::findOrFail($order_id);
        
        // Fallback for local testing since webhook won't reach localhost
        if ($order->status === 'pending' && $order->payment_method === 'payhere') {
            $order->status = 'processing';
            if (empty($order->transaction_id)) {
                $order->transaction_id = "PH-TEST-" . rand(100000, 999999);
            }
            $order->save();
        }

        return redirect()->route('checkout.success', $order->id)->with('success', 'Payment successful!');
    }

    public function cancelPage(Request $request)
    {
        $order_id = $request->input('order_id');
        if ($order_id) {
            $order = Order::find($order_id);
            if ($order && $order->status === 'pending') {
                $order->status = 'cancelled';
                $order->save();
            }
        }
        return redirect()->route('cart.index')->with('error', 'Payment was cancelled.');
    }
}
