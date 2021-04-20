<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Route;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $serverKey = "SDJNNWHNZD-JBJ9WM9JMH-2KWJHWDLMW";

    /**
     * Show the profile for a given user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('index', [
            'user' => 'test'
        ]);
    }

    /**
     * 
     * @return \Illuminate\View\View
     */
    public function initiateHostedPayment()
    {
//        return response('Hello World', 200)
//            ->header('Content-Type', 'text/plain');
        $client = new Client([
            'base_uri' => 'https://secure.paytabs.sa/payment/', // Base URI is used with relative requests
            'timeout'  => 2.0, // You can set any number of default request options.
            'verify'   => false, //disable SSL cerificate verification
        ]);

        $headers = ['Authorization' => $this->serverKey];
        $requestBody= '{
            "profile_id":         64594,
            "tran_type":          "sale",
            "tran_class":         "ecom",
            "cart_description":   "Desc of the items/services",
            "cart_id":            "Invoice1",
            "cart_currency":      "sar",
            "cart_amount":        1,
            "callback":           "https://webhook.site/5727c9aa-3417-4ce6-926c-c1cc5958ec02",
            "return":             "https://webhook.site/5727c9aa-3417-4ce6-926c-c1cc5958ec02"
          }';


        $request = new \GuzzleHttp\Psr7\Request('POST', 'request', $headers, $requestBody);
        $response = $client->send($request, ['timeout' => 2]);
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
            //show the response returned by the callback request verifier: verifyCallbackRequest
            $this->simulateCallbackRequest($requestSignature, $requestContent);
        }
        //if return chosen: simulate the request created by paytabs generate as a return (and caught by webhook.io for us)
        elseif($requestequestType == 'return'){            
            //show the response returned by the callback request verifier: verifyReturnRequest
            $this->simulateReturnRequest($requestSignature, $requestContent);
        }

    }

    /**
     * 
     */
    private function simulateCallbackRequest($signature, $content) {
        
        $baseUri= url(''). '/';
        $route= Route::getRoutes()->getByName('verify_callback_request');
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
    public function verifyCallbackRequest(Request $request){

        $signature= $request->header('signature');
        $content= $request->getContent(); //get the request raw content

        
        // Generate array from the request JSON content
        $contentAsArray= json_decode($content, 1);
        //echo '<br />array:<br /><pre>';print_r($contentAsArray); echo '</pre><br />';

        // Ignore empty values fields
        $signature_fields = array_filter($contentAsArray);

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
     * 
     */
    private function simulateReturnRequest($signature, $content) {
        
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
     * Show the profile for a given user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return view('user.profile', [
            'user' => User::findOrFail($id)
        ]);
    }
    
}
