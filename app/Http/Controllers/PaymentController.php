<?php

namespace App\Http\Controllers;

use App\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kabangi\MpesaLaravel\Facades\Mpesa;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $mpResponse = Mpesa::STKPush ([
            'amount' => 1,//intval($request->amount),
            'phoneNumber' => str_replace('+','',$request->phone),
            'accountReference' => '12',
            'transactionDesc' => "Test Transaction",
            'CallBackURL' =>'https://604ccf3f.ngrok.io/confirm',
        ]);
        return $mpResponse;
       /* if(!empty($mpResponse-> CheckoutRequestID)){
            $checkout= null;
            //save to the database

            $data = [
                'status' => 'COMPLETE',
                'phone'  => $request->input('phone'),

            ];

            $checkout = Payment::create($data);


        }*/
    }

    public function confirm(Request $request)
    {
        $data = file_get_contents('php://input');
        //$data = json_decode($data);
        file_put_contents(public_path()."data.txt", $data);
    }
}
