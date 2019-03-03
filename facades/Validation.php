<?php

namespace App\Facades;

use App\Facades\Traits\ValidationHelperTrait;

class Validation
{
    use ValidationHelperTrait;

    /**
     * All validation keys
     *
     * @var array
     */
    protected static $response = [];

    /**
     * Load and call the relevant controller action.
     *
     * @param string $action
     * @param array $value
     */
    public static function make(string $action, $value)
    {
        $validation = new static;

        $action = str_replace('_', '', ucwords($action, ' /_'));

        if (method_exists($validation, $action)) {
            $validation->$action($value);
        }
    }

    /**
     * Retrieve a value from the response
     *
     * @return array
     */
    public static function get()
    {
        $response = static::$response;

        static::$response = [];

        if (empty($response)) {
            return [
                'valid' => 'false',
                'error_code' => 'Invalid validation data!',
            ];
        }

        $response['error_code'] = array_filter($response['error_code']);

        if (empty($response['error_code'])) {
            http_response_code(200);

            return ['valid' => 'true'];
        }

        $response['valid'] = 'false';

        http_response_code(422);

        return array_reverse($response);
    }

    /**
     * This is validation methods
     *
     * Since we know the data and not a lot of them I did it
     * In the future, when increasing the data, it will be necessary to refactor
     */
    private function CreditCard($value)
    {
        if (array_key_exists('credit_card_number', $value)) {
            static::$response['error_code'][] = $this->luhnCheckHelper($value['credit_card_number']);
        } else {
            static::$response['error_code'][] = 'Credit card number is required!';
        }

        if (array_key_exists('expiration_date', $value)) {
            static::$response['error_code'][] = $this->expirationDateHelper($value['expiration_date']);
        } else {
            static::$response['error_code'][] = 'Expiration date is required!';
        }

        if (array_key_exists('cvv2', $value)) {
            static::$response['error_code'][] = $this->cvvHelper($value['cvv2']);
        } else {
            static::$response['error_code'][] = 'CVV2 code is required!';
        }

        if (array_key_exists('email', $value)) {
            static::$response['error_code'][] = $this->emailHelper($value['email']);
        } else {
            static::$response['error_code'][] = 'Email is required!';
        }
    }

    private function Mobile($value)
    {
        if (array_key_exists('phone_number', $value)) {
            static::$response['error_code'][] = $this->phoneHelper($value['phone_number']);
        } else {
            static::$response['error_code'][] = 'Phone number is required!';
        }
    }
}
