<?php

namespace App\Http\Controllers\Nagad;

use App\Http\Controllers\Controller;
use App\Services\NagadPaymentService;
use App\Services\Interfaces\NagadPaymentServiceInterface;
use Illuminate\Http\Request;

class NagadPaymentController extends Controller
{
    protected NagadPaymentServiceInterface $nagadPayment;

    public function __construct(NagadPaymentService $nagadPayment)
    {
        $this->nagadPayment = $nagadPayment;
    }

    public function processPayment(Request $request)
    {
        $amount = 10;
        $trx_id = uniqid();
        $call_back = 'http://127.0.0.1:8004/success-payment';
        $response = $this->nagadPayment->payment($amount, $trx_id, $call_back);
        // return response()->json(['data' => $response]);
        if (isset($response) && $response->status == "Success") {
            return redirect()->away($response->callBackUrl);
        }
        // return redirect()->back()->with("error-alert", "Invalid request try again after few time later");
    }
}
