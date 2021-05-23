<?php
declare(strict_types=1);

namespace CodingLiki\CalculatorParser\Parser\RuleCalculators;

use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstLeaf;
use CodingLiki\GrammarParser\Token\Token;

class AtomCalculator extends AbstractRuleCalculator
{

    public function __construct(private AssignmentExpressionCalculator $assignmentExpressionCalculator)
    {
    }

    protected array $acceptedRules = ['atom'];

    public function calculate(CalculatedAstLeaf $leaf): void
    {
        $children = $leaf->getChildren();
        if (count($children) === 1) {
            /** @var Token $numberToken */
            $numberToken = $children[0];
            switch ($numberToken->getType()) {
                case 'FLOAT_NUM':
                    $leaf->setCalculatedResult((float)$numberToken->getValue());
                    break;
                case 'INT_NUM':
                    $leaf->setCalculatedResult((int)$numberToken->getValue());
                    break;
                case 'VARIABLE':
                    $leaf->setCalculatedResult(
                        $this->assignmentExpressionCalculator->getVariable($numberToken->getValue())
                    );
            }
        } elseif (count($children) === 3) {

            [$leftToken, $expression, $rightToken] = $children;

            if (!$leftToken instanceof Token || !$rightToken instanceof Token || !$expression instanceof CalculatedAstLeaf || $leftToken->getType() !== 'L_P' || $rightToken->getType() !== 'R_P' || $expression->getName() !== 'expression') {
                throw new Error("error in expression in brackets");
            }

            $leaf->setCalculatedResult($expression->getCalculatedResult());
        }
    }
}