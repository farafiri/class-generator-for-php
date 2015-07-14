<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : '';
  $baseClassExploded = explode('\\', $baseClass);
  $exposed = ucfirst(array_pop($baseClassExploded));
  $interfaceLiteral = 'Interface';
  $prefix = 'cgExposed';
  if (substr($exposed, -strlen($interfaceLiteral)) === $interfaceLiteral) {
      $exposed = substr($exposed, 0, -strlen($interfaceLiteral));
  }

  if (!empty($methods)) {
      $exposed = $methods;
  }
?>

trait {{newClassName}}
{
    protected function {{prefix}}{{exposed}}($methodName, $params) {
        $o = $this->{{prefix}}Get{{exposed}}($methodName, $params);
        if ($o) {
            return $this->{{prefix}}Map{{exposed}}($methodName, $params, call_user_func_array(array($o, $methodName), $params));
        } else {
            return $this->{{prefix}}OnEmpty{{exposed}}($methodName, $params);
        }
    }

    protected function {{prefix}}Get{{exposed}}($methodName, $params) {
        return $this->get{{exposed}}();
    }

    protected function {{prefix}}Map{{exposed}}($methodName, $params, $actualResult) {
        return $actualResult;
    }

    protected function {{prefix}}OnEmpty{{exposed}}($methodName, $params) {
        return null;
    }

{{method}}
<?php if (substr($methodName, 0, 2) === '__') continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}})
    {
        return $this->{{prefix}}{{exposed}}('{{methodName}}', array({{parameters}}));
    }

{{\method}}
}