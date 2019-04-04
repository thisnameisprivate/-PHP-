<?php

abstract class Expression {
    private static $keycount = 0;
    private $key;
    abstract function interpret (InterpreterContext $context);
    public function getKey () {
        if (! asset($this->key)) {
            self::$keycount ++;
            $this->key = self::$keycount;
        }
        return $this->key;
    }
}
class LiteralExpression extends Expression {
    private $value;
    public function __construct ($value) {
        $this->value = $value;
    }
    public function interpret(InterpreterContext $context) {
        $context->replace($this, $this->value);
    }
}
class InterpreterContext {
    private $expressionstore = array();
    public function replace (Expression $exp, $value) {
        $this->expressionstore[$exp->getKey()] = $value;
    }
    public function lookup (Expression $exp) {
        return $this->expressionstore[$exp->getKey()];
    }
}
$context = new InterpreterContext();
$literal = new LiteralExpression('four');
$literal->interpret($context);
print $context->lookup($literal) . "\n";

// result:
// four