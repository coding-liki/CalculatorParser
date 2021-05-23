<?php
declare(strict_types=1);

namespace CodingLiki\CalculatorParser\Parser\RuleCalculators;

use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstLeaf;

class CodeLineSubrule1Calculator extends AbstractRuleCalculator
{
    protected array $acceptedRules = ['codeLine_subrule_1'];

    public function calculate(CalculatedAstLeaf $leaf): void
    {
        $expressionLeaf = $leaf->getChildren()[0];

        if (($expressionLeaf instanceof CalculatedAstLeaf) && $expressionLeaf->getName() === 'assignmentExpression') {
            $variableToken = $expressionLeaf->getChildren()[0];
            echo $variableToken->getValue()." = " . $expressionLeaf->getCalculatedResult() . "\n";
        }
    }
}