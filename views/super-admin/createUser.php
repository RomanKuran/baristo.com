<?php

use yii\helpers\Html;
use kartik\file\FileInput;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Створити користувача';

// -----create user-----

$form = ActiveForm::begin([
    'id'        => 'create-user-form',
    'action'    => ['add-user']
])?>

<!-- <h1 class="title">Категорії</h1> -->
<h3 class="text-center">Створити користувача</h3>


<?= $form->field($model_user, 'first_name');?>
<?= $form->field($model_user, 'surname'); ?>
<?= $form->field($model_user, 'email'); ?>
<?= $form->field($model_user, 'phone_number'); ?>


<?= $form->field($role_user, 'item_name')->dropDownList($roles); ?>

<?= Html::submitButton('Зберегти', ['class' => 'btn btn-primary btn-block']) ?>

<?php ActiveForm::end() ?>
