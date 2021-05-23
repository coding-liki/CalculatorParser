<?php
declare(strict_types=1);

namespace CodingLiki\CalculatorParser\Parser;

use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstLeaf;
use CodingLiki\CalculatorParser\Parser\CalculatedTree\CalculatedAstTree;
use CodingLiki\CalculatorParser\Parser\RuleCalculators\RuleCalculatorInterface;
use CodingLiki\GrammarParser\GrammarRuleParser;
use CodingLiki\GrammarParser\Token\GrammarTokenParser;
use CodingLiki\LALR1Parser\Parser\LALR1Parser;
use CodingLiki\LALR1Parser\TableReader\CsvTableReader;
use CodingLiki\LALRLexer\Lexer\LALRLexer;

class CalculatorParser
{

    /**
     * CalculatorParser constructor.
     * @param string $tokensFile
     * @param string $tableFile
     * @param string $rulesFile
     * @param RuleCalculatorInterface[] $ruleCalculators
     */
    public function __construct(private string $tokensFile, private string $tableFile, private string $rulesFile, private array $ruleCalculators = [ ])
    {
    }

    public function parse(string $source): float
    {
        $lexer = new LALRLexer(GrammarTokenParser::parse(file_get_contents($this->tokensFile)), ['WS']);
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

        $foundRuleCalculator = false;
        foreach ($this->ruleCalculators as $ruleCalculator){
            if($ruleCalculator->acceptRule($leaf->getName())){
                $ruleCalculator->calculate($leaf);
                $foundRuleCalculator = true;
                break;
            }
        }

        if(!$foundRuleCalculator){
            echo "Не знаю, как парсить ".$leaf->getName()."\n";
        }
    }
}