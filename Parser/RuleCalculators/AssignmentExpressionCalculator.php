<?php
declare(strict_types=1);

namespace CodingLiki\CalculatorParser\Parser\RuleCalculators;

use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstLeaf;
use CodingLiki\GrammarParser\Token\Token;

class AssignmentExpressionCalculator extends AbstractRuleCalculator
{
    protected array $acceptedRules = ['assignmentExpression'];

    private array $variables = [];
    public function calculate(CalculatedAstLeaf $leaf): void
    {
        [$variableToken, $equalsToken, $valueLeaf] = $leaf->getChildren();

        if($variableToken instanceof Token && $equalsToken instanceof Token && $valueLeaf instanceof CalculatedAstLeaf){
            $this->setVariable($variableToken->getValue(), $valueLeaf->getCalculatedResult());
            $leaf->setCalculatedResult($valueLeaf->getCalculatedResult());
        }
    }


    public function setVariable(string $name, float $value): self
    {
        $this->variables[$name] = $value;

        return $this;
    }

    public function getVariable(string $name): ?float
    {
        return $this->variables[$name] ?? null;
    }
}