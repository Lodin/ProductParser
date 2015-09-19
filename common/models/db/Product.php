<?php

namespace common\models\db;

use Yii;
use common\extensions\parser\Word;
use common\models\db\Color;

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
 *
 * @property Color[] $colors
 * @property Size[] $sizes
 */
class Product extends \yii\db\ActiveRecord
{
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
            [['section', 'subsection', 'brand', 'model', 'name'], 'required'],
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
            'id' => 'ID',
            'section' => 'Section',
            'subsection' => 'Subsection',
            'article' => 'Article',
            'brand' => 'Brand',
            'model' => 'Model',
            'name' => 'Name',
            'orientation' => 'Orientation',
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
    
    public function from(array $list)
    {
        $this->article = $list[Word::TYPE_ARTICLE];
        $this->brand = $list[Word::TYPE_BRAND];
        $this->model = $list[Word::TYPE_NAME_PART];
        $this->name = $list[Word::TYPE_NAME_PART];
        $this->orientation = $list[Word::TYPE_STICK_ORIENTATION];
    }
}