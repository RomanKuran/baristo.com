

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


 $form = ActiveForm::begin([
        // 'action' => Url::to(['/product/view']),
        'method' => 'get',
        'options' => ['class' => 'form-inline form-group form-group-sm col-xs-12'],
        'fieldConfig' => [
            'template' => "{input}",
        ],
    ]); ?>
    </div>

    <!--<nobr><?= $form->field($model, 'nome')->textInput(['placeholder' => 'Nome']) ?>-->

    <nobr>
        <?= $form->field($searchModel, 'categoria')->dropDownList(ArrayHelper::map(Categorias::find()->all(), 'categoria','categoria'), ['prompt'=>Yii::t('yii', 'Escolha a categoria...')])  ?>
        <?= Html::submitButton(Yii::t('app', 'Pesquisar'), ['class' => 'btn btn-warning']) ?>
    </nobr>

    <?php ActiveForm::end(); ?>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_view2',
    ]); ?>