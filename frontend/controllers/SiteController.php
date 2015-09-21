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
        return $this->render('index');
    }
    
    /**
     * Displays page for uploading csv-file with product list
     * 
     * @return mixed`
     */
    public function actionUpload()
    {
        $upload = new Upload;
        
        if (!empty($_FILES)) {
            $products = $upload->parse();
            
            foreach ($products as $product) {
                $product->setSection();
                if (!$product->upload()) {
                    print_r($product->getErrors()); echo PHP_EOL;
                    print_r($product);
                    die();
                }
            }
        }
        
        return $this->render('upload', [
            'upload' => $upload
        ]);
    }
}
