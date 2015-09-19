<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\forms\Upload;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * Displays homepage with info of uploaded products.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $upload = new Upload;
        $data = null;
        
        if (!empty($_FILES)) {
            $data = $upload->parse();
        } 
        
        return $this->render('upload', [
            'upload' => $upload,
            'data' => $data
        ]);
    }
    
    /**
     * Displays page for uploading csv-file with product list
     * 
     * @return mixed`
     */
    public function actionUpload()
    {
        
    }
}
