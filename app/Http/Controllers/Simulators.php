<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Route;

class Simulators extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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

}
