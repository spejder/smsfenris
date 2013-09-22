<?php
/**
 * User: jot
 * Date: 22-09-13
 * Time: 16:58
 */

function getDanishPhoneNumber($string) {
    $pattern = "/^(\+45|0045|45)?([2-6][0-9]{7})$/xS";

    if (!preg_match($pattern, $string, $matches))
        return null;

    return $matches[2];
}