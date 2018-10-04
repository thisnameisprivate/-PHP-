<?php

/*
 *  ReflectionClass 接收一个类名,返回给定类的所有信息和方法.
 * */

// 检查类
function classData ( ReflectionClass $class ) {
    $details = '';
    $name = $class->getName();
    if ( $class->isUserDefined() ) {
        $details .= "$name is user defined";
    }
    if ( $class->isInstance() ) {
        $details .= "$name is built-in\n";
    }
    if ( $class->isInterface() ) {
        $details .= "$name is interface\n";
    }
    if ( $class->isAbstract() ) {
        $details .= "$name is an abstract class\n";
    }
    if ( $class->isFinal() ) {
        $details .= "$name is a final class\n";
    }
    if ( $class->isInstantiable() ) {
        $details .= "$name can be instantiated\n";
    } else {
        $details .= "$name can not be instantiated\n";
    }
    return $details;
}
$prod_class = new ReflectionClass( 'CdProduct' );
print classData( $prod_class );
// 利用ReflectionClass来获取源码的代码实例
class ReflectionClass {
    static function getClassSource ( ReflectionClass $class) {
        $path = $class->getFileName();
        $lines = @file( $path );
        $from = $class->getStartLine();
        $to = $class->getEndLine();
        $len = $to-$from+1;
        return implode( array_slice( $lines, $from-1, $len));
    }
}
print ReflectionUtil::getClassSource( new ReflectionClass( 'CdProduct' ) );