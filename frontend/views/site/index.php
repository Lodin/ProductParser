<?php

/* @var $this yii\web\View */

use common\models\db\Product;
use yii\grid\GridView;

$this->title = 'Product list';

print_r(Yii::t('db/product', 'current_section'));
?>
<div class="row"><?php
    echo GridView::widget([
        'dataProvider' => $provider,
        'columns' => [
            'id',
            'section',
            'subsection',
            'brand',
            'model',
            'name',
            'article',
            'orientation',
            [
                'attribute' => 'color',
                'value' => function(Product $product) {
                    $result = !empty($product->colors)? '' : null;
                    
                    foreach ($product->colors as $color) {
                        if (!empty($result)) {
                            $result .= ', ';
                        }
                        
                        $result .= $color->getName();
                    }
                    
                    return $result;
                }
            ],
            [
                'attribute' => 'size',
                'value' => function(Product $product) {
                    $result = !empty($product->sizes)? '' : null;
                    
                    foreach ($product->sizes as $size) {
                        if (!empty($result)) {
                            $result .= ', ';
                        }
                        
                        $result .= $size->name;
                    }
                    
                    return $result;
                }
            ]
        ]
    ]);
?></div>