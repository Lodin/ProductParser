<?php
namespace frontend\controllers;

use Yii;
use common\models\forms\Upload;
use common\models\db\Product;
use yii\web\Controller;
use yii\data\ActiveDataProvider;

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
        $provider = new ActiveDataProvider([
            'query' => Product::find()
                ->joinWith('colors')
                ->joinWith('sizes')
        ]);
        
        return $this->render('index', [
            'provider' => $provider,
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);
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
