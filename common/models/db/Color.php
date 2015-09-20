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
    
    public static function isRed($word)
    {
        return strpos($word, 'крас') !== false;
    }
    
    public static function isBlack($word)
    {
        return strpos($word, 'чер') !== false;
    }
    
    public static function isWhite($word)
    {
        return strpos($word, 'бел') !== false;
    }
    
    public static function isGold($word)
    {
        return strpos($word, 'зол') !== false;
    }
    
    public static function isBlue($word)
    {
        return strpos($word, 'син') !== false;
    }
    
    public static function isYellow($word)
    {
        return strpos($word, 'желт') !== false;
    }
    
    public static function isGreen($word)
    {
        return strpos($word, 'зел') !== false;
    }
    
    public static function isGray($word)
    {
        return strpos($word, 'сер') !== false;
    }
    
    public static function isOrange($word)
    {
        return strpos($word, 'оранж') !== false;
    }
    
    public static function isDarkBlue($word)
    {
        return strpos($word, 'т.') !== false;
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
            'id' => Yii::t('app', 'ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
    
    public function parse($word)
    {
        if (static::isDarkBlue($word)) {
            $this->name = Color::DARK_BLUE;
        } elseif (static::isRed($word)) {
            $this->name = Color::RED;
        } elseif (static::isBlack($word)) {
            $this->name = Color::BLACK;
        } elseif (static::isWhite($word)) {
            $this->name = Color::WHITE;
        } elseif (static::isGold($word)) {
            $this->name = Color::GOLD;
        } elseif (static::isBlue($word)) {
            $this->name = Color::BLUE;
        } elseif (static::isYellow($word)) {
            $this->name = Color::YELLOW;
        } elseif (static::isGreen($word)) {
            $this->name = Color::GREEN;
        } elseif (static::isGray($word)) {
            $this->name = Color::GRAY;
        } elseif (static::isOrange($word)) {
            $this->name = Color::ORANGE;
        } else {
            throw new \Exception("No color $word exist");
        }
    }
    
    public function getName()
    {
        switch($this->name) {
            case Color::DARK_BLUE:
                return Yii::t('color', 'dark_blue');
            case Color::RED:
                return Yii::t('color', 'red');
            case Color::BLACK:
                return Yii::t('color', 'black');
            case Color::WHITE:
                return Yii::t('color', 'white');
            case Color::GOLD:
                return Yii::t('color', 'gold');
            case Color::BLUE:
                return Yii::t('color', 'blue');
            case Color::YELLOW:
                return Yii::t('color', 'yellow');
            case Color::GREEN:
                return Yii::t('color', 'green');
            case Color::GRAY:
                return Yii::t('color', 'gray');
            case Color::ORANGE:
                return Yii::t('color', 'orange');
        }
    }
}