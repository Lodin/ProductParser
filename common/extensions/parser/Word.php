<?php

namespace common\extensions\parser;

use common\models\db\Product;
use common\models\db\Color;

class Word
{
    const TYPE_ARTICLE = 0;
    const TYPE_NAME_PART = 1;
    const TYPE_SIZE_PART = 2;
    const TYPE_MODEL_PART = 3;
    const TYPE_BRAND = 4;
    const TYPE_COLOR = 5;
    const TYPE_STICK_ORIENTATION = 6;
    
    protected $_data;
    protected $_original;
    
    protected $_type;
    protected $_afterDelimiter;
    
    protected function __construct() {}
    
    public static function from($data)
    {
        $word = new static;
        
        $word->_data = trim(strtolower($data));
        $word->_original = trim($data);
        
        $word->test();
        
        return $word;
    }
    
    public static function hasDelimiter($word)
    {
        return strpos(static::inner($word), '-') !== false;
    }
    
    public static function hasJunk($word)
    {
        return preg_match('/^[[:punct:]]+$/', static::inner($word));
    }
    
    public static function hasArticle($word)
    {
        return preg_match('/\w*?\d{6,}/', static::inner($word));
    }
    
    public static function hasNamePart($word)
    {
        return preg_match('/[А-Яа-я]+/', static::inner($word));
    }
    
    public static function hasSizePart($word, $isAfterDelimiter = false)
    {
        return preg_match('/\b(?:euro|pant|jr|sr|yth|s|m|xl|xxl)\b/i', static::inner($word))
            || (preg_match('/\b(?:\d{1,3})\b/', static::inner($word)) && $isAfterDelimiter);
    }
    
    public static function hasColor($word)
    {
        return preg_match('/крас|чер|бел|зол|син|желт|зел|сер|оранж/i', static::inner($word))
            && strpos('белье', static::inner($word)) === false;
    }
    
    public static function hasStickOrientation($word)
    {
        return preg_match('/l|r/i', static::inner($word));
    }
    
    public function call(callable $callback)
    {
        $callback($this, $this->_data);
    }
    
    public function attach(Product &$product)
    {
        switch ($this->_type) {
            case self::TYPE_ARTICLE:
                $product->article = $this->_original;
                break;
            case self::TYPE_NAME_PART:
                $product->name .= " {$this->_original}";
                break;
            case self::TYPE_SIZE_PART:
                $product->size .= " {$this->_original}";
                break;
            case self::TYPE_MODEL_PART:
                $product->model .= " {$this->_original}";
                break;
            case self::TYPE_BRAND:
                $product->brand = $this->_original;
                break;
            case self::TYPE_COLOR:
                $color = new Color();
                $color->parse($this->_original);
                $product->colorlist[] = $color;
                break;
            case self::TYPE_STICK_ORIENTATION:
                $product->orientation = $this->_data;
                break;
            default:
                break;
        }
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
    
    public function isColor()
    {
        return $this->_type === self::TYPE_COLOR;
    }
    
    public function isUnknownPart()
    {
        return !$this->isArticle() && !$this->isNamePart()
            && !$this->isSizePart() && !$this->isColor()
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
    
    public function isAfterDelimiter()
    {
        return $this->_afterDelimiter;
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
    
    public function asAfterDelimiter()
    {
        $this->_afterDelimiter = true;
    }
    
    protected static function inner($word)
    {
        if ($word instanceof Word) {
            return $word->_data;
        } elseif (is_string($word)) {
            return $word;
        }
        
        throw new LogicException('$word should be string or Word instance');
    }
    
    protected function cleanArticle()
    {
        preg_match('/\(?([\w\d]+)\)?/', $this->_original, $data);
        $this->_data = strtolower($data[1]);
        $this->_original = $data[1];
    }
    
    protected function test()
    {
        if (!empty($this->_type)) {
            return;
        }
        
        if (static::hasArticle($this->_data)) {
            $this->_type = self::TYPE_ARTICLE;
            $this->cleanArticle();
        } elseif (static::hasColor($this->_data)) {
            $this->_type = self::TYPE_COLOR;
        } elseif (static::hasNamePart($this->_data)) {
            $this->_type = self::TYPE_NAME_PART;
        } elseif (static::hasSizePart($this->_data, $this->isAfterDelimiter())) {
            $this->_type = self::TYPE_SIZE_PART;
        } 
    }
}