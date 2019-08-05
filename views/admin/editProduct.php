<?php

use yii\helpers\Html;
use kartik\file\FileInput;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Редагування продукту';

// -----edit product-----

$form = ActiveForm::begin([
    'id'        => 'update-product-form',
    'action'    => ['update-product?id='.$id.'&category_id='.$editModel->category_id ],
    'options' => ['enctype' => 'multipart/form-data']
])?>

<!-- <h1 class="title">Категорії</h1> -->

<h1 class="title text-center">Редагування продукту</h1>

<?= $form->field($editModel, 'name');
// echo('<pre>');
// var_dump($editModel);
// echo('</pre>');
// exit;


?>
<?= $form->field($editModel, 'price'); ?>

<?= $form->field($editModel, 'image')->widget(FileInput::class, [
    'options'       => [
        'accept' => 'image/*',
        'multiple'  => false
    ],
    'pluginOptions' => [
        'initialPreview'        => [$product->photo_way],
        'uploadUrl'             => Url::to(['/images']),
        'initialPreviewAsData'  => true,
        'showPreview'           => true,
        'showCaption'           => false,
        'showRemove'            => false,
        'showUpload'            => false,
        'browseClass'           => 'btn btn-success btn-block',
        'uploadClass'           => 'btn btn-info',
        'removeClass'           => 'btn btn-danger'
    ],
]); ?>

<?= Html::submitButton('Зберегти', ['class' => 'btn btn-primary btn-block']) ?>

<?php ActiveForm::end() ?>
