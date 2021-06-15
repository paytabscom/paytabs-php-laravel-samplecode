<?php

/*
 * This file is part of paytabs.
 *
 * (c) Walaa Elsaeed <w.elsaeed@paytabs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
     |--------------------------------------------------------------------------
     | Merchant profile id
     |--------------------------------------------------------------------------
     |
     | Your merchant profile id , you can find the profile id on your PayTabs Merchant’s Dashboard- profile.
     |
     */

    'profile_id' => env('paytabs_profile_id', '64594'),

    /*
   |--------------------------------------------------------------------------
   | Server Key
   |--------------------------------------------------------------------------
   |
   | You can find the Server key on your PayTabs Merchant’s Dashboard - Developers - Key management.
   |
   */

    'server_key' => env('paytabs_server_key', 'S2JNNWHNZG-JB99JMHHR2-WHKDJTGL69'),

    /*
   |--------------------------------------------------------------------------
   | Currency
   |--------------------------------------------------------------------------
   |
   | The currency you registered in with PayTabs account
     you must pass value from this array ['AED','EGP','SAR','OMR','JOD','US']
   |
   */

    'currency' => env('paytabs_currency', 'SAR'),


    /*
   |--------------------------------------------------------------------------
   | Region
   |--------------------------------------------------------------------------
   |
   | The region you registered in with PayTabs
     you must pass value from this array ['ARE','EGY','SAU','OMN','JOR','GLOBAL']
   |
   */

    'region' => env('paytabs_region', 'SAU'),

];
