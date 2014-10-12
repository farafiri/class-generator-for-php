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
<?php if (substr($methodName, 0, 2) === '__') continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}})
    {
        return $this->{{exposedGetter}}()->{{methodName}}({{parameters}});
    }

{{\method}}
}