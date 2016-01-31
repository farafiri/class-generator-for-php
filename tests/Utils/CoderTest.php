<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 30.01.16
 * Time: 14:12
 */

class CoderTest extends \PHPUnit_Framework_TestCase {
    /**
     * @param $data         data to code encode / decode (string)
     * @param string $coder coder class or instance
     */
    protected function check($data, $coder = 'ClassGenerator\Utils\Coder') {
        if (is_string($coder)) {
            $coder = new $coder();
        }

        $coded = $coder->encode($data);
        $encoded = $coder->decode($coded);
        $this->assertEquals($data, $encoded);
        $this->assertRegExp('/^[A-Za-z_]?[A-Za-z_0-9]*$/', $coded);
    }

    public function testUnderscoreCoding() {
        $this->check('Lorem_Ipsum_Dolor_Sit_Emet');
        $this->check('_Lorem_');
        $this->check('__Lorem__Ipsum___dolor__');
    }

    public function testBackslashCoding() {
        $this->check('Lorem\\Ipsum\\Dolor\\Sit\\Emet');
        $this->check('\\Lorem\\');
        $this->check('\\\\Lorem\\\\Ipsum\\\\\\dolor\\\\');
    }

    public function testBackslashAndUnderscoreCoding() {
        $this->check('\\_\\');
        $this->check('\\\\___\\\\');
        $this->check('_\\_');
        $this->check('__\\\\__');
    }
}