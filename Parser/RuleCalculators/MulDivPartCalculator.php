<?php
declare(strict_types=1);

namespace CodingLiki\CalculatorParser\Parser\RuleCalculators;

use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstLeaf;
use CodingLiki\GrammarParser\Token\Token;

class MulDivPartCalculator extends AbstractRuleCalculator
{
    protected array $acceptedRules = ['mulDivPart'];

    public function calculate(CalculatedAstLeaf $leaf): void
    {
        $children = $leaf->getChildren();
        if (count($children) === 2) {
            [$signToken, $numberLeaf] = $children;
            if ($signToken instanceof Token && $numberLeaf instanceof CalculatedAstLeaf) {
                switch ($signToken->getType()) {
                    case 'MUL':
                        $leaf->setCalculatedResult($numberLeaf->getCalculatedResult());
                        break;
                    case 'DIV':
                        $leaf->setCalculatedResult(1 / $numberLeaf->getCalculatedResult());
                        break;
                }
            }
        }
    }
}