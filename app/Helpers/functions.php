<?php

/**
 * This function file is loaded by default and is used to bride dependencis that might be missing
 */

/**
 * Fallback (Do not remove function_exists): Google uses functions that are not particallary
 * supported by PHP in their GA4 SDK.
 * @param  float   $num1  compare1
 * @param  float   $num2  compare2
 * @param  int      $scale scalar value
 * @return 0 if the two operands are equal, 1 if the num1 is larger than the num2, -1 otherwise.
 */
if (!function_exists("bccomp")) {
    function bccomp(float $number1, float $number2, int $scale = 0)
    {
        $num1 = (float)number_format($number1, $scale, "", "");
        $num2 = (float)number_format($number2, $scale, "", "");
        if ($num1 > $num2) {
            return 1;
        }
        if ($num1 < $num2) {
            return -1;
        }
        return 0;
    }
}