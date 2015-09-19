<?php

namespace common\models\forms;

use SimpleFile;
use common\extensions\parser\Parser;

class Upload extends \yii\db\ActiveRecord
{
    public $products;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['products'], 'file']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'products' => 'Products',
        ];
    }
    
    public function parse()
    {
        $files = SimpleFile::disassemble($_FILES);
        
        if (empty($files) || $files[0]->error != UPLOAD_ERR_OK
                || !is_uploaded_file($files[0]->tmpName)) {
            return;
        }
        
        return Parser::load(file_get_contents($files[0]->tmpName))->run();
    }
}