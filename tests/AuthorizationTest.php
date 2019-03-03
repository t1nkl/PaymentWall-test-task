<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use PHPUnit\Framework\TestCase;

class AuthorizationTest extends TestCase
{
    public function test_original_authorization_check_logic()
    {
        $api_key = decrypt(encrypt());
        $time = explode('__', $api_key)[2];

        $test = $time >= time() - 30;

        $this->assertTrue($test);
    }

    public function test_modified_authorization_check_logic()
    {
        $encrypt_key = encrypt();

        sleep(2);

        $decrypt_key = decrypt($encrypt_key);
        $time = explode('__', $decrypt_key)[2];

        $test = $time >= time() - 3;

        $this->assertTrue($test);
    }
}
