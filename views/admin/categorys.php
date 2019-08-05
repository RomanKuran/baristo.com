<?php

use yii\helpers\Html;
use kartik\file\FileInput;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\User;

$this->title = 'Категорії';

// echo("<pre>");
// var_dump($dataProviderCategory);
// echo("</pre>");
// exit;

?>

<h1 class="title text-center">Категорії</h1>

<?php echo GridView::widget([
        'dataProvider' => $dataProviderCategory,
        'filterModel' => $searchModelCategory,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
 

            'name',
            [
                'attribute' => 'Зображення',
                'value' => function($model){

                    $user_id = Yii::$app->user->identity->user_id;
                    $model_user = User::find()->where(['user_id'=>$user_id])->one();
                    $model_shop = $model_user->shop;

                    return "<div class='category' style='background-image: url(http://baristo/users/".$model_shop->shop_id."/categorys/".$model['photo_name'].")'>";
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
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '/admin/edit-category?id=' . $model["category_id"] /*. "&rule=" . $model["authAssignment"]["item_name"]*/,
                            [
                                'title' => Yii::t('app', 'Редагувати запис'),
                                'class' => 'btn btn-success btn-action',
                                'style' => 'margin: 5px'
                            ]
                        );
                    },
                    'blog-delete'   => function ($url, $model, $key) {return Html::a('<span class="glyphicon glyphicon-trash"></span>', '/admin/delete-category?id=' . $model["category_id"],
                            [
                                'title' => Yii::t('app', 'Редагувати запис'),
                                'class' => 'btn btn-danger btn-action',
                                'style' => 'margin-left: 5px',
                                'data'  => [
                                    'confirm'   => Yii::t('app', 'Ви дійсно хочете ВИДАЛИТИ цю категорію? (при видаленні категорії ДЕАКТИВУЮТЬСЯ всі продукти, які були в ній)'),
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
.category{
    height: 100px; 
    width: 100px;

    background-position: center;
    background-repeat: no-repeat;
    background-size: contain;
}

</style>


