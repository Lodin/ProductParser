<?php

use yii\bootstrap\ActiveForm;

$form = ActiveForm::begin([
    'options' => [
        'enctype' => 'multipart/form-data'
    ]
]);

$form->field($upload, 'data')->fileInput();

ActiveForm::end();