<?php

namespace App\Facades;

class Response
{
    /**
     * Send header
     *
     * @param int|500 $status
     * @param array|stirng $data
     */
    public static function make($status = 500, $data = null)
    {
        header('HTTP/1.1 ' . $status . ' ' . self::requestStatus($status));

        if (!is_null($data)) {
            return json_encode($data);
        }
    }

    /**
     * List of header codes
     *
     * @param int $code
     */
    private static function requestStatus($code)
    {
        $status = [
            200 => 'OK',
            202 => 'Accepted',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            415 => 'Unsupported Media Type',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
        ];

        return ($status[$code]) ? $status[$code] : $status[500];
    }
}
