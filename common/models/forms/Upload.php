<?php

namespace common\models\forms;

use common\extensions\Parser;

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
class Upload extends \yii\db\ActiveRecord
{
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
    
    public function parse()
    {
        $file = $_FILES['Upload']['products'];
        
        if ($file === null || $file['error'] != UPLOAD_ERR_OK
                || !is_uploaded_file($file['tmp_name'])) {
            return;
        }
        
        return Parser::load(file_get_contents($file['tmp_name']))->run();
    }
}