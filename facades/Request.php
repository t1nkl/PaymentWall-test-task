<?php

namespace App\Facades;

class Request
{
    /**
     * Fetch the request URI
     *
     * @return string
     */
    public static function uri()
    {
        return trim(
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
            '/'
        );
    }

    /**
     * Fetch the request method
     *
     * @return string
     */
    public static function method()
    {
        if (self::isAuthenticated(request_headers())) {
            $method = $_SERVER['REQUEST_METHOD'];

            if ($method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
                if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                    return 'DELETE';
                } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                    return 'PUT';
                } else {
                    throw new \RuntimeException('Unexpected Header', 400);
                }
            }

            return $method;
        }

        response(401);

        exit;
    }

    /**
     * Validate api key in headers
     *
     * @return bool
     */
    private static function isAuthenticated(array $headers)
    {
        if (array_key_exists('X-API-KEY', $headers) && null !== $headers['X-API-KEY']) {
            $api_key = decrypt($headers['X-API-KEY']);
            $time = explode('__', $api_key)[2];

            return $time >= time() - app('config.authorization')['api_waiting_time'];
        } else {
            return response(400);
        }
    }
}
