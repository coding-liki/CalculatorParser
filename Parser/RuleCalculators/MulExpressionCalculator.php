<?php
declare(strict_types=1);

namespace CodingLiki\CalculatorParser\Parser\RuleCalculators;

use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstLeaf;

class MulExpressionCalculator extends AbstractRuleCalculator
{
    protected array $acceptedRules = ['mulExpression'];

    public function calculate(CalculatedAstLeaf $leaf): void
    {
        $children = $leaf->getChildren();

        $firstAtom = $children[0] ?? null;

        $firstChildIsAtom = $firstAtom instanceof CalculatedAstLeaf && $firstAtom->getName() === 'atom';
        if ($firstChildIsAtom && count($children) === 1) {
            $leaf->setCalculatedResult($firstAtom->getCalculatedResult());
        } else {
            $parts = array_slice($children, 1);
            if ($firstChildIsAtom) {
                $result = $firstAtom->getCalculatedResult();
                foreach ($parts as $part) {
                    if ($part instanceof CalculatedAstLeaf && $part->getName() === 'mulDivPart') {
                        $result *= $part->getCalculatedResult();
                    }
                }
                $leaf->setCalculatedResult($result);
            }
        }
    }
}