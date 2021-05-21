<?php
declare(strict_types=1);

namespace CodingLiki\CalculatorParser\Parser;

use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstLeaf;
use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstTree;
use CodingLiki\GrammarParser\GrammarRuleParser;
use CodingLiki\GrammarParser\Token\GrammarTokenParser;
use CodingLiki\GrammarParser\Token\Token;
use CodingLiki\LALR1Parser\Parser\LALR1Parser;
use CodingLiki\LALR1Parser\TableReader\CsvTableReader;
use CodingLiki\LALRLexer\Lexer\LALRLexer;
use Error;

class CalculatorParser
{
    public const RULE_TO_METHOD = [
        'expression' => 'calculateExpression',
        'plusMinusPart' => 'calculatePlusMinusPart',
        'mulExpression' => 'calculateMulExpression',
        'mulDivPart' => 'calculateMulDivPart',
        'atom' => 'calculateAtom',
    ];

    public function __construct(private string $tokensFile, private string $tableFile, private string $rulesFile)
    {
    }

    public function parse(string $source): float
    {
        $lexer = new LALRLexer(GrammarTokenParser::parse(file_get_contents($this->tokensFile)));
        $tableReader = new CsvTableReader();
        $parser = new LALR1Parser(
            $tableReader->read(file_get_contents($this->tableFile)),
            GrammarRuleParser::parse(file_get_contents($this->rulesFile))
        );

        $tokens = $lexer->parseSrc($source);
        $tree = $parser->parse($tokens);

        $calculatedTree = CalculatedAstTree::buildFromAstTree($tree);

        return $this->calculateTree($calculatedTree);

    }

    private function calculateTree(CalculatedAstTree $calculatedTree): float
    {
        $this->calculateLeaf($calculatedTree->getChildren()[0]);

        return $calculatedTree->getChildren()[0]->getCalculatedResult();
    }

    private function calculateLeaf(CalculatedAstLeaf $leaf)
    {
        foreach ($leaf->getChildren() as $child) {
            if (($child instanceof CalculatedAstLeaf) && !$child->isCalculated()) {
                $this->calculateLeaf($child);
            }
        }

        $method = self::RULE_TO_METHOD[$leaf->getName()];
        $this->$method($leaf);
    }

    public function calculateAtom(CalculatedAstLeaf $leaf)
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
                default:
                    throw new Error("unknown atom type " . $numberToken->getType());
            }
        } elseif (count($children) === 3) {

            [$leftToken, $expression, $rightToken] = $children;

            if (!$leftToken instanceof Token || !$rightToken instanceof Token || !$expression instanceof CalculatedAstLeaf || $leftToken->getType() !== 'L_P' || $rightToken->getType() !== 'R_P' || $expression->getName() !== 'expression') {
                throw new Error("error in expression in brackets");
            }

            $leaf->setCalculatedResult($expression->getCalculatedResult());
        }
    }

    public function calculateMulExpression(CalculatedAstLeaf $leaf)
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

    public function calculatePlusMinusPart(CalculatedAstLeaf $leaf)
    {
        $children = $leaf->getChildren();
        if (count($children) === 2) {
            [$signToken, $numberLeaf] = $children;
            if ($signToken instanceof Token && $numberLeaf instanceof CalculatedAstLeaf) {
                switch ($signToken->getType()) {
                    case 'PLUS':
                        $leaf->setCalculatedResult($numberLeaf->getCalculatedResult());
                        break;
                    case 'MINUS':
                        $leaf->setCalculatedResult(-$numberLeaf->getCalculatedResult());
                        break;
                }
            }
        }
    }

    public function calculateMulDivPart(CalculatedAstLeaf $leaf)
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

    public function calculateExpression(CalculatedAstLeaf $leaf)
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