<?php

namespace App\Controllers;

use App\Facades\Validation;

class PaymentController
{
    /**
     * Data from input file_get_contents
     *
     * @var mixed
     */
    protected $request;

    /**
     * Create and set new controller request data
     *
     * @return void
     */
    public function __construct()
    {
        $this->request = file_get_contents('php://input', false, stream_context_get_default());
    }

    /**
     * Init validation given data in request
     *
     * @return response
     */
    public function index()
    {
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $content_type = $_SERVER['CONTENT_TYPE'];

            switch ($content_type) {
            case contains('application/json', $content_type):
                if (json_validate($this->request)) {
                    $data = json_decode($this->request, true);

                    foreach ($data as $key => $value) {
                        Validation::make($key, $value);
                    }
                } else {
                    return response(415);
                }

                break;
            case contains(['text/xml', 'application/xml'], $content_type):
                if (xml_validate($this->request)) {
                    $data = json_decode(json_encode((array)simplexml_load_string($this->request)), true);

                    foreach ($data as $key => $value) {
                        Validation::make($key, $value);
                    }
                } else {
                    return response(415);
                }

                break;
            default:
                return response(415);

                break;
            }

            header('Content-Type: application/json');

            return print json_encode(Validation::get());
        }

        return response(415);
    }
}
