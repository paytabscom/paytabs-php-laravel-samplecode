<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\DB;

class IpnRequest
{
    
    /**
     * validating a IPN basic web request is the same as validating a IPN default web request
     */
    public static function isValidIPNBasicRequest($request){
        self::isValidIPNRequest($request);
    }

    /**
     * verify that it is a valid callback request\IPN Default Web request
     */
    public static function isValidIPNRequest($request){
        $signature= $request->header('signature');
        $content= $request->getContent(); //get the request raw content

        $calculatedSignature = hash_hmac('sha256', $content, config('app.gatewayServerKey'));
        return (hash_equals($calculatedSignature, $signature ) === TRUE);
    }

}
