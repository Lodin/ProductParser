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
        
        $isPassedDelimiter = false;
        foreach ($line->split($data) as $wordStr) {
            if (Word::hasDelimiter($wordStr)) {
                $isPassedDelimiter = true;
                continue;
            }
            
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
            
            if ($isPassedDelimiter) {
                $word->asAfterDelimiter();
            }
            
            if ($line->_isStick && $isPassedDelimiter
                    && Word::hasStickOrientation($word)) {
                $word->asStickOrientation();
            }
        
            $line->_words[] = $word;
        }
        
        return $line;
    }
    
    public function consider(Counter &$counter)
    {
        $bufWord = null;
        $bufData = null;
        
        $this->each(function(Word $word, $data) use($counter, &$bufWord, &$bufData) {
            if (!$counter->validate($word)) {
                return;
            }
            
            if ($bufWord === null) {
                $bufWord = $word;
                $bufData = $data;
                return;
            }
            
            if ($counter->greater($data, $bufData)) {
                if ($counter->isBrand()) {
                    $bufWord->asModelPart();
                }
                
                $counter->remove($bufData);
                $bufWord = $word;
                $bufData = $data;
            } else {
                if ($counter->isBrand()) {
                    $word->asModelPart();
                }
                
                $counter->remove($data);
            }
        });
        
        if ($bufWord !== null) {
            if ($counter->isBrand()) {
                $bufWord->asBrand();
            } else {
                $bufWord->asSubsection();
            }
        }
        
        return $this;
    }
    
    public function apply(Product $product)
    {
        foreach ($this->_words as $word) {
            $word->attach($product);
        }
        
        return $product;
    }
    
    public function each(callable $callback)
    {
        foreach ($this->_words as $word) {
            $word->call($callback);
        }
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
        return strpos($str, '/') !== false && Word::hasColor($str);
    }
    
    protected function handleMultipleColors($str)
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
        return explode('/', $str);
    }
    
    protected function isStick($word)
    {
        return mb_strpos(mb_strtolower($word), 'клюшк') !== false;
    }
}