<?php

use common\models\db\Product;

class Line
{
    protected $_original = '';
    protected $_words = [];
    
    protected function __construct() {}
    
    public static function from($data)
    {
        $line = new Line;
        
        $line->_original = $data;
        foreach (explode(' ', $data) as $word) {
            $this->_words[] = Word::from($word);
        }
        
        return $line;
    }
    
    public function consider(array $counts)
    {
        $buffer = null;
        
        foreach ($this->_words as $word) {
            if ($word->isEmpty()) {
                continue;
            }
            
            if ($buffer === null) {
                $buffer = $word;
                continue;
            }
            
            list($buffer, $previous) = $this->compare($buffer, $word, $counts);
            $previous->asModelPart();
        }
        
        $buffer->asBrand();
    }
    
    public function apply(Product $product)
    {
        
    }
    
    public function count()
    {
        $count = [];
        
        foreach ($this->_words as $word) {
            if ($word->isEmpty()) {
                continue;
            }
            
            if ($word->isUnknownPart()) {
                $word->countUp($count);
            }
        }
        
        return $count;
    }
    
    protected function compare(Word $first, Word $second, array $counts)
    {
        if ($first->peek($counts) < $second->peek($counts)) {
            return [$second, $first];
        } else {
            return [$first, $second];
        }
    }
}