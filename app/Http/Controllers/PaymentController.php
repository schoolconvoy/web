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
use App\Models\Fee;
use App\Models\Waiver;
use Filament\Notifications\Notification;
class PaymentController extends Controller
{
    public function redirectToGateway()
    {
        try{

            $wardId = Cache::get('ward', 0);
            $ward = User::find($wardId);

            // Fetch the (unpaid) school fees of the current ward in the cache
            $fees = $ward->fees()->whereDoesntHave('payments')->get();
            Log::debug("Actual fee for ward: " . print_r($fees->sum('amount'), true));

            // Calculate total amount including discounts
            $amount = $fees->sum(function($fee) use ($ward) {
                $discount = $ward->discounts()->where('fee_id', $fee->id)->first();
                $discountedPercentage = $discount->percentage ?? 0;
                return $fee->amount - ($fee->amount * $discountedPercentage / 100);
            });

            Log::debug("Discounted amount due for ward: " . print_r($amount, true));

            $parent = $ward->parent;

            // No more fees to pay
            if ($amount === 0)
            {
                Log::debug("No amount due for ward: " . print_r($ward->id, true) . " and parent: " . print_r($parent[0]->id, true));
                return Redirect::back()->with('message', [
                    'title' => 'No amount due',
                    'body' => 'No amount due for ' . $ward->full_name,
                    'type' => 'info'
                ]);
            }

            // Convert to kobo and add paystack charges (1.5% + 100 naira)
            $charges = ((1.5 / 100) * $amount) + 100;
            $total = (($amount + $charges) * 100);

            Log::debug("Total amount to pay: " . print_r($total, true) . " base fee " . $amount);

            $ref = "ITGA-PAYMENT-" . Payment::count() + 1000 . "-"  . rand(1000, 9000);

            $data = array(
                "amount" => $total,
                "reference" => $ref,
                "email" => $parent[0]->email,
                "currency" => "NGN",
                "orderID" => Payment::count() + 1000,
            );

            Log::debug('[PAYSTACK] ' . print_r($data, true));

            return paystack()->getAuthorizationUrl($data)->redirectNow();
        }catch(\Exception $e) {
            Log::debug('[PAYSTACK ERR] ' . print_r([
                'msg'=>$e->getMessage(),
                'type'=>'error',
                'error' => print_r($e->getMessage(), true),
                'trace' => print_r($e->getTrace(), true),
                'line' => $e->getLine()
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

        $fees = $ward->fees;

        // Create a new invoice
        $payment = Payment::create([
            'amount' => $paymentDetails['data']['amount'] / 100, // Convert kobo to naira
            'paid' => $paymentDetails['status'],
            'feedback' => $paymentDetails['message'],
            'type' => $paymentDetails['data']['channel'],
            'provider' => 'paystack',
            'student_id' => $ward->id,
        ]);

        $payment->fees()->saveMany($fees);

        PaymentReceived::dispatch($payment, $ward);

        return Redirect::to(route('filament.parent.resources.fees.index'));
    }

    public function pay()
    {
        $ward = User::find(Cache::get('ward'));
        $totalAmount = $this->calculateTotalAmount($ward);

        if ($totalAmount <= 0) {
            return redirect()->back()->with('error', 'No pending fees to pay.');
        }

        $paymentData = [
            'email' => auth()->user()->email,
            'amount' => $totalAmount * 100, // Convert to kobo for Paystack
            'reference' => $this->generateReference(),
            'callback_url' => route('payment.verify'),
            'metadata' => [
                'ward_id' => $ward->id,
                'parent_id' => auth()->id(),
            ]
        ];

        try {
            $payment = Payment::create([
                'reference' => $paymentData['reference'],
                'amount' => $totalAmount,
                'status' => 'pending',
                'ward_id' => $ward->id,
                'parent_id' => auth()->id(),
            ]);

            $url = $this->initializePaystackPayment($paymentData);
            return redirect($url);
        } catch (\Exception $e) {
            Log::error('Payment initialization failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to process payment. Please try again.');
        }
    }

    protected function calculateTotalAmount(User $ward)
    {
        $pendingFees = $ward->fees()
            ->whereDoesntHave('payments')
            ->whereDoesntHave('waivers', function ($query) {
                $query->where(function($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                });
            })
            ->get();

        return $pendingFees->sum(function ($fee) {
            return $fee->getTotal($fee->discount_percentage);
        });
    }

    public function verify(Request $request)
    {
        $reference = $request->reference;
        $payment = Payment::where('reference', $reference)->first();

        if (!$payment) {
            return redirect()->route('parent.fees.index')->with('error', 'Invalid payment reference.');
        }

        try {
            $paymentData = $this->verifyPaystackPayment($reference);

            if ($paymentData['status'] === 'success') {
                $this->processSuccessfulPayment($payment);
                return redirect()->route('parent.fees.index')->with('success', 'Payment successful!');
            }

            $payment->update(['status' => 'failed']);
            return redirect()->route('parent.fees.index')->with('error', 'Payment verification failed.');
        } catch (\Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage());
            return redirect()->route('parent.fees.index')->with('error', 'Payment verification failed.');
        }
    }

    protected function processSuccessfulPayment(Payment $payment)
    {
        $ward = User::find($payment->ward_id);
        $pendingFees = $ward->fees()
            ->whereDoesntHave('payments')
            ->whereDoesntHave('waivers', function ($query) {
                $query->where(function($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                });
            })
            ->get();

        $remainingAmount = $payment->amount;

        foreach ($pendingFees as $fee) {
            $discountedAmount = $fee->getTotal($fee->discount_percentage);

            if ($remainingAmount >= $discountedAmount) {
                $payment->fees()->attach($fee->id, [
                    'amount_paid' => $discountedAmount,
                    'discount_applied' => $fee->discount_percentage
                ]);
                $remainingAmount -= $discountedAmount;
            }

            if ($remainingAmount <= 0) break;
        }

        $payment->update(['status' => 'success']);
    }

    protected function generateReference(): string
    {
        return 'PAY_' . uniqid() . '_' . time();
    }

    protected function initializePaystackPayment(array $data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . config('services.paystack.secret_key'),
                "Content-Type: application/json",
                "Cache-Control: no-cache",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new \Exception("cURL Error #:" . $err);
        }

        $result = json_decode($response, true);
        if (!$result['status']) {
            throw new \Exception($result['message']);
        }

        return $result['data']['authorization_url'];
    }

    protected function verifyPaystackPayment(string $reference)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . $reference,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . config('services.paystack.secret_key'),
                "Cache-Control: no-cache",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new \Exception("cURL Error #:" . $err);
        }

        $result = json_decode($response, true);
        if (!$result['status']) {
            throw new \Exception($result['message']);
        }

        return $result['data'];
    }
}
