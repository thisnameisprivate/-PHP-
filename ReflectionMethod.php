<?php

// 使用 ReflectionClass::getMethods() 来获取对象的所有方法并用ReflectionMethod来检查

$prod_class = new ReflectionClass('CdProduct');
$methods = $prod_class->getMethods();
foreach ($methods as $method) {
    print $methodData($method);
    print "\n---\n";
}
function methodData (ReflectionMethod $method) {
    $details = "";
    $name = $method->getName();
    if ($method->isUserDefined()) {
        $details .= "$name is user defined\n";
    }
    if ($method->isAbstract()) {
        $details .= "$name is abstract\n";
    }
    if ($method->isPublic()) {
        $details .= "$name is public\n";
    }
    if ($method->isProtected()) {
        $details .= "$name is protected\n";
    }
    if ($method->isPrivate()) {
        $details .= "$name is private\n";
    }
    if ($method->isStatic()) {
        $details .= "$name is static\n";
    }
    if ($method->isFinal()) {
        $details .= "$name is final\n";
    }
    if ($metod->isConstructor()) {
        $details .= "$name is constructor\n";
    }
    if ($method->returnsReference()) {
        $details .= "$name returns a reference (as opposed to a value)\n";
    }
    return $details;
}