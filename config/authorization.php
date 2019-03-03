<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Api key
    |--------------------------------------------------------------------------
    | This value determines the api key and uses in encrypt and decrypt functions in Request validation
    */

    'api_key' => '1qaz2wsx3edc',

    /*
    |--------------------------------------------------------------------------
    | Date format
    |--------------------------------------------------------------------------
    | This value determines the date format for encrypt and decrypt functions
    */

    'api_date_format' => 'd-m-Y',

    /*
    |--------------------------------------------------------------------------
    | Api validation waiting time
    |--------------------------------------------------------------------------
    | This value determines how long encrypted uniq key can be valid in seconds
    */

    'api_waiting_time' => 30,
];
