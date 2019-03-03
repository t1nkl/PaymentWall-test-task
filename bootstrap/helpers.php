<?php

use App\Facades\App;
use App\Facades\Response;

/**
 * Lite version of dd helper like in laravel
 *
 * @param mixed $value
 *
 * @return print_r
 */
function dd($value)
{
    array_map(function ($value) {
        print_r($value);
    }, func_get_args());

    die;
}

/**
 * Simple for calling App facade get config data
 *
 * @param string $key
 *
 * @return array
 */
function app($key)
{
    return App::get($key);
}

/**
 * Simple for calling Response facade make header
 *
 * @param int|500 $status
 * @param string|null $data
 *
 * @return header
 */
function response($status = 500, $data = null)
{
    return Response::make($status, $data);
}

/**
 * Something like headers refactoring:)
 *
 * @return array
 */
function request_headers()
{
    $headers = [];
    $protocol = '/\AHTTP_/';

    foreach ($_SERVER as $key => $value) {
        if (preg_match($protocol, $key)) {
            $header_key = preg_replace($protocol, '', $key);
            $exploaded_header_key = [];
            $exploaded_header_key = explode('_', $header_key);

            if (count($exploaded_header_key) > 0 and strlen($header_key) > 2) {
                foreach ($exploaded_header_key as $exploaded_key => $exploaded_val) {
                    $exploaded_header_key[$exploaded_key] = ucfirst($exploaded_val);
                }

                $header_key = implode('-', $exploaded_header_key);
            }

            $headers[$header_key] = $value;
        }
    }

    return $headers;
}

/**
 * Check if string contains a specific word or words
 *
 * @param string|array $needle
 * @param string $haystack
 *
 * @return bool
 */
function contains($needle, $haystack, $offset = 0)
{
    if (is_array($needle)) {
        foreach ($needle as $value) {
            if (strpos($haystack, $value, $offset) !== false) {
                return true;
            }
        }

        return false;
    }

    return strpos($haystack, $needle) !== false;
}

/**
 * Simple validation for xml
 *
 * @param string $value
 *
 * @return bool
 */
function xml_validate($value)
{
    if (is_string($value)) {
        $simplexml = @simplexml_load_string($value);

        if (!$simplexml) {
            return false;
        }

        return true;
    }

    return false;
}

/**
 * Simple validation for json
 *
 * @param string $value
 *
 * @return bool
 */
function json_validate($value)
{
    if (is_string($value)) {
        json_decode($value);

        return (json_last_error() == JSON_ERROR_NONE);
    }

    return false;
}

/**
 * Example function for encrypting data for X-API-KEY header
 *
 * @param string|null $value
 *
 * @return string
 */
function encrypt($value = null)
{
    if (is_null($value)) {
        $value = app('config.authorization')['api_key'];
        $value .= '__' . date(app('config.authorization')['api_date_format']);
        $value .= '__' . time();
    }

    $api_key = app('config.authorization')['api_key'];

    $iv_length = openssl_cipher_iv_length($rijndael = 'AES-256-CBC');

    $pseudo_bytes = openssl_random_pseudo_bytes($iv_length);

    $encrypt = openssl_encrypt($value, $rijndael, $api_key, $options = OPENSSL_RAW_DATA, $pseudo_bytes);

    $hash = hash_hmac('sha256', $encrypt, $api_key, $as_binary = true);

    return base64_encode($pseudo_bytes . $hash . $encrypt);
}

/**
 * Function for decrypting data from X-API-KEY header
 *
 * @param string $value
 *
 * @return string
 */
function decrypt($value)
{
    $api_key = app('config.authorization')['api_key'];

    $key = base64_decode($value);

    $iv_length = openssl_cipher_iv_length($rijndael = 'AES-256-CBC');

    $pseudo_bytes = substr($key, 0, $iv_length);

    $hmac = substr($key, $iv_length, $sha2len = 32);

    $encrypt = substr($key, $iv_length + $sha2len);

    $plaintext = openssl_decrypt($encrypt, $rijndael, $api_key, $options = OPENSSL_RAW_DATA, $pseudo_bytes);

    $hash_hmac = hash_hmac('sha256', $encrypt, $api_key, $as_binary = true);

    if (hash_equals($hmac, $hash_hmac)) {
        return $plaintext;
    }
}
