<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    
<nav id="w2" class="navbar-inverse navbar-fixed-top navbar"><div class="container"><div class="navbar-header"><button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#w2-collapse"><span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button><a class="navbar-brand" href="/">My Application</a></div>
<div id="w2-collapse" class="collapse navbar-collapse">
<ul id="w3" class="navbar-nav navbar-right nav"><li><a href="/my-category/office">Мій кабінет</a></li>
<li><form action="/site/logout" method="post">

<input type="hidden" name="_csrf" value="I20X1k8rqLVg0W9XR2MiH0TrpRp-Tm7Payg_H71vG6ERXSWPF3Tw5i-WIhsQU0RSfLmWTih9ILhfQWt3xT1X8A==">
    <?php if(Html::encode(Yii::$app->user->identity->first_name)):?>
        <button type="submit" class="btn btn-link logout">Вийти (<?= Html::encode(Yii::$app->user->identity->first_name); ?>)</button>
    <?php else: ?>
        <button type="submit" class="btn btn-link logout">Увійти</button>
    <?php endif; ?>
</form></li></ul></div></div></nav>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        
        <?= $content ?>
        
    </div>
</div>

<!-- <footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer> -->

<?php $this->endBody() ?>
<script src="/assets/209faea/js/bootstrap.js"></script>
</body>
</html>
<?php $this->endPage() ?>
