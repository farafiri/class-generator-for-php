<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 30.01.16
 * Time: 14:05
 */

namespace ClassGenerator\Utils;


class Coder {
    const START_CHAR = 'a';
    public function encode($data) {
        $a = ord(static::START_CHAR);
        $output = '';
        $l = strlen($data);
        for($i = 0; $i < $l; $i++) {
            $charCode = ord($data[$i]);
            $output .= chr($a + floor($charCode / 16));
            $output .= chr($a + $charCode % 16);
        }

        return $output;
    }

    public function decode($str) {
        $a = ord(static::START_CHAR);
        $output = '';
        $l = strlen($str);
        for($i = 0; $i < $l; $i+=2) {
            $output .= chr((ord($str[$i]) - $a) * 16 + ord($str[$i + 1]) - $a);
        }

        return $output;
    }
} 