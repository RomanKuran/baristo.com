<?php

use yii\helpers\Html;
use kartik\file\FileInput;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Категорії-продукти';


// -----add category-----

$form = ActiveForm::begin([
    'id'        => 'Category-form',
    'action'    => 'category-upload',
    'options' => ['enctype' => 'multipart/form-data']
])?>



<h1 class="title text-center">Категорії</h1>
<?= $form->field($category, 'name'); ?>


<?= $form->field($category, 'image')->widget(FileInput::class, [
    'options'       => [
        'accept' => 'image/*',
        'multiple'  => false
    ],
    'pluginOptions' => [
        // 'initialPreview'        => [$category->photo_name],
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

<?= Html::submitButton('Добавить', ['class' => 'btn btn-primary btn-block']) ?>

<?php ActiveForm::end() ?>

<hr>
 <!-- -----add product----- -->

<h1 class="title text-center">Продукти</h1>

<?$form = ActiveForm::begin([
    'id'        => 'Product-form',
    'action'    => 'product-upload'
])?>

<?= $form->field($product, 'name'); ?>
<?= $form->field($product, 'price'); ?>
<?= $form->field($stock, 'quantity'); ?>

<?= $form->field($product, 'category_id')->dropDownList($categories); ?>

<?= $form->field($product, 'image')->widget(FileInput::class, [
    'options'       => [
        'accept' => 'image/*',
        'multiple'  => false
    ],
    'pluginOptions' => [
        'initialPreview'        => [$product->photo_name],
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

<?= Html::submitButton('Добавить', ['class' => 'btn btn-primary btn-block']) ?>

<?php ActiveForm::end() ?>

<style>
.content-page{
    margin-bottom: 40px;
}
</style>