<?php

/* @var $this yii\web\View */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\User;

AppAsset::register($this);
$this->registerCssFile('/css/admin/sidebar.css', [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
]);
$url = $_SERVER['REQUEST_URI'];
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

    <nav id="w0" class="navbar-inverse navbar-fixed-top navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <img src="/images/siteIcons/logo.svg" style="height: 100%" alt="" />
            </a>
            <div class="navbar-header">
                <button id="burger" type="button" class="navbar-toggle icon">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!-- <a class="navbar-brand" href="/">
                    <img src="/images/siteIcons/logo.svg" height="100%" alt="qwert">
                </a> -->
            </div>
            <div id="w0-collapse" class="collapse navbar-collapse">
                <!-- <ul class="nav navbar-nav navbar-right">
                  <li ><form action="/site/logout" method="post"><input type="hidden" name="_csrf"><button class="logout" type="submit"><img class="icon-menu" src="/images/icons/logout.svg"></button></form></li>
                </ul> -->
            </div>
        </div>
    </nav>

    <div class="property-sidebar">

        <div id="sidebar">
            <div class="container-fluid"></div>

            <ul class="nav navbar-nav side-bar" id="myTopnav">

                <li class="side-bar <?= (strpos($url, '/admin/add-product') !== false) ? "active-menu" : ""; ?>"><a
                            href="/admin/add-product"><span>&nbsp;</span><img class="icon-menu" src="/images/pk.svg" alt="" >Категорії-продукти</a>
                </li>

                <li class="side-bar <?= (strpos($url, '/admin/categorys') !== false) ? "active-menu" : ""; ?>"><a
                            href="/admin/categorys"><span>&nbsp;</span> <img class="icon-menu" src="/images/category.svg" alt="" >Категорії</a>
                </li> 

                <li class="side-bar <?= (strpos($url, '/admin/products') !== false) ? "active-menu" : ""; ?>"><a
                            href="/admin/products"><span>&nbsp;</span> <img class="icon-menu" src="/images/product.svg" alt="" >Продукти</a>
                </li> 

                <li class="side-bar <?= (strpos($url, '/admin/change-quantity') !== false) ? "active-menu" : ""; ?>"><a
                            href="/admin/change-quantity"><span>&nbsp;</span> <img class="icon-menu" src="/images/money.svg" alt="" >Прихід / Списання</a>
                </li> 

                <li class="side-bar <?= (strpos($url, '/site/logout') !== false) ? "active-menu" : ""; ?> size">
                    <form action="/site/logout" method="post"><input type="hidden" name="_csrf">
                        <button class="logout" type="submit"><img class="icon-menu" src="/images/logout.svg" alt="" >Вийти
                            (<?= Html::encode(Yii::$app->user->identity->first_name); ?>)
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </div>

    <div class="container-fluid content-page">

        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>

    </div>
</div>

<?php $this->endBody() ?>
<script src='/js/super_admin/sidebar.js'></script>
<script src="/assets/209faea/js/bootstrap.js"></script>
</body>
</html>
<?php $this->endPage() ?>
