<?php

namespace common\extensions\parser;

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
        foreach ($line->split($data) as $wordStr) {
            if (empty($wordStr) || Word::hasJunk($wordStr)) {
                continue;
            }
            
            if ($line->hasMultipleColors($wordStr)) {
                $line->handleMultipleColors($wordStr);
                continue;
            }
            
            if ($line->isStick($wordStr)) {
                $line->_isStick = true;
            }
            
            $word = Word::from($wordStr);
            
            if ($line->_isStick && Word::hasStickOrientation($word)) {
                $word->asStickOrientation();
            }
        
            $line->_words[] = $word;
        }
        
        return $line;
    }
    
    public function consider(array &$counts)
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
            $previous->removeFrom($counts);
        }
        
        $buffer->asBrand();
        
        return $this;
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
        
        return $list;
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