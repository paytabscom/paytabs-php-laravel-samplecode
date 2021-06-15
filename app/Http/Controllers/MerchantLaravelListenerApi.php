<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Paytabscom\Laravel_paytabs\IpnRequest;
use Paytabscom\Laravel_paytabs\BadRequestException;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;

class MerchantLaravelListenerApi extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * RESTful callable action receives: callback request\IPN Default Web request from the payment gateway after payment is processed
     */
    public function paymentIPN(Request $request){
        try{
            $ipnRequest= new IpnRequest($request);
            self::updateCartByPaymentIPN($ipnRequest->getIpnRequestDetails());
            $response= 'valid callback\IPN request. Cart updated';
            return response($response, 200)
                ->header('Content-Type', 'text/plain');        
        }catch(\Exception $e){
            return response($e, 200)
                ->header('Content-Type', 'text/plain');        
        }
    }

    /**
     * RESTful callable action receives the return request from the payment gateway after payment is processed
     */
    public function verifyReturnRequest(Request $request){
        $content= $request->getContent(); //get the request raw content

        // Generate an array from the URL-encoded query string of Post fields
        parse_str($content, $decodedArray);
        //echo '<br />decode:<br /><pre>';print_r($decodedArray); echo '</pre><br />';

        $signature= $decodedArray["signature"]; //extract the signature submitted with the request
        unset($decodedArray["signature"]);

        // Ignore empty values fields
        $signature_fields = array_filter($decodedArray);

        // Sort form fields 
        ksort($signature_fields);
        //echo '<br />filtered:<br /><pre>';print_r($signature_fields); echo '</pre><br />';

        // Generate URL-encoded query string of Post fields except signature field.
        $query = http_build_query($signature_fields);
        //echo '<br />querystring:<br />'. $query. '<br /><br /><br />';

        $calculatedSignature = hash_hmac('sha256', $query, config('app.gatewayServerKey'));
        if (hash_equals($calculatedSignature, $signature ) === TRUE) {
          $response= 'Valid request';
        }else{
          $response= 'INVALID request';
        }

        return response($response, 200)
            ->header('Content-Type', 'text/plain');
    }

    /** 
     * update cart according to the payment final status
     */
    private static function updateCartByPaymentIPN($requestData){
        $cartId= $requestData->cart_id;
        $status= $requestData->payment_result->response_status;
        $code= $requestData->payment_result->response_code;
        $message= $requestData->payment_result->response_message;
        $tranRef= $requestData->tran_ref;
        DB::update(
            'UPDATE '. config('app.cartTable'). ' SET payment_resp_status = :status, payment_resp_code = :code, payment_resp_msg= :message,'
            . 'payment_tran_ref= :tran_ref, payment_updated_at= NOW() WHERE cart_id = :id',
                ['status'=> $status, 'code'=> $code , 'message'=> $message, 'tran_ref'=> $tranRef, 'id'=> $cartId]
            );
    }

    /** 
     * update cart according to the payment final status
     */
    private static function updateCartByPaymentIPNBasic($requestData){
        $cartId= $requestData->cart_id;
        $status= $requestData->response_status;
        $code= $requestData->response_code;
        $message= $requestData->response_message;
        $tranRef= $requestData->tran_ref;
        DB::update(
            'UPDATE '. config('app.cartTable'). ' SET payment_resp_status = :status, payment_resp_code = :code, payment_resp_message= :message,'
            . 'payment_tran_ref= :tran_ref, payment_updated_at= NOW() WHERE cart_id = :id',
                ['status'=> $status, 'code'=> $code , 'message'=> $message, 'tran_ref'=> $tranRef, 'id'=> $cartId]
            );
    }

}