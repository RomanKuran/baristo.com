<?php

use yii\helpers\Html;
use kartik\file\FileInput;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Baristo';

// -----create shop-----

$form = ActiveForm::begin([
    'id'        => 'create-shop-form',
    'action'    => ['sent-message']
]);?>

<h1 class="title">Вас вітає форма реєстрації "Baristo"</h1>


<?= $form->field($model_reserve_shop, 'surname'); ?>
<?= $form->field($model_reserve_shop, 'first_name'); ?>
<?= $form->field($model_reserve_shop, 'email'); ?>
<?= $form->field($model_reserve_shop, 'phone_number'); ?>
<?= $form->field($model_reserve_shop, 'login'); ?>
<?= $form->field($model_reserve_shop, 'password')->passwordInput(); ?>


<?= $form->field($model_reserve_shop, 'shop_name');?>


<?= Html::submitButton('Подати заявку', ['class' => 'btn btn-primary btn-block']) ?>

<?php ActiveForm::end() ?>
