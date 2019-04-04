<?php

class VariableExpression extends Expression {
    private $name;
    private $val;
    public function __construct ($name, $val = null) {
        $this->name = $name;
        $this->val = $val;
    }
    public function interpret (InterpreterContext $context) {
        if (! is_null($this->val)) {
            $context->replace($this, $this->val);
            $this->val = null;
        }
    }
    public function setValue ($value) {
        $this->val = $value;
    }
    public function getKey () {
        return $this->name;
    }
}
$context = new InterpreterContext();
$myvar = new VariableExpression('input', 'four');
$myvar->interpret($context);
print $context->lookup($myvar) . "\n";
// print: four
$newvar = new VariableExpression('input');
$newvar->interpret($context);
print $context->lookup($newvar);
// print: four
$myvar->setValue("five");
$myvar->interpret($context);
print $context->lookup($myvar);
// print: five
print $context->lookup($newvar);
// print: five