<?php
declare(strict_types=1);

namespace CodingLiki\CalculatorParser\Parser\RuleCalculators;

use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstLeaf;

class CodeCalculator extends AbstractRuleCalculator
{
    protected array $acceptedRules = ['code'];

    public function calculate(CalculatedAstLeaf $leaf): void
    {
    }
}