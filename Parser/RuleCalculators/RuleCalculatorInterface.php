<?php
declare(strict_types=1);

namespace CodingLiki\CalculatorParser\Parser\RuleCalculators;

use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstLeaf;
use CodingLiki\GrammarParser\Rule\Rule;

interface RuleCalculatorInterface
{
    public function calculate(CalculatedAstLeaf $leaf): void;

    public function acceptRule(string $ruleName): bool;
}