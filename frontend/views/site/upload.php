<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;

$form = ActiveForm::begin([
    'options' => [
        'enctype' => 'multipart/form-data',
        'method' => 'post'
    ]
]);

echo $form->field($upload, 'products')->fileInput();
echo Button::widget([
    'label' => 'Send',
    'options' => [
        'type' => 'submit'
    ]
]);

ActiveForm::end();