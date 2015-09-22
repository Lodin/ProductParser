<?php

namespace common\models\db;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property string $section
 * @property string $subsection
 * @property string $article
 * @property string $brand
 * @property string $model
 * @property string $name
 * @property string $orientation
 * @property string $size
 *
 * @property Color[] $colors
 */
class Product extends \yii\db\ActiveRecord
{
    protected $colorList = [];
    protected $sizeList = [];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['section', 'name'], 'required'],
            [['orientation'], 'string'],
            [['section', 'subsection', 'article', 'brand', 'model', 'name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('db/product', 'id'),
            'section' => Yii::t('db/product', 'section'),
            'subsection' => Yii::t('db/product', 'subsection'),
            'article' => Yii::t('db/product', 'article'),
            'brand' => Yii::t('db/product', 'brand'),
            'model' => Yii::t('db/product', 'model'),
            'name' => Yii::t('db/product', 'name'),
            'orientation' => Yii::t('db/product', 'orientation')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColors()
    {
        return $this->hasMany(Color::className(), ['product_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSizes()
    {
        return $this->hasMany(Size::className(), ['product_id' => 'id']);
    }
    
    /**
     * Saves new product and it's colors and sizes.
     * 
     * @return boolean
     */
    public function upload()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $this->save();
        
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($this->colorList as $color) {
            $color->product_id = $this->id;
            $color->save();
        }
        
        foreach ($this->sizeList as $size) {
            $size->product_id = $this->id;
            $size->save();
        }
        $transaction->commit();
        
        return true;
    }
    
    /**
     * Adds color to inner list.
     * 
     * @param \common\models\db\Color $color
     */
    public function addColor(Color $color)
    {
        $this->colorList[] = $color;
    }
    
    /**
     * Adds size to inner list.
     * 
     * @param \common\models\db\Size $size
     */
    public function addSize(Size $size)
    {
        $this->sizeList[] = $size;
    }
    
    /**
     * Returns last size in inner list if exists.
     * 
     * @return \common\models\db\Size|null
     */
    public function getLastSize()
    {
        $count = count($this->sizeList);
        if ($count === 0) {
            return;
        }
        
        return $this->sizeList[$count - 1];
    }
    
    /**
     * Setting section to product.
     */
    public function setSection()
    {
        $this->section = Yii::t('db/product', 'current_section');
    }
}