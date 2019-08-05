<?php

use yii\helpers\Html;
use kartik\file\FileInput;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Персонал';

// var_dump($dataProvider);
// exit;

    // echo GridView::widget([
    //     'id'            => 'all_personnel',
    //     'dataProvider'  => $dataProvider,
    //     // 'layout'        => '{items}{summary}{pager}',
    //     'columns'       => [
    //         [
    //             // 'attribute' => 'id_category',
    //             'format'    => 'html',
    //             'value'     => function ($model) {
    //                 var_dump($model);
    //                 exit;
    //             }
    //         ],
            
    //     ]
    // ]);
    ?>
    <h3 class="text-center">Персонал</h3>
    <a href="/super-admin/create-user" class="btn btn-primary btn-block">Добавити користувача</a><br><br>
    <?
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
 

            'first_name',
            'surname',
            'email',
            'phone_number',
            [
                'attribute' => 'role',
                'value' => function($model){
                    return $model["authAssignment"]["item_name"];
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
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '/super-admin/edit-user?id=' . $model["user_id"] . "&rule=" . $model["authAssignment"]["item_name"],
                            [
                                'title' => Yii::t('app', 'Редагувати запис'),
                                'class' => 'btn btn-success btn-action',
                                'style' => 'margin: 5px'
                            ]
                        );
                    },
                    'blog-delete'   => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', '/super-admin/delete-user?id=' . $model["user_id"],
                            [
                                'title' => Yii::t('app', 'Редагувати запис'),
                                'class' => 'btn btn-danger btn-action',
                                'style' => 'margin-left: 5px',
                                'data'  => [
                                    'confirm'   => Yii::t('app', 'Ви дійсно хочете ДЕАКТИВУВАТИ цього користувача?'),
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




