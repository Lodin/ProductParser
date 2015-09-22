<?php

namespace common\models\db;

use Yii;

/**
 * This is the model class for table "color".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $name
 *
 * @property Product $product
 */
class Color extends \yii\db\ActiveRecord
{
    const RED = 'red';
    const BLACK = 'black';
    const WHITE = 'white';
    const GOLD = 'gold';
    const BLUE = 'blue';
    const YELLOW = 'yellow';
    const GREEN = 'green';
    const GRAY = 'gray';
    const ORANGE = 'orange';
    const DARK_BLUE = 'dark_blue';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'color';
    }
    
    /**
     * Determines if the word contains `red` color. 
     * 
     * @param string $word
     * @return boolean
     */
    public static function isRed($word)
    {
        return mb_strpos($word, 'крас') !== false;
    }
    
    /**
     * Determines if the word contains `black` color. 
     * 
     * @param string $word
     * @return boolean
     */
    public static function isBlack($word)
    {
        return mb_strpos($word, 'чер') !== false;
    }
    
    /**
     * Determines if the word contains `white` color. 
     * 
     * @param string $word
     * @return boolean
     */
    public static function isWhite($word)
    {
        return mb_strpos($word, 'бел') !== false;
    }
    
    /**
     * Determines if the word contains `gold` color. 
     * 
     * @param string $word
     * @return boolean
     */
    public static function isGold($word)
    {
        return mb_strpos($word, 'зол') !== false;
    }
    
    /**
     * Determines if the word contains `blue` color. 
     * 
     * @param string $word
     * @return boolean
     */
    public static function isBlue($word)
    {
        return mb_strpos($word, 'син') !== false;
    }
    
    /**
     * Determines if the word contains `yellow` color. 
     * 
     * @param string $word
     * @return boolean
     */
    public static function isYellow($word)
    {
        return mb_strpos($word, 'желт') !== false;
    }
    
    /**
     * Determines if the word contains `green` color. 
     * 
     * @param string $word
     * @return boolean
     */
    public static function isGreen($word)
    {
        return mb_strpos($word, 'зел') !== false;
    }
    
    /**
     * Determines if the word contains `gray` color. 
     * 
     * @param string $word
     * @return boolean
     */
    public static function isGray($word)
    {
        return mb_strpos($word, 'сер') !== false;
    }
    
    /**
     * Determines if the word contains `orange` color. 
     * 
     * @param string $word
     * @return boolean
     */
    public static function isOrange($word)
    {
        return mb_strpos($word, 'оранж') !== false;
    }
    
    /**
     * Determines if the word contains `dark blue` color. 
     * 
     * @param string $word
     * @return boolean
     */
    public static function isDarkBlue($word)
    {
        return mb_strpos($word, 'т.') !== false || mb_strpos($word, 'темн');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'name'], 'required'],
            [['product_id'], 'integer'],
            [['name'], 'string'],
            [['name'], 'in', 'range' => [
                Color::RED,
                Color::BLACK,
                Color::WHITE,
                Color::GOLD,
                Color::BLUE,
                Color::YELLOW,
                Color::GREEN,
                Color::GRAY,
                Color::ORANGE,
                Color::DARK_BLUE
            ]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'id'),
            'product_id' => Yii::t('app', 'product_id'),
            'name' => Yii::t('app', 'name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
    
    /**
     * Parses string to have one of need colors.
     * 
     * @param string $word
     * @throws \Exception if color does not exist
     */
    public function parse($word)
    {
        switch ($word) {
            case static::isDarkBlue($word):
                $this->name = Color::DARK_BLUE;
                break;
            case static::isRed($word):
                $this->name = Color::RED;
                break;
            case static::isBlack($word):
                $this->name = Color::BLACK;
                break;
            case static::isWhite($word):
                $this->name = Color::WHITE;
                break;
            case static::isGold($word):
                $this->name = Color::GOLD;
                break;
            case static::isBlue($word):
                $this->name = Color::BLUE;
                break;
            case static::isYellow($word):
                $this->name = Color::YELLOW;
                break;
            case static::isGreen($word):
                $this->name = Color::GREEN;
                break;
            case static::isGray($word):
                $this->name = Color::GRAY;
                break;
            case static::isOrange($word):
                $this->name = Color::ORANGE;
                break;
            default:
                throw new \Exception("No color $word exist");
        }
    }
    
    /**
     * Returns current color name.
     * 
     * @return string
     */
    public function getName()
    {
        switch($this->name) {
            case Color::DARK_BLUE:
                return Yii::t('db/color', 'dark_blue');
            case Color::RED:
                return Yii::t('db/color', 'red');
            case Color::BLACK:
                return Yii::t('db/color', 'black');
            case Color::WHITE:
                return Yii::t('db/color', 'white');
            case Color::GOLD:
                return Yii::t('db/color', 'gold');
            case Color::BLUE:
                return Yii::t('db/color', 'blue');
            case Color::YELLOW:
                return Yii::t('db/color', 'yellow');
            case Color::GREEN:
                return Yii::t('db/color', 'green');
            case Color::GRAY:
                return Yii::t('db/color', 'gray');
            case Color::ORANGE:
                return Yii::t('db/color', 'orange');
        }
    }
}