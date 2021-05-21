<?php
declare(strict_types=1);
namespace CodingLiki\CalculatorParser\Parser\CalculatedTree;

use CodingLiki\LALR1Parser\AstTree\AstLeaf;

class CalculatedAstLeaf extends AstLeaf
{
    private bool $calculated = false;

    private mixed $calculatedResult = 0;
    /**
     * @return bool
     */
    public function isCalculated(): bool
    {
        return $this->calculated;
    }

    /**
     * @return mixed
     */
    public function getCalculatedResult(): mixed
    {
        return $this->calculatedResult;
    }

    /**
     * @param int|mixed $calculatedResult
     * @return CalculatedAstLeaf
     */
    public function setCalculatedResult(mixed $calculatedResult): CalculatedAstLeaf
    {
        $this->calculatedResult = $calculatedResult;
        return $this;
    }


}