<?php

interface Expression {
    public function interpret();
}

class Number implements Expression {
    private $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public function interpret() {
        return $this->value;
    }
}

class Add implements Expression {
    private $left;
    private $right;

    public function __construct(Expression $left, Expression $right) {
        $this->left = $left;
        $this->right = $right;
    }

    public function interpret() {
        return $this->left->interpret() + $this->right->interpret();
    }
}

class Subtract implements Expression {
    private $left;
    private $right;

    public function __construct(Expression $left, Expression $right) {
        $this->left = $left;
        $this->right = $right;
    }

    public function interpret() {
        return $this->left->interpret() - $this->right->interpret();
    }
}

function parseExpression($input) {
    $tokens = preg_split('/\s+/', trim($input));

    if (count($tokens) !== 3) {
        return "Invalid expression";
    }

    list($leftOperand, $operator, $rightOperand) = $tokens;

    if (!is_numeric($leftOperand) || !is_numeric($rightOperand)) {
        return "Invalid numbers";
    }

    $left = new Number($leftOperand);
    $right = new Number($rightOperand);

    switch ($operator) {
        case '+':
            return (new Add($left, $right))->interpret();
        case '-':
            return (new Subtract($left, $right))->interpret();
        default:
            return "Unsupported operator";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expression = $_POST['expression'] ?? '';
    echo parseExpression($expression);
}

