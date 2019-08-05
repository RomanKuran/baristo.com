<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\Product;
use app\models\Stock;
use app\models\Basket;
use app\models\Sold;
use app\models\CategorySearch;
use app\models\ProductSearch;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MyCategoryController implements the CRUD actions for Category model.
 */
class MyCategoryController extends Controller
{
    // /**
    //  * {@inheritdoc}
    //  */
    // public function behaviors()
    // {
    //     return [
    //         'verbs' => [
    //             'class' => VerbFilter::className(),
    //             'actions' => [
    //                 'delete' => ['POST'],
    //             ],
    //         ],
    //     ];
    // }



    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'get-price-product', 'get-sum-product', 
                        'sale', 'delete-product-from-list-products', 'clear-list-products', 
                        'sale-products', 'office'],
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                    // [
                    //     'allow' => true,
                    //     'roles' => ['@'],
                    // ],
                   
                ]
            ]
        ];
    }




    public function actionSale()
    {
        // $bar = $_POST['text'];
        $stock = Stock::find()->where(['product_id' => $_POST['text']])->one();
        // $product = Stock::findOne(31);
        $stock->quantity--;
        // var_dump($stock->quantity);
        // exit;
        $stock->update($attributeNames = null);

        return 0;
    }

    public function actionGetPriceProduct()
    {
        $product = Product::find()->where(['product_id' => $_POST['id']])->one();
        return $this->asJson(['price'=>$product->price]);
    }

    public function getContainsProduct($id_product_basket)
    {
        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();
        $model_shop = $model_user->shop;
        return $model_shop->getProducts()->where(['product_id' => $id_product_basket, 'active'=>true])->exists();
    }

    public function actionGetSumProduct()
    {
        $id_product_basket = $_POST['id'];
        $count_product_basket = $_POST['count'];
        $key = $_POST['key'];
        // $status_reserve = Stock::reserveProducts($id_product_basket, $count_product_basket, $key);

        // $user_id = Yii::$app->user->identity->user_id;
        // $model_user = User::find()->where(['user_id'=>$user_id])->one();
        // $model_shop = $model_user->shop;
        // $product = $model_shop->getProducts()->where(['product_id' => $id_product_basket])->exists();
        $product = $this->getContainsProduct($id_product_basket);
        if($product)
        {
            $status_reserve = Stock::reserveProducts($id_product_basket, $count_product_basket, $key);
            // return $this->asJson(['sum'=>$status_reserve]);

            if($status_reserve)
            {
                $product = Product::find()->where(['product_id' => $id_product_basket])->one();
                $sum = $product->price * $count_product_basket;
                $all_sum = $_POST['all_sum'] + $sum;

                return $this->asJson(['sum'=>round($sum, 2), 'all_sum'=>round($all_sum, 2), 'status_reserve'=>$status_reserve]);
            }
            else return $this->asJson(['status_reserve'=>$status_reserve]);
        }
        else return $this->asJson(['status_reserve'=>false]);
    }

    public function actionDeleteProductFromListProducts()
    {
        $id_product_basket = $_POST['id'];
        $index = $_POST['index'];
        $key = $_POST['key'];
        $products_id = $_POST['products_id'];
        $products_count = $_POST['products_count'];
        $all_sum = $_POST['all_sum'];

        $product_contains = $this->getContainsProduct($id_product_basket);
        
        if($product_contains)
        {   
            $product = Basket::find()->where(['product_id' => $id_product_basket, 'quantity' => $products_count[$index], 'seller_key' => $key])->one();
            if($product != null)
            {
                $product->delete();
                

                $product_update = Stock::find()->where(['product_id'=>$id_product_basket])->one();
                $product_update->quantity += $products_count[$index];
                $product_update->update($attributeNames = null);
                $all_sum -= ((Product::find()->where(['product_id' => $id_product_basket])->one())->price * $products_count[$index]);
                $products_items = array();
                
                array_splice($products_id, $index, 1);
                array_splice($products_count, $index, 1);
                
                for($i = 0; $i < count($products_id); $i++)
                {
                    $product_item_from_db = Product::find()->where(['product_id' => $products_id[$i]])->one();
                    $product_item = '<li class="item" product="'.$products_id[$i].'"><div class="info"><p>'.
                    $product_item_from_db->name.'</p><div class="price"><span id="price">'.($product_item_from_db->price*$products_count[$i]).'</span><span>грн</span></div>'.
                    '</div><div class="count">'.$products_count[$i].'шт</div><div class="delete"></div></li>';
                    array_push($products_items, $product_item);
                }
                return $this->asJson(['products_id' => $products_id, 'products_count'=>$products_count, 'status_update' => true, 'products_items' => $products_items, 'all_sum' => round($all_sum, 2)]);
            }
            else return $this->asJson(['status_update' => false]);
        }
        else return $this->asJson(['status_update' => false]);
    }

    public function actionClearListProducts()
    {
        $key = $_POST['key'];
        $products_id = $_POST['products_id'];
        $products_count = $_POST['products_count'];
        $status = array();

        if($key & count($products_id) > 0 & count($products_count) > 0 & count($products_count) == count($products_id))
        {
            for($i = 0; $i < count($products_id); $i++)
            {
                $product_contains = $this->getContainsProduct($products_id[$i]);
                if($product_contains)
                {   
                    $status_delete = Basket::deleteProduct($products_id[$i], $products_count[$i], $key);
                    if(!$status_delete) array_push($status, $products_id[$i]);
                    else Stock::addProducts($products_id[$i], $products_count[$i]);
                }
            }
            if(count($status)>0)
            {
                return $this->asJson(['status_clear' => 2, $status]);
            }
            else{
                return $this->asJson(['status_clear' => true]);
            }
        }
        else
        {
            return $this->asJson(['status_clear' => false]);
        }

    }

    public function actionSaleProducts()
    {
        $key = $_POST['key'];
        $products_id = $_POST['products_id'];
        $products_count = $_POST['products_count'];
        $status = array();

    
// var_dump($products_id);
// return $this->asJson(['status_clear' => true]);

        if($key && count($products_id) > 0 && count($products_count) > 0 && count($products_count) == count($products_id))
        {
            // return $this->asJson(['status_clear' => 1]);
            for($i = 0; $i < count($products_id); $i++)
            {
                $product_contains = $this->getContainsProduct($products_id[$i]);
                if($product_contains)
                {   
                    $status_delete = Basket::deleteProduct($products_id[$i], $products_count[$i], $key);

                    if(!$status_delete) array_push($status, $products_id[$i]);
                    else $number = Sold::addProduct($products_id[$i], $products_count[$i], $key);
                   
                    // return  $this->asJson(['status_clear'=>$number]);
                    // return $this->asJson(['status_clear' => Sold::test()]);

                }
            }
            
            if(count($status)>0)
            {
                return $this->asJson(['status_clear' => 2, $status]);
            }
            else{
                return $this->asJson(['status_clear' => true]);
            }
        }
        else
        {
            return $this->asJson(['status_clear' => false]);
        }

    }


    public function getCategorys()
    {
        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();

        $model_shop = $model_user->shop;
        $categories = $model_shop->categorys;

        return $categories;
    }


    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex($id=null)
    {
        // if($id){
        //     // $categoryId = Category::find()->where(['name' => $id])->one()["category_id"];
        //     // $products = ProductSearch::find()->where(['category_id' => $categoryId])->all();
        //     $categoryId = $this->getCategorys()[0]->category_id;
        //     $products = ProductSearch::find()->where(['category_id' => $categoryId])->all();
        // }
        // else{
        //     $products = null;
        // }
        // var_dump($products );
        // exit;
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $searchModelProduct = new ProductSearch();
        $dataProviderProduct = ($searchModelProduct->search(Yii::$app->request->queryParams));

        // Якщо є PJAX ця штука обов'язкова!!!!!
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'searchModelProduct' => $searchModelProduct,
                'dataProviderProduct' => $dataProviderProduct,
            ]);
        }
        else{
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'searchModelProduct' => $searchModelProduct,
                'dataProviderProduct' => $dataProviderProduct,
            ]);
        }
    }


    public function actionOffice()
    {
        return $this->render('office');
    }

}
