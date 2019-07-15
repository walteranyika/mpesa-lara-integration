<?php

namespace App\Http\Controllers;

use App\User;
use App\Payment;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Kabangi\MpesaLaravel\Facades\Mpesa;
class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        //Validate the input
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);
        $phone = substr($request->phone, 1);
        $payer = '254' . $phone;
        $user_id = auth()->user()->id;
        //dd($payer);
        try {

            $mpResponse = Mpesa::STKPush([
                'amount' => 1,//intval($request->amount),
                'phoneNumber' => $payer,
                'accountReference' => '12',
                'transactionDesc' => "Test Transaction",
                'CallBackURL' =>'https://ecfdb1ac.ngrok.io/confirm/',
        ]);
        }catch(\Exception $e){
            $response = json_decode($e->getMessage());
        }
        //return response()->json($mpResponse);

        //Save an attempt of Mpesa payment into Transactions Table  with status Placed
        if (!empty($mpResponse->CheckoutRequestID)) {
            $data = [
                'request_id' => $mpResponse->CheckoutRequestID,
                'amount' => 1,
                'phone' => $payer,
                "merchant_reference" => $mpResponse->MerchantRequestID,
                "user_id" => $user_id,
                "status" => 'Placed',
            ];
            //dd($data);
            $query = Payment::where('merchant_reference', '=', $data['merchant_reference'])->first();
            if (is_null($query)) {
                $response = Payment::create($data);
            } else {
                return abort(503, "Something went wrong. Please try again.");
            }
            return redirect('verify-payment')->with('success', trans('Kindly wait as we process your Payment'));

        }

    }
    public function getCallback()
    {
        $data = file_get_contents('php://input');
       // file_put_contents(public_path()."data.txt","the response is");
        Storage::put('data.txt', json_encode($data));
        //dd($data);
        if(!$data){
            echo "Invalid Request";
            exit;
        }
        $data = json_decode($data);
        $tmp = $data->Body->stkCallback;
        $master = array();
        foreach($data->Body->stkCallback->CallbackMetadata->Item as $item){
            $item = (array) $item;
            $master[$item['Name']] = ((isset($item['Value'])) ? $item['Value'] : NULL);

        }
        $master = (object) $master;
        $master->ResultCode = $tmp->ResultCode;
        $master->MerchantRequestID = $tmp->MerchantRequestID;
        $master->CheckoutRequestID = $tmp->CheckoutRequestID;
        $master->ResultDesc = $tmp->ResultDesc;
         $id= $tmp->MerchantRequestID;
         $transaction=Payment::where('merchant_reference',$id)->first();


        //Check MPESA status query
        if($master->ResultCode == 0 )
        {
           /* $data = [
                "amount"=> $master->Amount,
                'reference'=>$master->MpesaReceiptNumber,
                "request_id"=>$master->CheckoutRequestID,
                "user_id" =>1,
                'phone'=>$master->PhoneNumber,
                "status"  => 'COMPLETE',
            ];*/
        $transaction->status='COMPLETED';
        $transaction->save();

        }
        return "success";


    }

    public function verifyPayment()
    {
        return view('verify_payment');
    }
}
