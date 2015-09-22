<?php

namespace common\extensions\parser;

use Closure;
use common\models\db\Product;
use common\models\db\Color;
use common\models\db\Size;

/**
 * Incapsulates single word in product name string. 
 */
class Word
{
    const TYPE_ARTICLE = 0;
    const TYPE_NAME_PART = 1;
    const TYPE_SIZE = 2;
    const TYPE_MODEL_PART = 3;
    const TYPE_BRAND = 4;
    const TYPE_COLOR = 5;
    const TYPE_STICK_ORIENTATION = 6;
    const TYPE_SUBSECTION = 7;
    
    protected $_data;
    protected $_original;
    
    protected $_type;
    protected $_afterDelimiter;
    
    protected function __construct() {}
    
    /**
     * Creates new Word instance from received word.
     * 
     * @param string $data single word from product name string
     * @return common\extensions\parser\Word
     */
    public static function from($data)
    {
        $word = new static;
        
        $word->_data = trim(mb_strtolower($data));
        $word->_original = trim($data);
        
        return $word;
    }
    
    /**
     * Searches for delimiter in the word. It is need for determine sizes and
     * stick orientation in the products.
     * 
     * @param \common\extensions\parser\Word|string $word word for search
     * @return boolean
     */
    public static function hasDelimiter($word)
    {
        return mb_strpos(static::inner($word), '-') !== false;
    }
    
    /**
     * Searches for junk symbols in the received data. 
     * 
     * @param \common\extensions\parser\Word|string $word
     * @return boolean
     */
    public static function hasJunk($word)
    {
        return preg_match('/^\s*?[[:punct:]]+\s*?$/', static::inner($word));
    }
    
    /**
     * Searches for article string in the received data.
     * 
     * @param \common\extensions\parser\Word|string $word
     * @return boolean
     */
    public static function hasArticle($word)
    {
        return preg_match('/\w*?\d{6,}/', static::inner($word));
    }
    
    /**
     * Searches for name part in cyrillic in the received data.
     * 
     * @param \common\extensions\parser\Word|string $word
     * @return boolean
     */
    public static function hasNamePart($word)
    {
        return preg_match('/[А-Яа-я]+/', static::inner($word));
    }
    
    /**
     * Searches for size string in the received data.
     * 
     * @param \common\extensions\parser\Word|string $word
     * @return boolean
     */
    public static function hasSize($word, $isAfterDelimiter = false)
    {
        return preg_match('/\b(?:jr|sr|yth|s|m|xl|xxl)\b/i', static::inner($word))
            || static::hasSizePart($word)
            || (preg_match('/\b(?:\d{1,3})\b/', static::inner($word)) && $isAfterDelimiter);
    }
    
    /**
     * Searches for size part in the received data.
     * 
     * @param \common\extensions\parser\Word|string $word
     * @return boolean
     */
    public static function hasSizePart($word)
    {
        return mb_strpos(mb_strtolower(static::inner($word)), 'euro') !== false
            || mb_strpos(mb_strtolower(static::inner($word)), 'pant') !== false;
    }
    
    /**
     * Searches for colors in the received data.
     * 
     * @param \common\extensions\parser\Word|string $word
     * @return boolean
     */
    public static function hasColor($word)
    {
        return preg_match('/крас|чер|бел|зол|син|желт|зел|сер|оранж/i', static::inner($word))
            && strpos('белье', static::inner($word)) === false;
    }
    
    /**
     * Searches for stick orientation in the received data.
     * 
     * @param \common\extensions\parser\Word|string $word
     * @return boolean
     */
    public static function hasStickOrientation($word)
    {
        return preg_match('/\b(?:l|r)\b/i', static::inner($word));
    }
    
    /**
     * Receives closure to deal with inner word data or word itself.
     * 
     * @param Closure $callback
     * @return mixed
     */
    public function call(Closure $callback)
    {
        return $callback($this, $this->_data);
    }
    
    /**
     * Attaches words with different types to product model fields.
     * 
     * @param common\models\db\Product $product
     */
    public function attach(Product &$product)
    {
        switch ($this->_type) {
            case self::TYPE_ARTICLE:
                $this->addArticle($product);
                break;
            case self::TYPE_NAME_PART:
                $this->addNamePart($product);
                break;
            case self::TYPE_SIZE:
                $this->addSize($product);
                break;
            case self::TYPE_MODEL_PART:
                $this->addModelPart($product);
                break;
            case self::TYPE_BRAND:
                $this->addBrand($product);
                break;
            case self::TYPE_COLOR:
                $this->addColor($product);
                break;
            case self::TYPE_SUBSECTION:
                $this->addSubsection($product);
                break;
            case self::TYPE_STICK_ORIENTATION:
                $this->addStickOrientation($product);
                break;
            default:
                break;
        }
    }
    
    /**
     * Answers if the word is product article.
     * 
     * @return boolean
     */
    public function isArticle()
    {
        return $this->_type === self::TYPE_ARTICLE;
    }
    
    /**
     * Answers if the word is product name part.
     * 
     * @return boolean
     */
    public function isNamePart()
    {
        return $this->_type === self::TYPE_NAME_PART;
    }
    
    /**
     * Answers if the word is product size.
     * 
     * @return boolean
     */
    public function isSize()
    {
        return $this->_type === self::TYPE_SIZE;
    }
    
    /**
     * Answers if the word is product color.
     * 
     * @return boolean
     */
    public function isColor()
    {
        return $this->_type === self::TYPE_COLOR;
    }
    
    /**
     * Answers if the word is still unknown.
     * 
     * @return boolean
     */
    public function isUnknownPart()
    {
        return !$this->isArticle() && !$this->isNamePart()
            && !$this->isSize() && !$this->isColor()
            && !$this->isStickOrientation();
    }
    
    /**
     * Answers if the word is product brand.
     * 
     * @return boolean
     */
    public function isBrand()
    {
        return $this->_type === self::TYPE_BRAND;
    }
    
    /**
     * Answers if the word is product model part.
     * 
     * @return boolean
     */
    public function isModelPart()
    {
        return $this->_type === self::TYPE_MODEL_PART;
    }
    
    /**
     * Answers if the word is places after delimiter (in other words, it can be
     * size or stick orientation)
     * 
     * @return boolean
     */
    public function isAfterDelimiter()
    {
        return $this->_afterDelimiter;
    }
    
    /**
     * Answers if the word is product stick orientation.
     * 
     * @return boolean
     */
    public function isStickOrientation()
    {
        return $this->_type === self::TYPE_STICK_ORIENTATION;
    }
    
    /**
     * Answers if the word is product subsection name.
     * 
     * @return boolean
     */
    public function isSubsection()
    {
        return $this->_type === self::TYPE_SUBSECTION;
    }
    
    /**
     * Sets word type to `stick orientation`.
     * 
     * @return \common\extensions\parser\Word
     */
    public function asStickOrientation()
    {
        $this->_type = self::TYPE_STICK_ORIENTATION;
        return $this;
    }
    
    /**
     * Sets word type to `color`.
     * 
     * @return \common\extensions\parser\Word
     */
    public function asColor()
    {
        $this->_type = self::TYPE_COLOR;
        return $this;
    }
    
    /**
     * Sets word type to `brand`.
     * 
     * @return \common\extensions\parser\Word
     */
    public function asBrand()
    {
        $this->_type = self::TYPE_BRAND;
        return $this;
    }
    
    /**
     * Sets word type to `model part`.
     * 
     * @return \common\extensions\parser\Word
     */
    public function asModelPart()
    {
        $this->_type = self::TYPE_MODEL_PART;
        return $this;
    }
    
    /**
     * Sets word type to `subsection`.
     * 
     * @return \common\extensions\parser\Word
     */
    public function asSubsection()
    {
        $this->_type = self::TYPE_SUBSECTION;
        return $this;
    }
    
    /**
     * Sets word status to `after delimiter`. Is does not change it's type.
     * 
     * @return \common\extensions\parser\Word
     */
    public function asAfterDelimiter()
    {
        $this->_afterDelimiter = true;
    }
    
    /**
     * Tests word to determine it's type.
     * 
     * @return \common\extensions\parser\Word
     */
    public function test()
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
        } elseif (static::hasSize($this->_data, $this->isAfterDelimiter())) {
            $this->_type = self::TYPE_SIZE;
        }
        
        return $this;
    }
    
    /**
     * Removes junk for articles, name parts, sizes, brands and subsections.
     * 
     * @return \common\extensions\parser\Word
     */
    public function clean()
    {
        switch ($this->_type) {
            case self::TYPE_ARTICLE:
            case self::TYPE_NAME_PART:
            case self::TYPE_SIZE:
            case self::TYPE_BRAND:
            case self::TYPE_SUBSECTION:
                $this->removeJunk();
                break;
            default:
                break;
        }
        
        return $this;
    }
    
    /**
     * Checks if the received data is string or Word instance and returns Word's
     * inner data or string itself in order to access bare word string. 
     * 
     * @param \common\extensions\parser\Word|string $word
     * @return string
     * @throws LogicException if word is something else than string or Word
     *                        instance.
     */
    protected static function inner($word)
    {
        if ($word instanceof Word) {
            return $word->_data;
        } elseif (is_string($word)) {
            return $word;
        }
        
        throw new LogicException('$word should be string or Word instance');
    }
    
    /**
     * Removes parens around article.
     */
    protected function cleanArticle()
    {
        preg_match('/\(?([\w\d]+)\)?/', $this->_original, $data);
        $this->_data = strtolower($data[1]);
        $this->_original = $data[1];
    }
    
    /**
     * Enters delimiter after model attribute if it is not empty.
     * 
     * @param common\models\db\Product|common\models\db\Size $model
     * @param string $attribute
     * @param string $delimiter
     */
    protected function chain($model, $attribute, $delimiter = ' ')
    {
        if (!empty($model->$attribute)) {
            $model->$attribute .= $delimiter;
        }
    }
    
    /**
     * Removes punctuation junk around word.
     */
    protected function removeJunk()
    {
        preg_match('/[[:punct:]]*?([^[:punct:]]+)[[:punct:]]*?/', $this->_original, $word);
        $this->_original = $word[1];
        $this->_data = mb_strtolower($this->_original);
    }
    
    /**
     * Adds article to a product field.
     * 
     * @param common\models\db\Product $product
     */
    protected function addArticle(Product $product)
    {
        $product->article = $this->_original;
    }
    
    /**
     * Adds name part to a product field.
     * 
     * @param common\models\db\Product $product
     */
    protected function addNamePart(Product $product)
    {
        $this->chain($product, 'name');
        $product->name .= $this->_original;
    }
    
    /**
     * Adds size to a product field.
     * 
     * @param common\models\db\Product $product
     */
    protected function addSize(Product $product)
    {
        $lastSize = $product->getLastSize();
        if ($lastSize !== null && static::hasSizePart($lastSize->name)) {
            $this->chain($lastSize, 'name');
            $lastSize->name .= $this->_original;
            return;
        }
        
        $size = new Size();
        $size->name = $this->_original;
        $product->addSize($size);
    }
    
    /**
     * Adds model part to a product field.
     * 
     * @param common\models\db\Product $product
     */
    protected function addModelPart(Product $product)
    {
        $this->chain($product, 'model');
        $product->model .= $this->_original;

        $this->chain($product, 'name');
        $product->name .= $this->_original;
    }
    
    /**
     * Adds brand to a product field.
     * 
     * @param common\models\db\Product $product
     */
    protected function addBrand(Product $product)
    {
        $this->chain($product, 'name');
        $product->name .= $this->_original;
        $product->brand = $this->_original;
    }
    
    /**
     * Add color to a product field.
     * 
     * @param common\models\db\Product $product
     */
    protected function addColor(Product $product)
    {
        $color = new Color();
        $color->parse($this->_original);
        $product->addColor($color);
    }
    
    /**
     * Add subsection name to a product field.
     * 
     * @param common\models\db\Product $product
     */
    protected function addSubsection(Product $product)
    {
        $this->chain($product, 'name');
        $product->name .= $this->_original;
        $product->subsection = mb_convert_case($this->_original, MB_CASE_TITLE);
    }
    
    /**
     * Add stick orientation to a product field.
     * 
     * @param common\models\db\Product $product
     */
    protected function addStickOrientation(Product $product)
    {
        $product->orientation = $this->_data;
    }
}