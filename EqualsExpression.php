<?php

class EqualsExpression extends OperatorExpression {
    protected function doInterpret (InterpreterContext $context, $result_l, $result_r) {
        $context->replace($this, $result_l || $result_r);
    }
}
class BooleanAndExpression extends OperatorExpression {
    protected function doInterpret (InterpreterContext $context, $result_l, $result_r) {
        $context->replace($this, $result_l && $result_r);
    }
}
class BooleanAndExpression2 extends OperatorExpression {
    protected function doInterpret (InterpreterContext $context, $result_l, $result_r) {
        $context->replace($this, $result_l, $result_r);
    }
}
$context = new InterpreterContext();
$input = new VariableExpression('input');
$statement = new BooleanAndExpression(
    new EqualsExpression($input, new LiteralExpression('four')),
    new EqualsExpression($input, new LiteralExpression('4'))
);
foreach (array('four', '4', '52') as $val) {
    $input->setValue($val);
    print "$val:\n";
    $statement->interpret($context);
    if ($context->lookup($statement)) {
        print "top marks\n\n";
    } else {
        print "dunce hat on\n\n";
    }
}