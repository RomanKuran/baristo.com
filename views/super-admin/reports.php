<?php

use yii\helpers\Html;
use kartik\file\FileInput;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
// use dosamigos\datepicker\DatePicker; Колись пошукати!

$this->title = 'Звіти';  
$mon_sum = 0;
?>
    <h3 class="text-center">Звіти</h3>

    <div class="header_container">

    <div class="container_controller">
        <form action="reports" class="container_dates">
            <div class="container_date">
                <h3>Від:</h3>
                <input type="date" id="start_date" name="start_date" value="<?=$start_this_date?>"/ >
            </div>
            <div class="container_date">
                <h3>До:</h3>
                <input type="date" id="end_date" name="end_date" value="<?=$this_date?>"/ >
            </div>
            <button type="submit" class="btn">Сформувати звіт...</button>
        </form> 
        <a href="download-reports"><button class="btn">Скачати звіт...</button></a>
    </div>

        <div class="container_sum">
            <h3 class="sum_title">Загальна сума:</h3>
            <h3 id="sum_id">0грн</h3>
        </div>
    </div>
    

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
 

            'name',
            'price',
            [
                'attribute' => 'Активний',
                'value' => function($model){
                    if($model->active){
                        return "+";
                    }
                    else return "-";
                    
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'залишок',
                'value' => function($model){
                    return $model->stocks[0]->quantity;
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'Прихід',
                'value' => function($model){
                    if($model->sum_coming){
                        return $model->sum_coming;
                    }
                    else return 0;
                    
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'Списано',
                'value' => function($model){
                    if($model->sum_withdraw){
                        return $model->sum_withdraw;
                    }
                    else return 0;
                    
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'продано',
                'contentOptions' => ['class' => 'sold'],
                'value' => function($model){
                    if($model->total){
                        return $model->total;
                    }
                    else return 0;
                    
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'сума',
                'value' => function($model) use(&$mon_sum){
                    if($model->total){
                        // var_dump($mon_sum);
                        // exit;
                        $sum = $model->total * $model->price;
                        $mon_sum += $sum;
                        return $sum;
                    }
                    else return 0;
                    
                },
                'format' => 'raw',
            ],

        ],
    ]);
?>

<script>
    document.getElementById('sum_id').innerHTML = <?=$mon_sum?>+"Грн"
</script>

<style>
.sold{
    background-color: #99cc03;
}
.header_container{
    display: flex;
    justify-content: space-between;
}
.container_date{
    display: inline-flex;
}
.container_date h3{
    margin: 0px 10px;
}
.container_dates, .container_sum, .container_controller{
    display: flex;
    align-items: center;
}
.container_controller a{
    text-decoration: none;
    color: #333;
    margin-left: 10px;
}
.container_dates button{
    margin-left: 10px;
}
#sum_id{
    color: green;
    margin-right: 10px;
    margin-left: 10px;
    border-bottom: 2px solid green;
}
</style>