<?php

use yii\helpers\Html;
use kartik\file\FileInput;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\User;

$this->title = 'Продукти';

// echo("<pre>");
// var_dump($dataProviderProduct);
// echo("</pre>");
// exit;

// var_dump($model_shop);
//                     exit;
?>
<h1 class="title text-center">Продукти</h1>
<?php
    echo GridView::widget([
        'dataProvider' => $dataProviderProduct,
        'filterModel' => $searchModelProduct,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
 

            'name',
            'price',
            // 'photo_way',
            // 'photo_name',
            // 'category_id',
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
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '/admin/edit-product?id=' . $model["product_id"] /*. "&rule=" . $model["authAssignment"]["item_name"]*/,
                            [
                                'title' => Yii::t('app', 'Редагувати запис'),
                                'class' => 'btn btn-success btn-action',
                                'style' => 'margin: 5px'
                            ]
                        );
                    },
                    'blog-delete'   => function ($url, $model, $key) {return Html::a('<span class="glyphicon glyphicon-trash"></span>', '/admin/delete-product?id=' . $model["product_id"],
                            [
                                'title' => Yii::t('app', 'Редагувати запис'),
                                'class' => 'btn btn-danger btn-action',
                                'style' => 'margin-left: 5px',
                                'data'  => [
                                    'confirm'   => Yii::t('app', 'Ви дійсно хочете ДЕАКТИВУВАТИ цей продукт?'),
                                    'method'    => 'post'
                                ]
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

<style>
.product{
    height: 100px; 
    width: 100px;

    background-position: center;
    background-repeat: no-repeat;
    background-size: contain;
}

</style>


