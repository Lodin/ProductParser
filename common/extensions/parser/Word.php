<?php

class Word
{
    const TYPE_ARTICLE = 0;
    const TYPE_NAME_PART = 1;
    const TYPE_SIZE_PART = 2;
    const TYPE_MODEL_PART = 3;
    const TYPE_BRAND = 4;
    
    protected $_basic;
    protected $_original;
    
    protected $_type;
    protected $_empty = false;
    
    protected function __construct() {}
    
    public static function from($data)
    {
        $word = new static;
        
        $word->_basic = static::clean($data);
        $word->_original = trim($data);
        
        if ($word->_basic === null) {
            $this->_empty = true;
        } else {
            $this->test();
        }
        
        return $word;
    }
    
    public function countUp(array &$count)
    {
        if (!isset($count[$this->_basic])) {
            $count[$this->_basic] = 0;
        }
        
        $count[$this->_basic] += 1;
    }
    
    public function peek(array $count)
    {
        return $count[$this->_basic];
    }
    
    public function isEmpty()
    {
        return $this->_empty;
    }
    
    public function isArticle()
    {
        return $this->_type === self::TYPE_ARTICLE;
    }
    
    public function isNamePart()
    {
        return $this->_type === self::TYPE_NAME_PART;
    }
    
    public function isSizePart()
    {
        return $this->_type === self::TYPE_SIZE_PART;
    }
    
    public function isUnknownPart()
    {
        return !$this->isArticle() && !$this->isNamePart()
            && !$this->isSizePart();
    }
    
    public function isBrand()
    {
        return $this->_type === self::TYPE_BRAND;
    }
    
    public function isModelName()
    {
        return $this->_type === self::TYPE_MODEL_PART;
    }
    
    public function asBrand()
    {
        $this->_type = self::TYPE_BRAND;
    }
    
    public function asModelPart()
    {
        $this->_type = self::TYPE_MODEL_PART;
    }
    
    protected static function clean($word)
    {
        $pocket = [];
        preg_match('/\w+/ig', strtolower(trim($word)), $pocket);
        return isset($pocket[0])? $pocket[0] : null;
    }
    
    protected function test()
    {
        if (preg_match('/\w*?\d{6,}/', $this->_basic)) {
            $this->_type = self::TYPE_ARTICLE;
        } elseif (preg_match('/[А-Яа-я]+/', $this->_basic)) {
            $this->_type = self::TYPE_NAME_PART;
        } elseif (preg_match('/euro|pant|jr|sr|yth|s|m|l|xl|xxl|\d{0,3}\b/ig',$this->_basic)) {
            $this->_type = self::TYPE_SIZE_PART;
        }
    }
}