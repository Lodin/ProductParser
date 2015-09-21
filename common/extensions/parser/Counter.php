<?php

namespace common\extensions\parser;

use common\extensions\parser\Word;
use common\extensions\parser\Line;
use common\extensions\parser\WordTester;

class Counter
{
    const TYPE_BRAND = 0;
    const TYPE_CATEGORY = 1;
    
    public $_list;
    protected $_type;
    public $_tester;
    
    public static function brand()
    {
        $counter = new Counter();
        $counter->_type = self::TYPE_BRAND;
        return $counter;
    }
    
    public static function category()
    {
        $counter = new Counter();
        $counter->_type = self::TYPE_CATEGORY;
        return $counter;
    }
    
    public function useTester(WordTester $tester)
    {
        $this->_tester = $tester;
        return $this;
    }
    
    public function isBrand()
    {
        return $this->_type === self::TYPE_BRAND;
    }
    
    public function isCategory()
    {
        return $this->_type === self::TYPE_CATEGORY;
    }
    
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
    
    public function remove($word)
    {
        if (!isset($this->_list[$word])) {
            return;
        }
        
        unset($this->_list[$word]);
    }
    
    public function validate(Word $word)
    {
        return ($this->isBrand() && $word->isUnknownPart()) ||
            ($this->isCategory() && $word->isNamePart());
    }
    
    protected function __construct() {}
    
    protected function simpleCount($data)
    {
        if (!isset($this->_list[$data])) {
            $this->_list[$data] = 0;
        }

        $this->_list[$data] += 1;
    }
    
    protected function nounCount($data)
    {
        if (!$this->_tester->testNoun($data)) {
            return;
        }
        
        $this->simpleCount($data);
    }
}