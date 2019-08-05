<?php

use yii\helpers\Html;
use kartik\file\FileInput;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\User;


$this->title = 'Кількість продуктів';
?>

<h1 class="title text-center">Кількість продуктів</h1>
<div id="sukes"></div>
<?php
    echo GridView::widget([
        'dataProvider' => $dataProviderProduct,
        'filterModel' => $searchModelProduct,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
 

            'name',
            'price',
            [
                'attribute' => 'Зображення',
                'value' => function($model){

                    $user_id = Yii::$app->user->identity->user_id;
                    $model_user = User::find()->where(['user_id'=>$user_id])->one();
                    $model_shop = $model_user->shop;
                   
                    return "<div class='product' style='background-image: url(/users/".$model_shop->shop_id."/products/".$model['photo_name']."'>";
                },
                'format' => 'raw',
                // 'authAssignment.item_name'
            ],

            [
                'class'     => 'yii\grid\ActionColumn',
                'template'  => '{blog-edit}{blog-delete}',
                'buttons'   => [
                    'blog-edit'     => function ($url, $model, $key) {
                        // echo("<pre>");
                        // var_dump($model["user_id"]);
                        // echo("</pre>");
                        // exit;

                        $_GET['product_id'] = $model->product_id; 
                        // echo "<button type='submit'>ok</button>";
                        // var_dump($_GET['bar']);
                        // exit;
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '#'/*'/admin/plus?id=' . $model["product_id"]*/,
                            [
                                'title' => Yii::t('app', 'Редагувати запис'),
                                'class' => 'btn btn-success btn-action',
                                'style' => 'margin: 5px',
                                'data-toggle'=>"modal",
                                'data-target'=>"#myModal",
                                
                            ]
                        );
                    },
                    'blog-delete'   => function ($url, $model, $key) {return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#'/*'/admin/minus?id=' . $model["product_id"]*/,
                            [
                                'title' => Yii::t('app', 'Редагувати запис'),
                                'class' => 'btn btn-danger btn-action',
                                'style' => 'margin-left: 5px',
                                // 'data'  => [
                                //     // 'confirm'   => Yii::t('app', 'Ви дійсно хочете ДЕАКТИВУВАТИ цей продукт?'),
                                //     'method'    => 'post'
                                // ]
                                'data-toggle'=>"modal",
                                'data-target'=>"#myModal",
                            ]
                        );
                    }
                ],
                'contentOptions' => [
                    'style' => 'width: 120px'
                ]
            ]
        ],
    ]);

?>




    <div class="container">
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Прихід товару</h4>
                </div>
                <div id="modal_error"></div>
                <!-- <div class="modal-body">
                <input type="text" id="count">
                </div> -->
                
                    <form class="modal-footer coming">
                        <input id="form-token" type="hidden" name="<?=Yii::$app->request->csrfParam?>"
                        value="<?=Yii::$app->request->csrfToken?>"/>
                        <input type="hidden" class="hidden_data_key" name="product_id" value="" /> 
                        <label for="count">Кількість:</label>
                        <input type="number" class="form-control" id="count" name="count">
                        <label for="comment">Коментар:</label>
                        <textarea class="form-control" rows="5" id="comment" name="comment"></textarea>
                        <label for="comment">Дата за яку прийшов товар:</label>
                        <input type="date" name="date" value="<?=date('Y-m-d')?>">

                        
                    </form>
                    <button class="btn btn-default ok" id="coming_ok">Ok</button>
                    <button type="button" class="btn btn-default b_close" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>




    <?php $this->registerJsFile(Yii::$app->request->baseUrl.'/js/dev/changeQuantity_script.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<style>
.product{
    height: 100px; 
    width: 100px;

    background-position: center;
    background-repeat: no-repeat;
    background-size: contain;
}

.glyphicon.glyphicon-pencil{
    background-image: url("/images/plus-symbol.svg");
    height: 13px; 
    width: 13px;
    margin-left: 1px;
    /* background-position: center;
    background-repeat: no-repeat; */
    background-size: contain;
}
.glyphicon.glyphicon-trash{
    background-image: url("/images/minus-symbol.svg");
    height: 13px; 
    width: 13px;
    margin-left: 1px;
    /* background-position: center;
    background-repeat: no-repeat; */
    background-size: contain;
}
.glyphicon-pencil:before, .glyphicon-trash:before{
    content: none;
}
</style>


