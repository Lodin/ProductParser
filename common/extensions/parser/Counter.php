<?php

namespace common\extensions\parser;

use common\extensions\parser\Word;
use common\extensions\parser\Line;
use common\extensions\parser\WordTester;

/**
 * Incapsulates counting logic intended for intelligence imitation. It is need
 * to find brands and subsections.
 */
class Counter
{
    const TYPE_BRAND = 0;
    const TYPE_CATEGORY = 1;
    
    protected $_list;
    protected $_type;
    protected $_tester;
    
    /**
     * Creates new Counter instance with `brand` type.
     * 
     * @return \common\extensions\parser\Counter
     */
    public static function brand()
    {
        $counter = new Counter();
        $counter->_type = self::TYPE_BRAND;
        return $counter;
    }
    
    /**
     * Creates new Counter instance with `category` type.
     * 
     * @return \common\extensions\parser\Counter
     */
    public static function category()
    {
        $counter = new Counter();
        $counter->_type = self::TYPE_CATEGORY;
        return $counter;
    }
    
    /**
     * Sets the WordTester that counter will use.
     * 
     * @param WordTester $tester
     * @return \common\extensions\parser\Counter
     */
    public function useTester(WordTester $tester)
    {
        $this->_tester = $tester;
        return $this;
    }
    
    /**
     * Answers if the counter has `brand` type.
     * 
     * @return boolean
     */
    public function isBrand()
    {
        return $this->_type === self::TYPE_BRAND;
    }
    
    /**
     * Answers if the counter has `category` type.
     * 
     * @return boolean
     */
    public function isCategory()
    {
        return $this->_type === self::TYPE_CATEGORY;
    }
    
    /**
     * Counts word in single line.
     * 
     * @param Line $line
     */
    public function count(Line $line)
    {
        $line->each(function(Word $word, $data) {
            if (!$this->validate($word)) {
                return;
            }
            
            if ($this->isCategory()) {
                $this->nounCount($data); 
            } else {
                $this->simpleCount($data);
            }
        });
    }
    
    /**
     * Defines word type if it is not determined as brand or category.
     * 
     * @param Word $word
     */
    public function throwOut(Word $word)
    {
        if ($this->isBrand()) {
            $word->asModelPart();
        }
    }
    
    /**
     * Defines word type if it is determined as brand or category. 
     * 
     * @param Word $word
     */
    public function accept(Word $word)
    {
        if ($this->isBrand()) {
            $word->asBrand();
        } else {
            $word->asSubsection();
        }
    }
    
    /**
     * Returns if the first word has bigger count than second.
     * 
     * @param string $first first word to compare
     * @param string $second second word to compare
     * @return boolean
     */
    public function greater($first, $second)
    {
        if (!isset($this->_list[$first])) {
            return false;
        }
        
        if (!isset($this->_list[$second])) {
            return true;
        }
        
        return $this->_list[$first] > $this->_list[$second];
    }
    
    /**
     * Removes word from counter list if it was throwed out.
     * 
     * @param string $word
     */
    public function remove($word)
    {
        if (!isset($this->_list[$word])) {
            return;
        }
        
        unset($this->_list[$word]);
    }
    
    /**
     * Validates word to have right type depending on counter type.
     * 
     * @param Word $word
     * @return boolean
     */
    public function validate(Word $word)
    {
        return ($this->isBrand() && $word->isUnknownPart()) ||
            ($this->isCategory() && $word->isNamePart());
    }
    
    protected function __construct() {}
    
    /**
     * Simply counts word.
     * 
     * @param string $data
     */
    protected function simpleCount($data)
    {
        if (!isset($this->_list[$data])) {
            $this->_list[$data] = 0;
        }

        $this->_list[$data] += 1;
    }
    
    /**
     * Counts word only if it is a noun.
     * 
     * @param string $data
     */
    protected function nounCount($data)
    {
        if (!$this->_tester->testNoun($data)) {
            return;
        }
        
        $this->simpleCount($data);
    }
}