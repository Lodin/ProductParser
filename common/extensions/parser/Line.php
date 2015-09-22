<?php

namespace common\extensions\parser;

use Closure;
use common\models\db\Product;

/**
 * Wrapper above product name line inteded for distributing words to product
 * model fields.
 */
class Line
{
    protected $_original = '';
    protected $_words = [];
    protected $_isStick = false;
    
    /**
     * Receives product name, parses it to words and creates new instance of
     * Line.
     * 
     * @param string $data unparsed product name
     * @return \common\extensions\parser\Line
     */
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
            
            $word
                ->test()
                ->clean();
            
            if ($line->_isStick && $isPassedDelimiter
                    && Word::hasStickOrientation($word)) {
                $word->asStickOrientation();
            }
        
            $line->_words[] = $word;
        }
        
        return $line;
    }
    
    /**
     * Applies counter data to included words.
     * 
     * @param \common\extensions\parser\Counter $counter
     * @return \common\extensions\parser\Line
     */
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
                $counter->throwOut($bufWord);

                $counter->remove($bufData);
                $bufWord = $word;
                $bufData = $data;
            } else {
                $counter->throwOut($word);
                
                $counter->remove($data);
            }
        });
        
        if ($bufWord !== null) {
            $counter->accept($bufWord);
        }
        
        return $this;
    }
    
    /**
     * Writes words data to product model.
     * 
     * @param Product $product
     * @return Product
     */
    public function apply(Product $product)
    {
        foreach ($this->_words as $word) {
            $word->attach($product);
        }
        
        return $product;
    }
    
    /**
     * Iterates word list.
     * 
     * @param Closure $callback rule applying to each word
     */
    public function each(Closure $callback)
    {
        foreach ($this->_words as $word) {
            $word->call($callback);
        }
    }
    
    protected function __construct() {}
    
    /**
     * Searches for multiple colors inside the string.
     * 
     * @param string $str
     * @return boolean
     */
    protected function hasMultipleColors($str)
    {
        return mb_strpos($str, '/') !== false && Word::hasColor($str);
    }
    
    /**
     * Accumulates multiple colors found inside the string to word list.
     * 
     * @param string $str
     */
    protected function handleMultipleColors($str)
    {
        foreach ($this->splitColors($str) as $word) {
            $this->_words[] = Word::from($word)->asColor();
        }
    }
    
    /**
     * Splits product name to words by space symbol.
     * 
     * @param string $str
     * @return string[]
     */
    protected function split($str)
    {
        return explode(' ', $str);
    }
    
    /**
     * Splits multiple color string to words by color delimiter symbol `/`.
     * 
     * @param string $str
     * @return string[]
     */
    protected function splitColors($str)
    {
        return explode('/', $str);
    }
    
    /**
     * Determines if the word points the possibility of stick orientation
     * existion inside the whole line.
     * 
     * @param type $word
     * @return type
     */
    protected function isStick($word)
    {
        return mb_strpos(mb_strtolower($word), 'клюшк') !== false;
    }
}