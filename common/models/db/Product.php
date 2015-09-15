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
 * @property string $size
 * @property string $color
 * @property string $orientation
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
            [['section', 'subsection', 'article', 'brand', 'model', 'name', 'size', 'color'], 'string', 'max' => 255]
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
            'size' => 'Size',
            'color' => 'Color',
            'orientation' => 'Orientation',
        ];
    }
}