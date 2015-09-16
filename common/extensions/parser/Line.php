<?php

use common\models\db\Product;

class Line
{
    protected $_original = '';
    protected $_words = [];
    protected $_isStick = false;
    
    protected function __construct() {}
    
    public static function from($data)
    {
        $line = new Line;
        
        $line->_original = $data;
        foreach ($line->split($data) as $word) {
            if ($line->hasMultipleColors($word)) {
                $line->handleMultipleColors($word);
                continue;
            }
            
            if ($this->isStick($word)) {
                $this->_isStick = true;
            }
            
            $word = Word::from($word);
            
            if ($this->_isStick && Word::hasStickOrientation($word)) {
                $word->asStickOrientation();
            }
        
            $line->_words[] = $word;
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
        $list = [
            Word::TYPE_ARTICLE => '',
            Word::TYPE_NAME_PART => '',
            Word::TYPE_SIZE_PART => '',
            Word::TYPE_MODEL_PART => '',
            Word::TYPE_BRAND => '',
            Word::TYPE_COLOR => '',
            Word::TYPE_STICK_ORIENTATION => ''
        ];
        
        foreach ($this->_words as $word) {
            $word->attach($list);
        }
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
    
    protected function hasMultipleColors($str)
    {
        return strpos($str, '\\') !== false && Word::hasColor($str);
    }
    
    protected function handleMultupleColors($str)
    {
        foreach ($this->splitColors($str) as $word) {
            $this->_words[] = Word::from($word)->asColor();
        }
    }
    
    protected function split($str)
    {
        return explode(' ', $str);
    }
    
    protected function splitColors($str)
    {
        return explode('\\', $str);
    }
    
    protected function isStick($word)
    {
        return (bool)preg_match('/клюшк/i', $word);
    }
}