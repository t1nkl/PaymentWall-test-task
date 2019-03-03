<?php

namespace App\Facades\Traits;

trait ValidationHelperTrait
{
    // Made from example code from https://en.wikipedia.org/wiki/Luhn_algorithm
    public function luhnCheckHelper($number)
    {
        if (empty($number)) {
            return 'Credit card number can\'t be empty!';
        }

        // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
        $number = preg_replace('/\D/', '', $number);

        // Set the string length and parity
        $number_length = strlen($number);
        $parity = $number_length % 2;

        // Loop through each digit and do the maths
        $total = 0;

        for ($i = 0; $i < $number_length; $i++) {
            $digit = $number[$i];
            // Multiply alternate digits by two
            if ($i % 2 == $parity) {
                $digit *= 2;
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            // Total up the digits
            $total += $digit;
        }

        // If the total mod 10 equals 0, the number is valid
        return ($total % 10 == 0) ? false : 'Invalid credit card number!';
    }

    public function expirationDateHelper($date)
    {
        if (empty($date)) {
            return 'Expiration date can\'t be empty!';
        }

        if (preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $date, $output_array)) {
            $expires = strtotime('20' . $output_array[2] . $output_array[1]);
            $now = strtotime(date('Ym'));

            if ($expires < $now) {
                return 'Expiration date can\'t be older then now!';
            }

            return false;
        }

        return 'Invalid expiration date!';
    }

    public function cvvHelper($code)
    {
        if (empty($code)) {
            return 'CVV2 code can\'t be empty!';
        }

        if ((strlen($code) == 3)
            && (strspn($code, '0123456789') == 3)
        ) {
            return false;
        }

        return 'Invalid CVV2 code!';
    }

    public function emailHelper($email)
    {
        if (empty($email)) {
            return 'Email can\'t be empty!';
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
            return false;
        }

        return 'Invalid Email!';
    }

    public function phoneHelper($phone)
    {
        $phone = (int)filter_var($phone, FILTER_SANITIZE_NUMBER_INT);

        if (preg_match('/^\+380\d{3}\d{2}\d{2}\d{2}$/', '+' . $phone)) {
            return false;
        }

        return 'Invalid Phone number!';
    }
}
