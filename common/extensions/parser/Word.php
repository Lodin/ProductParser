<?php

class Word
{
    const TYPE_ARTICLE = 0;
    const TYPE_NAME_PART = 1;
    const TYPE_SIZE_PART = 2;
    const TYPE_MODEL_PART = 3;
    const TYPE_BRAND = 4;
    const TYPE_COLOR = 5;
    const TYPE_STICK_ORIENTATION = 6;
    
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
            $word->_empty = true;
        } else {
            $word->test();
        }
        
        return $word;
    }
    
    public static function hasArticle($word)
    {
        return preg_match('/\w*?\d{6,}/', static::check($word));
    }
    
    public static function hasNamePart($word)
    {
        return preg_match('/[А-Яа-я]+/', static::check($word));
    }
    
    public static function hasSizePart($word)
    {
        return preg_match('/euro|pant|jr|sr|yth|s|m|l|xl|xxl|\d{0,3}\b/ig', static::check($word));
    }
    
    public static function hasColor($word)
    {
        return preg_match('/крас|черн|бел|золот|син|желт/i', static::check($word));
    }
    
    public static function hasStickOrientation($word)
    {
        return preg_match('/l|r/i', static::check($word));
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
    
    public function attach(array &$list)
    {
        switch ($this->_type) {
            case self::TYPE_NAME_PART:
            case self::TYPE_SIZE_PART:
            case self::TYPE_MODEL_PART:
                $list[$this->_type] .= " {$this->_original}"
        }
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
    
    public function isColorPart()
    {
        return $this->_type === self::TYPE_COLOR;
    }
    
    public function isUnknownPart()
    {
        return !$this->isArticle() && !$this->isNamePart()
            && !$this->isSizePart() && !$this->isColorPart()
            && !$this->isStickOrientation();
    }
    
    public function isBrand()
    {
        return $this->_type === self::TYPE_BRAND;
    }
    
    public function isModelName()
    {
        return $this->_type === self::TYPE_MODEL_PART;
    }
    
    public function isStickOrientation()
    {
        return $this->_type === self::TYPE_STICK_ORIENTATION;
    }
    
    public function asStickOrientation()
    {
        $this->_type = self::TYPE_STICK_ORIENTATION;
        return $this;
    }
    
    public function asColor()
    {
        $this->_type = self::TYPE_COLOR;
        return $this;
    }
    
    public function asBrand()
    {
        $this->_type = self::TYPE_BRAND;
        return $this;
    }
    
    public function asModelPart()
    {
        $this->_type = self::TYPE_MODEL_PART;
        return $this;
    }
    
    protected static function clean($word)
    {
        $pocket = [];
        preg_match('/\w+/ig', strtolower(trim($word)), $pocket); // TODO
        return isset($pocket[0])? $pocket[0] : null;
    }
    
    protected static function check($word)
    {
        if ($word instanceof Word) {
            return $word->_basic;
        } elseif (is_string($word)) {
            return $word;
        }
        
        throw new LogicException('$word should be string or Word instance');
    }
    
    protected function test()
    {
        if (!empty($this->_type)) {
            return;
        }
        
        if (static::hasArticle($this->_basic)) {
            $this->_type = self::TYPE_ARTICLE;
        } elseif (static::hasNamePart($this->_basic)) {
            $this->_type = self::TYPE_NAME_PART;
        } elseif (static::hasSizePart($this->_basic)) {
            $this->_type = self::TYPE_SIZE_PART;
        } elseif (static::hasColor($this->_basic)) {
            $this->_type = self::TYPE_COLOR;
        }
    }
}