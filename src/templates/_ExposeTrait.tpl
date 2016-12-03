<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : '';
  $baseClassExploded = explode('\\', $baseClass);
  $exposedGetter = 'get' . ucfirst(array_pop($baseClassExploded));
  $interfaceLiteral = 'Interface';
  if (substr($exposedGetter, -strlen($interfaceLiteral)) === $interfaceLiteral) {
      $exposedGetter = substr($exposedGetter, 0, -strlen($interfaceLiteral));
  }
?>

trait {{newClassName}}
{
{{method}}
<?php if (substr($methodName, 0, 2) === '__' ||
          $methodName == 'getId' ||
          $methodName == 'setId') continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}}){{returnType}}
    {
        return $this->{{exposedGetter}}()->{{methodName}}({{parameters}});
    }

{{\method}}
}