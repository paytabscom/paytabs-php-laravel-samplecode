<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $serverKey = "SDJNNWHNZD-JBJ9WM9JMH-2KWJHWDLMW";
    private $profileId = "64594";
    private $returnUrl = "https://webhook.site/5727c9aa-3417-4ce6-926c-c1cc5958ec02/return";
    private $callbackUrl = "https://webhook.site/5727c9aa-3417-4ce6-926c-c1cc5958ec02/callback";
    private $ipnUrl = "https://webhook.site/5727c9aa-3417-4ce6-926c-c1cc5958ec02/ipn";

    /**
     * Show the profile for a given user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /* raw SQL
         * $users = DB::select('select * from users where active = :id', ['id' => 1]);
         * Running SQL Queries: (fluent query builder)
         * OR: $users = DB::table('users')->get();
         * OR: $user = DB::table('users')->where('name', 'John')->first();
         * foreach ($users as $user) {
         *     echo $user->name;
         * }
         */
//        return response('Hello World', 200)
//            ->header('Content-Type', 'text/plain');
        $carts = DB::table('cart')->get();

        return view('home_carts', [
            'carts' => $carts
        ]);
    }

    /**
     * Show the profile for a given user.
     *
     * @return \Illuminate\View\View
     */
    public function purchaseWithHostedPayment()
    {        
        return view('hosted_payment.purchase_with_hosted_payment');
    }

    /**
     * 
     * @return \Illuminate\View\View
     */
    public function doHostedPayment(Request $request)
    {
        $products = $request->input('products');
        $productList= serialize(array_keys($products));
        $total= array_sum($products);
        
        DB::insert('insert into cart (products, total) values (?,?)', [$productList, $total]);
        $cartId = DB::getPdo()->lastInsertId();

        $client = new Client([
            'base_uri' => 'https://secure.paytabs.sa/payment/', // Base URI is used with relative requests
            'timeout'  => 2.0, // You can set any number of default request options.
            'verify'   => false, //disable SSL cerificate verification
        ]);

        $headers = ['Authorization' => $this->serverKey];
        $requestBody= '{
            "profile_id":         '. $this->profileId. ',
            "tran_type":          "sale",
            "tran_class":         "ecom",
            "cart_description":   "Desc of the items/services",
            "cart_id":            "'. $cartId. '",
            "cart_currency":      "sar",
            "cart_amount":        '. $total. ',
            "callback":           "'. $this->callbackUrl. '",
            "return":             "'. $this->returnUrl. '"
          }';

        $paymentRequest = new \GuzzleHttp\Psr7\Request('POST', 'request', $headers, $requestBody);
        $response = $client->send($paymentRequest, ['timeout' => 2]);
        $output= 'StatusCode: '. $response->getStatusCode(). '<br />'. 'Reason: '. $response->getReasonPhrase(). '<br /><br />';
        
        $responseBody = $response->getBody();
        //$output .= 'responseBody: <pre>'. $responseBody. '</pre><br />';
        // Implicitly cast the body to a string and print it
        $jsonResponseAsObj= \GuzzleHttp\Utils::jsonDecode($responseBody);
        $output .= 'JsonResponseAsObj<pre>'. print_r($jsonResponseAsObj, true). '</pre>';
        
        return view('simple_output', [
            'output' => $output
        ]);
    }

    /**
     * 
     * @return \Illuminate\View\View
     */
    public function collectRequestDetails()
    {
        /*echo route('verify_callback_request'). '<br>';
        echo url(''). '<br>';
        $route= Route::getRoutes()->getByName('verify_callback_request');
        echo '<pre>';
        echo 'getName:'. $route->getName(). '<br>';
        echo 'getActionName:'. $route->getActionName(). '<br>';
        echo 'uri:'. $route->uri();
        echo '</pre>';*/
        
        return view('hosted_payment.collect_request_details');
    }
    
    /**
     * 
     */
    public function verifyRequest(Request $request){
        //catch posted data
        $requestequestType = $request->input('type');
        $requestSignature = $request->input('signature');
        $requestContent = $request->input('content');

        //if callback chosen: simulate the request created by paytabs generate as a callback (and caught by webhook.io for us)
        if($requestequestType == 'callback'){
            //show the response returned by the callback request verifier: paymentCallback
            $this->simulateCallbackRequest($requestSignature, $requestContent);
        }
        //if return chosen: simulate the request created by paytabs generate as a return (and caught by webhook.io for us)
        elseif($requestequestType == 'return'){
            //show the response returned by the return request verifier: verifyReturnRequest
            $this->simulateReturnRequest($requestContent);
        }
        //if return chosen: simulate the request created by paytabs generate as an IPN (and caught by webhook.io for us)
        elseif($requestequestType == 'ipn'){
            //show the response returned by the IPN request verifier: verifyReturnRequest
            $this->simulateIpnRequest($requestContent);
        }

    }

    /**
     * 
     */
    private function simulateCallbackRequest($signature, $content) {
        
        $baseUri= url(''). '/';
        $route= Route::getRoutes()->getByName('payment_callback');
        $uri= $route->uri();
        
        $client = new Client([
            'base_uri' => $baseUri, // Base URI is used with relative requests
            'timeout'  => 2.0, // You can set any number of default request options.
            //'verify'   => false, //disable SSL cerificate verification
        ]);

        $headers = [
            'connection'        => 'close',
            'accept-encoding'   => 'gzip',
            'signature'         => $signature,
            'content-type'      => 'application/json',
            //'client-key'        => 'CBKMMK-VBKM62-6PVNP6-RVTMHM',
            //'content-length'      => 929,
            //'user-agent'        => 'Go-http-client/1.1',
            //'host'              => '',
            ];

        $request = new \GuzzleHttp\Psr7\Request('POST', $uri, $headers, $content);
        $response = $client->send($request, ['timeout' => 2]);
        
        echo $response->getStatusCode();
    }

    /**
     * RESTful callable action receives the callback request from the payment gateway after payment is processed
     */
    public function paymentCallback(Request $request){
        //verify that it is a valid callback request
        if($this->isValidCallbackRequest($request)){
            //update the cart payment status
            $this->updateCartByPaymentCallback($request);
            $response= 'valid callback request';
        }else{
            $response= 'INVALID callback request';
        }

        return response($response, 200)
            ->header('Content-Type', 'text/plain');        
    }

    /**
     * verify that it is a valid callback or IPN request
     */
    private function isValidCallbackRequest($request){
        $signature= $request->header('signature');
        $content= $request->getContent(); //get the request raw content

        $calculatedSignature = hash_hmac('sha256', $content, $this->serverKey);
        return (hash_equals($calculatedSignature, $signature ) === TRUE);
    }

    /** 
     * update cart according to the payment final status
     */
    private function updateCartByPaymentCallback($request){
        $content= $request->getContent();
        $jsonContentAsObj= \GuzzleHttp\Utils::jsonDecode($content);
        $cartId= $jsonContentAsObj->cart_id;
        $status= $jsonContentAsObj->payment_result->response_status;
        $message= $jsonContentAsObj->payment_result->response_message;
        $tranRef= $jsonContentAsObj->tran_ref;
        DB::update(
            'UPDATE cart SET payment_status_via_callback = :status, payment_message_via_callback= :message,'
            . 'callback_tran_ref= :tran_ref where cart_id = :id',
                ['status'=> $status, 'message'=> $message, 'tran_ref'=> $tranRef, 'id'=> $cartId]
            );
    }

    /**
     * RESTful callable action receives the IPN request from the payment gateway after payment is processed
     */
    public function paymentIpn(Request $request){
        //verify that it is a valid callback request
        if($this->isValidCallbackRequest($request)){
            //update the cart payment status
            $this->updateCartByPaymentIpn($request);
            $response= 'valid callback request';
        }else{
            $response= 'INVALID callback request';
        }

        return response($response, 200)
            ->header('Content-Type', 'text/plain');        
    }

    /** 
     * update cart according to the payment final status
     */
    private function updateCartByPaymentIpn($request){
        $content= $request->getContent();
        $jsonContentAsObj= \GuzzleHttp\Utils::jsonDecode($content);
        $cartId= $jsonContentAsObj->cart_id;
        $status= $jsonContentAsObj->payment_result->response_status;
        $message= $jsonContentAsObj->payment_result->response_message;
        $tranRef= $jsonContentAsObj->tran_ref;
        DB::update(
            'UPDATE cart SET payment_status_via_ipn = :status, payment_message_via_ipn= :message,'
            . 'ipn_tran_ref= :tran_ref where cart_id = :id',
                ['status'=> $status, 'message'=> $message, 'tran_ref'=> $tranRef, 'id'=> $cartId]
            );
    }

    /**
     * 
     */
    private function simulateReturnRequest($content) {
        
        $baseUri= url(''). '/';
        $route= Route::getRoutes()->getByName('verify_return_request');
        $uri= $route->uri();
        
        $client = new Client([
            'base_uri' => $baseUri, // Base URI is used with relative requests
            'timeout'  => 2.0, // You can set any number of default request options.
            //'verify'   => false, //disable SSL cerificate verification
        ]);

        $headers = [
            'connection'        => 'close',
            'accept-language'   => 'en-US,en;q=0.9',
            'accept-encoding'   => 'gzip, deflate, br',
//            'referer'         => 'https://secure.paytabs.sa/',
            'sec-fetch-dest'    => 'document',
            'sec-fetch-mode'    => 'navigate',
            'sec-fetch-site'    => 'cross-site',
            'accept'            => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
//            'user-agent'      => '',
            'content-type'      => 'application/x-www-form-urlencoded',
//            'origin'            => '',
            'upgrade-insecure-requests'            => 1,
            'cache-control'            => 'max-age=0',
            //'content-length'      => 241,
            //'host'              => '',
            ];

        $request = new \GuzzleHttp\Psr7\Request('POST', $uri, $headers, $content);
        $response = $client->send($request, ['timeout' => 2]);
        
        echo 'response status code:'. $response->getStatusCode();
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

        $calculatedSignature = hash_hmac('sha256', $query, $this->serverKey);
        if (hash_equals($calculatedSignature, $signature ) === TRUE) {
          $response= 'Valid request';
        }else{
          $response= 'INVALID request';
        }

        return response($response, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Show sample managed form
     *
     * @return \Illuminate\View\View
     */
    public function managedForm()
    {
        return view('hosted_payment.managed_form');
    }

    /**
     * Show sample managed form
     *
     * @return \Illuminate\View\View
     */
    public function processManagedForm(Request $request)
    {
        $token = $request->input('token');

        $client = new Client([
            'base_uri' => 'https://secure.paytabs.sa/payment/', // Base URI is used with relative requests
            'timeout'  => 2.0, // You can set any number of default request options.
            'verify'   => false, //disable SSL cerificate verification
        ]);

        $headers = ['Authorization' => $this->serverKey,
            'Content-Type'=> 'application/json'];
        $requestBody= '{
        "profile_id": '. $this->profileId. ',
        "tran_type": "sale",
        "tran_class": "ecom",
        "cart_id": "cart_77771",
        "cart_currency": "sar",
        "cart_amount": 12.3,
        "cart_description": "Description of the items/services",
        "paypage_lang": "en",
        "customer_details": {
            "name": "first last",
            "email": "email@domain.com",
            "phone": "0522222222",
            "street1": "address street",
            "city": "dubai",
            "state": "du",
            "country": "AE",
            "zip": "12345",
            "ip": "1.1.1.1"
        },
        "shipping_details": {
            "name": "name1 last1",
            "email": "email1@domain.com",
            "phone": "971555555555",
            "street1": "street2",
            "city": "dubai",
            "state": "dubai",
            "country": "AE",
            "zip": "54321"
        },
        "return": "'. $this->returnUrl. '",
        "callback": "'. $this->callbackUrl. '",
        "payment_token": "'. $token. '"
        }';

        $paymentRequest = new \GuzzleHttp\Psr7\Request('POST', 'request', $headers, $requestBody);
        $response = $client->send($paymentRequest, ['timeout' => 2]);
        $responseBody = $response->getBody();
        $jsonResponseAsObj= \GuzzleHttp\Utils::jsonDecode($responseBody);
        
        //if valid response and it contains a redirect_url to proceed with payment then redirect the client to it
        if($response->getStatusCode() ==200 && $jsonResponseAsObj->redirect_url ){
            $jsonResponseAsObj->redirect_url;
            return \Illuminate\Support\Facades\Redirect::to($jsonResponseAsObj->redirect_url);
        }
        
        $output= 'StatusCode: '. $response->getStatusCode(). '<br />'. 'Reason: '. $response->getReasonPhrase(). '<br /><br />';

        //$output .= 'responseBody: <pre>'. $responseBody. '</pre><br />';
        // Implicitly cast the body to a string and print it
        $output .= 'JsonResponseAsObj<pre>'. print_r($jsonResponseAsObj, true). '</pre>';
        
        return view('simple_output', [
            'output' => $output
        ]);
    }

}
