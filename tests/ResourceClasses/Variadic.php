<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 27.11.16
 * Time: 11:11
 */

namespace ClassGenerator\tests\ResourceClasses;


class Variadic {
    public $postfixes;

    public function __construct(...$postfixes) {
        $this->postfixes = $postfixes;
    }

    public function join($glue, ...$strs) {
        $result = '';
        foreach($strs as $i => $str) {
            if ($i) {
                $result .= $glue;
            }

            if (isset($this->postfixes[$i])) {
                $result .= $this->postfixes[$i];
            }

            $result .= $str;
        }

        return $glue;
    }
} 