<?php
declare(strict_types=1);
namespace CodingLiki\CalculatorParser\Parser\CalculatedTree;
use CodingLiki\GrammarParser\Token\Token;
use CodingLiki\LALR1Parser\AstTree\AstLeaf;
use CodingLiki\LALR1Parser\AstTree\AstTree;

class CalculatedAstTree
{
    /** @var CalculatedAstLeaf[] */
    private array $children;
    public static function buildFromAstTree(AstTree $tree): self
    {
        $calculatedTree = new self;
        foreach ($tree->getChildren() as $child){
            $calculatedTree->addChild(self::buildLeaf($child));
        }

        return $calculatedTree;
    }

    private static function buildLeaf(AstLeaf $leaf): CalculatedAstLeaf
    {
        $calculatedLeaf = new CalculatedAstLeaf($leaf->getName());
        foreach ($leaf->getChildren() as $child){
            if($child instanceof Token){
                $calculatedLeaf->addChildToken($child);
            } elseif($child instanceof AstLeaf){
                $calculatedLeaf->addChildLeaf(self::buildLeaf($child));
            }
        }

        return $calculatedLeaf;
    }

    /**
     * @param CalculatedAstLeaf[] $children
     * @return CalculatedAstTree
     */
    public function setChildren(array $children): CalculatedAstTree
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return CalculatedAstLeaf[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(CalculatedAstLeaf $child): self
    {
        $this->children[] = $child;

        return $this;
    }
}