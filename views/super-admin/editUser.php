<?php

use yii\helpers\Html;
use kartik\file\FileInput;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Редагування користувача';

// -----edit user-----

$form = ActiveForm::begin([
    'id'        => 'update-user-form',
    'action'    => ['update-user?id=' . $id]
])?>

<h3 class="text-center">Редагування користувача</h3>

<?= $form->field($editModel, 'first_name');?>
<?= $form->field($editModel, 'surname'); ?>
<?= $form->field($editModel, 'email'); ?>
<?= $form->field($editModel, 'phone_number'); ?>

<?if($editModel->item_name == 'user' || $editModel->item_name == 'canUser'):?>
    <?=$form->field($editModel, 'item_name')->dropDownList($roles, ['options'=>array('canUser' => ['selected'=>true])]);?>
<?endif;?>
<?if($editModel->item_name == 'admin' || $editModel->item_name == 'canAdmin'):?>
    <?=$form->field($editModel, 'item_name')->dropDownList($roles, ['options'=>array('canAdmin' => ['selected'=>true])]);?>
<?endif;;?>

<?= Html::submitButton('Зберегти', ['class' => 'btn btn-primary btn-block']) ?>

<?php ActiveForm::end() ?>
