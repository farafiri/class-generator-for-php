<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 19.01.14
 * Time: 16:53
 */

namespace ClassGenerator\tests\ResourceClasses;


class Serialize extends X implements \Serializable
{
    public function serialize() {
        return $this->a . ':' . $this->b;
    }
    public function unserialize($data) {
        $imploded = explode(':', $data);
        $this->a = $imploded[0];
        $this->b = $imploded[1];
    }
} 