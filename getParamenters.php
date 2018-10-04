<?php

// 通过ReflectionMethod::getParameters() 方法返回ReflectionParmeter对象数组

$prod_class = new ReflectionClass('CdPrduct');
$method = $prod_class->getMethods("__construct");
$params = $method->getParameters();
foreach ($params as $param) {
    print argData($param) . "\n";
}
function argData (ReflectionParameter $arg) {
    $details = "";
    $declaringclass = $arg->getDeclaringClass();
    $name = $arg->getName();
    $class = $arg->getClass();
    $position = $arg->getPosition();
    $details .= "\$$name has position $position";
    if (! empty($class)) {
        $classname = $class->getName();
        $details .= "\$$name is passed by reference\n";
    }
    if ($arg->isDefaultValueAvailable()) {
        $def = $arg->getDefaultValue();
        $details .= "\$$name has default : $def\n";
    }
    return $details;
}