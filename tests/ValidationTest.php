<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Facades\Validation;
use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{
    public function test_correct_json_validating_logic()
    {
        $request = '{
            "credit_card": {
                "credit_card_number": "5105 1051 0510 5100",
                "expiration_date": "01/32",
                "cvv2": "123",
                "email": "qwerty@mail.com"
            },
            "mobile": {
                "phone_number": "+38 012 123 12 12"
            }
        }';

        if (json_validate($request)) {
            $data = json_decode($request, true);

            foreach ($data as $key => $value) {
                Validation::make($key, $value);
            }
        }

        $this->assertSame('{"valid":"true"}', json_encode(Validation::get()));
    }

    public function test_wrong_json_validating_logic()
    {
        $request = '{
            "credit_card": {
                "credit_card_number": "5105 1051 10 5100",
                "expiration_date": "01/2",
                "email": "qwerty@mail.com"
            },
            "mobile": {
                "phone_number": "+38 012 123 12 12"
            }
        }';

        if (json_validate($request)) {
            $data = json_decode($request, true);

            foreach ($data as $key => $value) {
                Validation::make($key, $value);
            }
        }

        $this->assertSame('{"valid":"false","error_code":["Invalid credit card number!","Invalid expiration date!","CVV2 code is required!"]}', json_encode(Validation::get()));
    }

    public function test_correct_xml_validating_logic()
    {
        $request = '<?xml version="1.0" encoding="UTF-8"?>
        <root>
            <credit_card>
                <credit_card_number>5105 1051 0510 5100</credit_card_number>
                <cvv2>123</cvv2>
                <email>qwerty@mail.com</email>
                <expiration_date>01/32</expiration_date>
            </credit_card>
            <mobile>
                <phone_number>+38 012 123 12 12</phone_number>
            </mobile>
        </root>';

        if (xml_validate($request)) {
            $data = json_decode(json_encode((array)simplexml_load_string($request)), true);

            foreach ($data as $key => $value) {
                Validation::make($key, $value);
            }
        }

        $this->assertSame('{"valid":"true"}', json_encode(Validation::get()));
    }

    public function test_wrong_xml_validating_logic()
    {
        $request = '<?xml version="1.0" encoding="UTF-8"?>
        <root>
            <credit_card>
                <email>qwertymail.com</email>
                <expiration_date>01/32</expiration_date>
            </credit_card>
            <mobile>
                <phone_number>+38 012  12</phone_number>
            </mobile>
        </root>';

        if (xml_validate($request)) {
            $data = json_decode(json_encode((array)simplexml_load_string($request)), true);

            foreach ($data as $key => $value) {
                Validation::make($key, $value);
            }
        }

        $this->assertSame('{"valid":"false","error_code":{"0":"Credit card number is required!","2":"CVV2 code is required!","3":"Invalid Email!","4":"Invalid Phone number!"}}', json_encode(Validation::get()));
    }
}
