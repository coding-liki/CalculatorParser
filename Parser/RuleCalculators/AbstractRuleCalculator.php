<?php
declare(strict_types=1);

namespace CodingLiki\CalculatorParser\Parser\RuleCalculators;

use CodingLiki\GrammarParser\Rule\Rule;

abstract class AbstractRuleCalculator implements RuleCalculatorInterface
{
    protected array $acceptedRules = [];

    public function acceptRule(string $ruleName): bool
    {
        return in_array($ruleName, $this->acceptedRules, true);
    }
}