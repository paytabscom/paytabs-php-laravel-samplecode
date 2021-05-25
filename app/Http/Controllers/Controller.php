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

    private $returnUrl = "https://webhook.site/5727c9aa-3417-4ce6-926c-c1cc5958ec02/return";
    private $callbackUrl = "https://webhook.site/5727c9aa-3417-4ce6-926c-c1cc5958ec02/callback";

    /**
     * Show carts
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /* raw SQL
         * $users = DB::select('select * from users where active = :id', ['id' => 1]);
         * Running SQL Queries: (fluent query builder)
         * $users = DB::table('users')->get();
         * OR: $user = DB::table('users')->where('name', 'John')->first();
         * foreach ($users as $user) {
         *     echo $user->name;
         * }
         */
//        return response('Hello World', 200)
//            ->header('Content-Type', 'text/plain');
        $carts = DB::table(config('app.cartTable'))->get()->sortBy('cart_id', 0, 1);

        return view('home_carts', [
            'carts' => $carts,
            'cart_profile' => config('app.cartTable')
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
        $isFramedPayment = $request->input('framed');
        $products = $request->input('products');
        $productList= serialize(array_keys($products));
        $total= array_sum($products);
        $tranType= ($request->input('auth'))? 'auth':'sale';

        DB::insert('INSERT INTO '. config('app.cartTable'). ' (products, total, tran_type) VALUES (?,?,?)', [$productList, $total, $tranType]);
        $cartId = DB::getPdo()->lastInsertId();

        $client = new Client([
            'base_uri' => config('app.paymentApiBaseUri'), // Base URI is used with relative requests
            'timeout'  => 2.0, // You can set any number of default request options.
            'verify'   => false, //disable SSL cerificate verification
        ]);

        $headers = ['Authorization' => config('app.gatewayServerKey') ];
        $requestAsArray= [
            "profile_id" => config('app.profileId'),
            "tran_type" => $tranType,
            "tran_class" => "ecom",
            "cart_description" =>"Desc of the items/services",
            "cart_id" => $cartId,
            "cart_currency" =>"sar",
            "cart_amount" => $total,
            "customer_details" => [
                "name" => "first last",
                "email" => "email@domain.com",
                "phone" => "0522222222",
                "street1" => "address street",
                "city" => "dubai",
                "state" => "du",
                "country" => "AE",
                "zip" => "12345",
                "ip" => "1.1.1.1"] ,
            "callback" => $this->callbackUrl,
            "return" => $this->returnUrl
          ];
        //if displaying payment page as iframe was choosen then pass a special flag
        if($isFramedPayment){
            $requestAsArray['framed']= true;
        }
        //if shipping details neither passed nor set as optional, it will be prompted in payment page
        $isShippingOptional= null;
        if($isShippingOptional){
            $requestAsArray['hide_shipping']= true; //make shipping_details optional
        }else{
            $requestAsArray['shipping_details']= [
                "name" => "name1 last1",
                "email" => "email1@domain.com",
                "phone" => "971555555555",
                "street1" => "street2",
                "city" => "dubai",
                "state" => "dubai",
                "country" => "AE",
                "zip" => "54321"];
        }
        $requestBody= json_encode($requestAsArray);
        $paymentRequest = new \GuzzleHttp\Psr7\Request('POST', 'request', $headers, $requestBody);
        $response = $client->send($paymentRequest, ['timeout' => 2]);
        $responseBody = $response->getBody();
        //$output .= 'responseBody: <pre>'. $responseBody. '</pre><br />';
        // Implicitly cast the body to a string and print it
        $jsonResponseAsObj= \GuzzleHttp\Utils::jsonDecode($responseBody);

        $output= 'StatusCode: '. $response->getStatusCode(). '<br />'. 'Reason: '. $response->getReasonPhrase(). '<br />'. 
                '<a href="'. $jsonResponseAsObj->redirect_url. '" target="_blank">enter card details</a> <br /><br />';
        
        $output .= 'JsonResponseAsObj<pre>'. print_r($jsonResponseAsObj, true). '</pre>';

        //display the iframe
        if($isFramedPayment){
            $output .= '<iframe width="600" height="800" src="'. $jsonResponseAsObj->redirect_url. '" ></iframe>';
        }

        //update the purchase order with the tran ref returned from the payment gateway
        DB::update('UPDATE '. config('app.cartTable'). ' SET payment_tran_ref = ? WHERE cart_id=?', [$jsonResponseAsObj->tran_ref, $cartId]);

        return view('simple_output', [
            'output' => $output
        ]);
    }

    /**
     * Show sample managed form
     *
     * @return \Illuminate\View\View
     */
    public function managedForm()
    {
        return view('hosted_payment.managed_form', [
            'js_lib_uri' => config('app.paymentApiBaseUri'),
            'client_key' => config('app.gatewayClientKey')
            ]);
    }

    /**
     * process a managed form payment
     *
     * @return \Illuminate\View\View
     */
    public function processManagedForm(Request $request)
    {
        $token = $request->input('token');
        $products = $request->input('products');
        $productList= serialize(array_keys($products));
        $total= array_sum($products);
        $tranType= ($request->input('auth'))? 'auth':'sale';

        DB::insert('insert into '. config('app.cartTable'). ' (products, total, tran_type) values (?,?,?)', [$productList, $total, $tranType]);
        $cartId = DB::getPdo()->lastInsertId();

        $client = new Client([
            'base_uri' => config('app.paymentApiBaseUri'), // Base URI is used with relative requests
            'timeout'  => 2.0, // You can set any number of default request options.
            'verify'   => false, //disable SSL cerificate verification
        ]);

        $headers = ['Authorization' => config('app.gatewayServerKey'),
            'Content-Type'=> 'application/json'];
        $requestAsArray= [
            "profile_id" => config('app.profileId'),
            "tran_type" => $tranType,
            "tran_class" => "ecom",
            "cart_id" => $cartId,
            "cart_currency" => "sar",
            "cart_amount"  => $total,
            "cart_description" => "Description of the items/services",
            "paypage_lang" => "en",
            "customer_details" => [
                "name" => "first last",
                "email" => "email@domain.com",
                "phone" => "0522222222",
                "street1" => "address street",
                "city" => "dubai",
                "state" => "du",
                "country" => "AE",
                "zip" => "12345",
                "ip" => "1.1.1.1"],
            "return" => $this->returnUrl. "/managed_form",
            "callback" => $this->callbackUrl. "/managed_form",
            "payment_token" => $token
            ];
        $requestBody= json_encode($requestAsArray);
        $paymentRequest = new \GuzzleHttp\Psr7\Request('POST', 'request', $headers, $requestBody);
        $response = $client->send($paymentRequest, ['timeout' => 2]);
        $responseBody = $response->getBody();
        $jsonResponseAsObj= \GuzzleHttp\Utils::jsonDecode($responseBody);
        
        //if valid response and it contains a redirect_url to proceed with payment then redirect the client to it
        if($response->getStatusCode() ==200 && $jsonResponseAsObj->redirect_url ){
            //update the purchase order with the tran ref returned from the payment gateway
            DB::update('UPDATE '. config('app.cartTable'). ' SET payment_tran_ref = ? WHERE cart_id=?', [$jsonResponseAsObj->tran_ref, $cartId]);
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

    /**
     * Show sample own form
     *
     * @return \Illuminate\View\View
     */
    public function ownForm()
    {
        return view('own_form');
    }

    /**
     * process an own form payment
     *
     * @return \Illuminate\View\View
     */
    public function processOwnForm(Request $request)
    {
        $pan = $request->input('number');
        $expiryMonth = $request->input('expmonth');
        $expiryYear = $request->input('expyear');
        $cvv = $request->input('cvv');
        
        $products = $request->input('products');
        $productList= serialize(array_keys($products));
        $total= array_sum($products);
        $tranType= ($request->input('auth'))? 'auth':'sale';

        DB::insert('insert into '. config('app.cartTable'). ' (products, total, tran_type) values (?,?,?)', [$productList, $total, $tranType]);
        $cartId = DB::getPdo()->lastInsertId();

        $client = new Client([
            'base_uri' => config('app.paymentApiBaseUri'), // Base URI is used with relative requests
            'timeout'  => 2.0, // You can set any number of default request options.
            'verify'   => false, //disable SSL cerificate verification
        ]);

        $headers = ['Authorization' => config('app.gatewayServerKey'),
            'Content-Type'=> 'application/json'];
        $requestAsArray= [
            "profile_id" => config('app.profileId'),
            "tran_type" => $tranType,
            "tran_class" => "ecom",
            "cart_id" => $cartId,
            "cart_currency" => "sar",
            "cart_amount"  => $total,
            "cart_description" => "Description of the items/services",
            "card_details" => [
                "pan" => $pan,
                "expiry_month" => (int)$expiryMonth,
                "expiry_year" => (int)$expiryYear,
                "cvv" => $cvv ],
            "return" => $this->returnUrl. "/own_form",
            "callback" => $this->callbackUrl. "/own_form"
            ];
        echo $requestBody= json_encode($requestAsArray);
        $paymentRequest = new \GuzzleHttp\Psr7\Request('POST', 'request', $headers, $requestBody);
        $response = $client->send($paymentRequest, ['timeout' => 2]);
        $responseBody = $response->getBody();
        $jsonResponseAsObj= \GuzzleHttp\Utils::jsonDecode($responseBody);
        
        $output= 'StatusCode: '. $response->getStatusCode(). '<br />'. 'Reason: '. $response->getReasonPhrase(). '<br /><br />';

        //$output .= 'responseBody: <pre>'. $responseBody. '</pre><br />';
        // Implicitly cast the body to a string and print it
        $output .= 'JsonResponseAsObj<pre>'. print_r($jsonResponseAsObj, true). '</pre>';
        
        return view('simple_output', [
            'output' => $output
        ]);
    }

    /**
     * capturing a transaction of a cart
     *
     * @return \Illuminate\View\View
     */
    public function capture($cartId)
    {
        $cart = DB::table(config('app.cartTable'))->where('cart_id', $cartId)->first();
        
        $response= $this->callFollowupTran('capture', $cart);
        
        $output= 'StatusCode: '. $response->getStatusCode(). '<br />'. 'Reason: '. $response->getReasonPhrase(). '<br /><br />';

        $responseBody = $response->getBody();
        //$output .= 'responseBody: <pre>'. $responseBody. '</pre><br />';
        $jsonResponseAsObj= \GuzzleHttp\Utils::jsonDecode($responseBody);
        $output .= 'JsonResponseAsObj<pre>'. print_r($jsonResponseAsObj, true). '</pre>';

        self::updateCartCaptureDetails($jsonResponseAsObj);

        return view('simple_output', [
            'output' => $output
        ]);
    }

    /**
     * voiding a transaction of a cart
     *
     * @return \Illuminate\View\View
     */
    public function void($cartId)
    {
        $cart = DB::table(config('app.cartTable'))->where('cart_id', $cartId)->first();
        
        $response= $this->callFollowupTran('void', $cart);
        
        $output= 'StatusCode: '. $response->getStatusCode(). '<br />'. 'Reason: '. $response->getReasonPhrase(). '<br /><br />';

        $responseBody = $response->getBody();
        //$output .= 'responseBody: <pre>'. $responseBody. '</pre><br />';
        $jsonResponseAsObj= \GuzzleHttp\Utils::jsonDecode($responseBody);
        $output .= 'JsonResponseAsObj<pre>'. print_r($jsonResponseAsObj, true). '</pre>';

        self::updateCartCaptureDetails($jsonResponseAsObj);

        return view('simple_output', [
            'output' => $output
        ]);
    }

    /**
     * refunding a transaction of a cart
     *
     * @return \Illuminate\View\View
     */
    public function refund($cartId)
    {
        $cart = DB::table(config('app.cartTable'))->where('cart_id', $cartId)->first();
        
        $response= $this->callFollowupTran('refund', $cart);
        
        $output= 'StatusCode: '. $response->getStatusCode(). '<br />'. 'Reason: '. $response->getReasonPhrase(). '<br /><br />';

        $responseBody = $response->getBody();
        //$output .= 'responseBody: <pre>'. $responseBody. '</pre><br />';
        $jsonResponseAsObj= \GuzzleHttp\Utils::jsonDecode($responseBody);
        $output .= 'JsonResponseAsObj<pre>'. print_r($jsonResponseAsObj, true). '</pre>';

        self::updateCartCaptureDetails($jsonResponseAsObj);

        return view('simple_output', [
            'output' => $output
        ]);
    }

    /**
     * lookup a transaction of a cart
     *
     * @return \Illuminate\View\View
     */
    public function lookup($cartId)
    {
        $cart = DB::table(config('app.cartTable'))->where('cart_id', $cartId)->first();
        
        $apiResponse= $this->callQueryTran($cart);
        
        $output= 'StatusCode: '. $apiResponse->getStatusCode(). '<br />'. 'Reason: '. $apiResponse->getReasonPhrase(). '<br /><br />';

        $apiResponseBody = $apiResponse->getBody();
        //$output .= 'responseBody: <pre>'. $responseBody. '</pre><br />';
        $jsonResponseAsObj= \GuzzleHttp\Utils::jsonDecode($apiResponseBody);
        $output .= 'JsonResponseAsObj<pre>'. print_r($jsonResponseAsObj, true). '</pre>';

        $status= $jsonResponseAsObj->payment_result->response_status;
        $code= $jsonResponseAsObj->payment_result->response_code;
        $message= $jsonResponseAsObj->payment_result->response_message;

        $response= json_encode(['status'=>$status, 'code'=>$code, 'message'=>$message]);
        
        return response($response, 200)
            ->header('Content-Type', 'application/json');
    }

    private function callFollowupTran($tranType, $cart){
        $client = new Client([
            'base_uri' => config('app.paymentApiBaseUri'), // Base URI is used with relative requests
            'timeout'  => 2.0, // You can set any number of default request options.
            'verify'   => false, //disable SSL cerificate verification
        ]);

        $headers = ['Authorization' => config('app.gatewayServerKey') ];
        $requestBody= '{
            "profile_id":         '. config('app.profileId'). ',
            "tran_type":          "'. $tranType. '",
            "tran_class":         "ecom",
            "cart_description":   "Desc of the items/services",
            "cart_id":            "'. $cart->cart_id. '",
            "cart_currency":      "sar",
            "cart_amount":        '. $cart->total. ',
            "tran_ref":           "'. $cart->payment_tran_ref. '"
          }';

        $paymentRequest = new \GuzzleHttp\Psr7\Request('POST', 'request', $headers, $requestBody);
        return $client->send($paymentRequest, ['timeout' => 2]);        
    }


    private function callQueryTran($cart){
        $client = new Client([
            'base_uri' => config('app.paymentApiBaseUri'), // Base URI is used with relative requests
            'timeout'  => 2.0, // You can set any number of default request options.
//            'verify'   => false, //disable SSL cerificate verification
        ]);

        $headers = ['Authorization' => config('app.gatewayServerKey') ];
        $requestBody= '{
            "profile_id":         '. config('app.profileId'). ',
            "tran_ref":           "'. $cart->payment_tran_ref. '"
          }';

        $paymentRequest = new \GuzzleHttp\Psr7\Request('POST', 'query', $headers, $requestBody);
        return $client->send($paymentRequest, ['timeout' => 2]);        
    }

    /** 
     * update cart according to the payment final status
     */
    private static function updateCartCaptureDetails($response){
        $cartId= $response->cart_id;
        $status= $response->payment_result->response_status;
        $code= $response->payment_result->response_code;
        $message= $response->payment_result->response_message;
        DB::update(
            'UPDATE '. config('app.cartTable'). ' SET capture_resp_status = :status, capture_resp_code = :code, capture_resp_msg= :message,'
            . 'capture_updated_at= NOW() WHERE cart_id = :id',
                ['status'=> $status, 'code'=> $code , 'message'=> $message, 'id'=> $cartId]
            );
    }

}