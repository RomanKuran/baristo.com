<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Product;
use app\models\Category;
use app\models\PostSearch;

class DevController extends Controller
{
    public $layout = "../dev/index";

    public function actionIndex($id=null)
    {
        if($id){
            
            $categoryId = Category::find()->where(['name' => $id])->one()["category_id"];
            $products = Product::find()->where(['category_id' => $categoryId])->all();
        }
        else{
            $products = null;
        }
        Yii::$app->view->params['category'] = Category::find()->all();
        return $this->render('index2', compact('products'));
    }
    

    public function actionSearch(){
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        Yii::$app->view->params['category'] = Category::find()->all();
        $products = Product::find()->all();
        return $this->render('qwe', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $products
        ]);
    }

    public function actionProducts()
    {
        $categoryId = Category::find()->where(['name' => $_GET["id"]])->one()["category_id"];
        $products = Product::find()->where(['category_id' => $categoryId])->all();
        $categories = Category::find()->all();
        // echo("<pre>");
        // var_dump($products);
        // echo("</pre>");
        // exit;
        return $this->render('index', compact("products", "categories"));
    }

}
