<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\User;

    $this->registerCssFile("/css/index_style.css", [
        'depends' => [\yii\bootstrap\BootstrapAsset::className()],
    ]);
    
    // var_dump($_GET);
    // exit;

    $categories = Yii::$app->view->params['category'];
?>
<!-- <link rel="stylesheet" href="/css/index_style.css"> -->

<div class="window">
    <div id="wind1">
        <?php Pjax::begin(); ?>

        <div class="categories">
            <div class="categories scroll">

                
            
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'summary' => false,

                    'columns' => [
                        

                        [
                            'attribute' => 'name',
                            'contentOptions' => ['class' => 'text-center'],
                            'format'=>'raw',
                            'value' => function($data)
                            {
                                $user_id = Yii::$app->user->identity->user_id;
                                $model_user = User::find()->where(['user_id'=>$user_id])->one();
                                $model_shop = $model_user->shop;
                                
                                return "<div class='category' style='background-image: url(/users/".$model_shop->shop_id."/categorys/$data->photo_name'><a href='/my-category/index?id=$data->name'><div>$data->name</div></a></div>";
                            }
                        ],
                    ],
                ]); ?>

            </div>
        </div>
        



        <div class="products">
            <div class="products scroll">

            
                <?= GridView::widget([
                    'dataProvider' => $dataProviderProduct,
                    'filterModel' => $searchModelProduct,
                    'summary' => false,

                    'columns' => [
                        [
                            'attribute' => 'name',
                            'contentOptions' => ['class' => 'text-center'],
                            'format'=>'raw',
                            'value' => function($data)
                            {
                                $user_id = Yii::$app->user->identity->user_id;
                                $model_user = User::find()->where(['user_id'=>$user_id])->one();
                                $model_shop = $model_user->shop;
                                
                                return "<div class='product' style='background-image: url(/users/".$model_shop->shop_id."/products/$data->photo_name'>

                                <div class='info'><div class='name' product='$data->product_id'>$data->name</div><div class='price'><div class='number'>$data->price</div><div>грн</div></div></div>";
                            }
                        ],
                    ],
                ]); ?>
                
                <!-- <a href='/my-category/index?id_product=$data->name'></a> -->
                
            </div>
        </div>


        <?php Pjax::end(); ?>


        <div class="check">
            <div class="container_title">
                <h4 class="title">Список</h4>
            </div>

            <div class="container_sum">
                <div class="text_sum">Сума:</div>
                <div class="sum">0грн</div>
            </div>
            <div class="action">
                <div class="buy">Купити</div>
                <div class="cancel">Відмінити</div>
            </div>

            <ul class="items">
                    <!-- items Check -->
            </ul>
        </div>
    </div>

<div class="input">
    <div class="container_name">
        <p>Назва продукту</p>
        <input class="name" type="text" disabled="disabled">
    </div>
    
    <div class="container_count">
        <p>Кількість</p>
        <div class="input_count">
            <input id="count" class="count" type="number" class="" value="1" min="1" step="1">
            <div class="buttons">
                <button class="upp"></button>
                <button class="down"></button>
            </div>
        </div>
    </div>

    <div class="container_price">
        <p>Ціна</p>
        <div class="input_price">
            <input class="price" type="text" disabled="disabled" value="0грн">
        </div>
    </div>

    <button class="ok">Ok</button>
</div>


</div>



<?
    $this->registerJsFile("/js/dev/index_script.js", ['depends'=> 'app\assets\AppAsset']);
?>





<!-- <button id="ok">Ok</button> -->


<script>


</script>

