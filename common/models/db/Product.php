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
            [['section', 'subsection', 'article', 'brand', 'model', 'name', 'size'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'section' => Yii::t('app', 'Section'),
            'subsection' => Yii::t('app', 'Subsection'),
            'article' => Yii::t('app', 'Article'),
            'brand' => Yii::t('app', 'Brand'),
            'model' => Yii::t('app', 'Model'),
            'name' => Yii::t('app', 'Name'),
            'orientation' => Yii::t('app', 'Orientation'),
            'size' => Yii::t('app', 'Size'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColors()
    {
        return $this->hasMany(Color::className(), ['product_id' => 'id']);
    }
    
    public function upload()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $this->save();
        
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($this->colorList as $color) {
            $color->save();
        }
        $transaction->commit();
        
        return true;
    }
    
    public function addColor(Color $color)
    {
        $this->colorList[] = $color;
    }
    
    public function setSection()
    {
        $this->section = 'Хоккей';
    }
}