<?php
declare(strict_types=1);

use CodingLiki\CalculatorParser\Parser\CalculatorParser;
use CodingLiki\CalculatorParser\Parser\RuleCalculators\AssignmentExpressionCalculator;
use CodingLiki\CalculatorParser\Parser\RuleCalculators\AtomCalculator;
use CodingLiki\CalculatorParser\Parser\RuleCalculators\CodeCalculator;
use CodingLiki\CalculatorParser\Parser\RuleCalculators\CodeLineCalculator;
use CodingLiki\CalculatorParser\Parser\RuleCalculators\CodeLineSubrule1Calculator;
use CodingLiki\CalculatorParser\Parser\RuleCalculators\ExpressionCalculator;
use CodingLiki\CalculatorParser\Parser\RuleCalculators\MulDivPartCalculator;
use CodingLiki\CalculatorParser\Parser\RuleCalculators\MulExpressionCalculator;
use CodingLiki\CalculatorParser\Parser\RuleCalculators\PlusMinusPartCalculator;

include_once __DIR__."/vendor/CodingLiki/OoAutoloader/Autoloader.php";

$assignmentExpressionCalculator = new AssignmentExpressionCalculator();
$parser = new CalculatorParser(
    __DIR__.'/grammar/calculator.grt',
    __DIR__.'/grammar/calculator_new.grr.lrt',
    __DIR__.'/grammar/calculator_new.grr',
    [
        new ExpressionCalculator(),
        new AtomCalculator($assignmentExpressionCalculator),
        new MulDivPartCalculator(),
        new MulExpressionCalculator(),
        new PlusMinusPartCalculator(),
        new CodeLineSubrule1Calculator(),
        $assignmentExpressionCalculator,
        new CodeLineCalculator(),
        new CodeCalculator()
    ]
);

$script = '
    r = 35;
    pi = 3.1415926;
    b = 2 * pi*r;
';
$result = $parser->parse($script);
