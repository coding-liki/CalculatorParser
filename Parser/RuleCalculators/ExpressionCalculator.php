<?php
declare(strict_types=1);

namespace CodingLiki\CalculatorParser\Parser\RuleCalculators;

use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstLeaf;

class ExpressionCalculator extends AbstractRuleCalculator
{
    protected array $acceptedRules = ['expression'];

    public function calculate(CalculatedAstLeaf $leaf): void
    {
        $children = $leaf->getChildren();

        $firstMulExpression = $children[0] ?? null;

        $firstChildIsMulExpression = $firstMulExpression instanceof CalculatedAstLeaf && $firstMulExpression->getName() === 'mulExpression';
        if ($firstChildIsMulExpression && count($children) === 1) {
            $leaf->setCalculatedResult($firstMulExpression->getCalculatedResult());
        } else {
            $parts = array_slice($children, 1);
            if ($firstChildIsMulExpression) {
                $result = $firstMulExpression->getCalculatedResult();
                foreach ($parts as $part) {
                    if ($part instanceof CalculatedAstLeaf && $part->getName() === 'plusMinusPart') {
                        $result += $part->getCalculatedResult();
                    }
                }
                $leaf->setCalculatedResult($result);
            }
        }
    }
}