<?php

namespace App\Http\Controllers;

use App\Events\PaymentReceived;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Unicodeveloper\Paystack\Paystack;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class PaymentController extends Controller
{
    public function redirectToGateway()
    {
        try{

            $wardId = Cache::get('ward', 0);
            $ward = User::find($wardId);

            // Fetch the school fees of the current ward in the session
            $amount = $ward->class->fees()->sum('amount');
            $parent = $ward->parent;

            $total = $amount * 100;

            $ref = "ITGA-PAYMENT-" . Payment::count() + 10000 . "-" . date("ymd") . "-" . rand(1000, 9000);

            $data = array(
                "amount" => $total,
                "reference" => $ref,
                "email" => $parent[0]->email,
                "currency" => "NGN",
                "orderID" => 23456,
            );

            return paystack()->getAuthorizationUrl($data)->redirectNow();
        }catch(\Exception $e) {
            Log::debug('[PAYSTACK ERR] ' . print_r([
                'msg'=>'The paystack token has expired. Please refresh the page and try again.',
                'type'=>'error',
                'error' => print_r($e->getMessage(), true)
            ], true));

            return Redirect::back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
        }
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = paystack()->getPaymentData();

        $wardId = Cache::get('ward', 0);
        $ward = User::find($wardId);

        $fees = $ward->class->fees;

        // Create a new invoice
        $payment = Payment::create([
            'amount' => $paymentDetails['data']['amount'] / 100, // Convert kobo to naira
            'paid' => $paymentDetails['status'],
            'feedback' => $paymentDetails['message'],
            'type' => $paymentDetails['data']['channel'],
            'provider' => 'paystack'
        ]);

        $payment->fees()->saveMany($fees);

        PaymentReceived::dispatch($payment, $ward);

        return Redirect::to(route('filament.admin.resources.fees.index'));
    }
}
