
<?php $this->beginContent(Yii::getAlias('@views') . '/layouts/main.php'); ?>
<?
    

    $this->registerCssFile("/css/index_style.css", [
        'depends' => [\yii\bootstrap\BootstrapAsset::className()],
    ]);
    $URL .= '&category=1';
    // var_dump($URL);
    // exit;
    $categories = Yii::$app->view->params['category'];
?>
<link rel="stylesheet" href="/css/index_style.css">

<?php yii\widgets\Pjax::begin();?>
<div class="categories">
    <input class="form-control fast-search-input" id="filter_input_category" placeholder="Пошук категорії">
    <div class="categories scroll">
        <ul  class="list-group fast-search-list" id="filter_list_category">

            <?foreach($categories as $category):?>
                
                    <li class="category" style="background-image: url('/users/user1/categorys/<?=$category['photo_name']?>')">
                        <a href="/dev/index?id=<?=$category['name']?>">
                            <div><?=$category['name']?></div>
                        </a>
                    </li>
                
            <? endforeach; ?>

        </ul>
    </div>
</div>


<?=$content?>

<?php yii\widgets\Pjax::end();?>


    <!-- <div class="products container">
        <input class="form-control fast-search-input" id="filter_input_product" placeholder="Пошук товару">
        <div class="products scroll">
            <ul class="list-group fast-search-list" id="filter_list_product">
                
                <? if($products) foreach($products as $product):?>
                    <li class="product"> 
                        <div><?=$product['name']?></div> 
                    </li>
                <? endforeach; ?>

            </ul>
        </div>
    </div> -->


<script
        src="https://code.jquery.com/jquery-3.3.1.js"
        integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
        crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"
        integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"
        integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm"
        crossorigin="anonymous"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"
        integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+"
        crossorigin="anonymous"></script>
<?
    $this->registerJsFile("/js/dev/index_script.js");
?>


<?php $this->endContent(); ?>

