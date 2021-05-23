<?php
declare(strict_types=1);

namespace CodingLiki\CalculatorParser\Parser\RuleCalculators;

use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstLeaf;

class CodeLineCalculator extends AbstractRuleCalculator
{
    protected array $acceptedRules = ['codeLine'];

    public function calculate(CalculatedAstLeaf $leaf): void
    {

    }
}