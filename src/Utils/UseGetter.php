<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 17.12.13
 * Time: 09:53
 */

namespace ClassGenerator\Utils;


class UseGetter
{
    protected $uses = array();

    public function getUses($className)
    {
        if (!isset($this->uses[$className])) {
            $this->buildForClass($className);
        }

        return $this->uses[$className];
    }

    protected function buildForClass($className)
    {
        $r = new \ReflectionClass($className);
        $file = new \SplFileObject($r->getFileName());
        $classLineNumber = $r->getStartLine();
        $uses = array();
        for($i = 0; $i < $classLineNumber; $i++) {
            $line = $file->fgets();
            if (preg_match('/^\s*use\s([a-zA-Z_][a-zA-Z0-9_\\\\]*)(\sas\s([a-zA-Z_][a-zA-Z0-9_]*))?/i', $line, $match)) {
                $usedClassName = $match[1];
                if (isset($match[3])) {
                    $classAlias = $match[3];
                } else {
                    $explodedClassName = explode('\\', $usedClassName);
                    $classAlias = $explodedClassName[count($explodedClassName) - 1];
                }
                $uses[$classAlias] = $usedClassName;
            }
        }

        $this->uses[$className] = $uses;
    }
}