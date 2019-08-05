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
use app\models\Stock;
use app\models\User;
use app\models\Shop;
use app\models\CategoryShop;
use app\models\ProductSearch;
use app\models\CategorySearch;
use app\models\Coming;
use app\models\Withdraw;
use yii\web\UploadedFile;


class AdminController extends Controller
{
    public $layout = "admin_layout";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['add-product', 'update-product', 'product-upload',
                        'category-upload', 'products', 'edit-product', 'delete-product',
                        'categorys', 'edit-category', 'update-category', 'delete-category',
                        'change-quantity', 'minus', 'plus'],

                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    // [
                    //     'allow' => true,
                    //     'roles' => ['@'],
                    // ],

                ]
            ]
        ];
    }


    public function getCategorys()
    {

        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();

        $model_shop = $model_user->shop;
        $cats = 878model_shop->categorys;

        $categories = [];
        foreach($cats as $c)
            $categories[$c->category_id] =  $c->name;
        return $categories;
    }
    public function getUser()
    {
        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();
        return $model_user;
    }
    public function getShopFromThisUser()
    {
        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();
        return $model_user->shop;
    }
    //------------------------------------------Add Product-----------------------------------------

    public function actionAddProduct()
    {
        $categories = $this->getCategorys();
        $product = new Product();
        $category = new Category();
        $stock = new Stock();
        return $this->render('addProduct', compact('product', 'category', 'categories', 'stock'));
    }

    public function actionProductUpload()
    {

        $product = new Product();

            if ($product->load(Yii::$app->request->post()))
            {
                $model_user = $this->getUser();
                $model_shop = $model_user->shop;
                $product_contains = $model_shop->getProducts()->where(['name'=>$product->name, 'active'=>true])->exists();
                if(!$product_contains)
                {
                    $photo = UploadedFile::getInstance($product, 'image');
                    if ( $photo != null )
                    {
                        $rand = rand(0, 9999);
                        $newName = $rand . date("Y_m_d") . '.' . $photo->name;
                        // $product->photo_way = '/images/' . $newName;
                        Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web';
                        $path =  'users/'.$model_shop->shop_id.'/products/' . $newName;
                        $photo->saveAs($path);
                        $product->photo_name = $newName;
                        $product->active = true;
                        $product->date = getdate()[0];

                        if($product->price > 0 && $_POST["Stock"]["quantity"] > 0)
                        {
                            $product->save();

                            $shop_id = $this->getShopFromThisUser()->shop_id;

                            $stock = new Stock();
                            $stock->quantity = $_POST["Stock"]["quantity"];
                            $stock->product_id = $product->product_id;
                            $stock->shop_id = $shop_id;
                            $stock->save();
                        }
                        else
                        {
                            Yii::$app->session->setFlash('error', 'Ви ввели некоректні данні!!');
                            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
                        }
                    }
                    else
                    {
                        Yii::$app->session->setFlash('error', 'Ви не вибрали зображення для товару!!');
                    }
                }
                else
                {
                    Yii::$app->session->setFlash('error', 'В вас вже існує такий товар, Переназвіть його!!');
                    return $this->redirect(['add-product']);
                }
            }
            $product = new Product();
            Yii::$app->session->setFlash('success', 'Новий продукт успішно добавлено!!');
            return $this->redirect(['add-product']);

    }

    //------------------------------------------Add Category-----------------------------------------

    public function actionCategoryUpload()
    {
        $category = new Category();

        if ($category->load(Yii::$app->request->post())) {

            $model_user = $this->getUser();
            $model_shop = $model_user->shop;
            $category_contains = $model_shop->getCategorys()->where(['name'=>$category->name])->exists();

            if(!$category_contains)
            {
                $photo = UploadedFile::getInstance($category, 'image');
                if ( $photo != null )
                {
                    $rand = rand(0, 9999);
                    $newName = $rand . date("Y_m_d") . '.' . $photo->name;
                    // $category->photo_way = '/images/' . $newName;
                    Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web';
                    $path = 'users/'. $model_shop->shop_id .'/categorys/' . $newName;
                    $photo->saveAs($path);
                    $category->photo_name = $newName;
                    $category->save();

                    $model_category_shop = new CategoryShop();
                    $model_category_shop->category_id = $category->category_id;
                    $model_category_shop->shop_id = $this->getShopFromThisUser()->shop_id;
                    $model_category_shop->save();
                }
                else
                {
                    Yii::$app->session->setFlash('error', 'Ви не вибрали зображення для категорії!!');
                }
            }
            else
            {
                Yii::$app->session->setFlash('error', 'В вас вже існує така категорія, Переназвіть її!!');
                return $this->redirect(['add-product']);
            }
        }
        $category = new Category();
        Yii::$app->session->setFlash('success', 'Нова категорія успішно добавлена!!');
        return $this->redirect(['add-product']);
    }

    //------------------------------------------Edit Product-----------------------------------------

    public function actionProducts()
    {
        // $searchModel = new CategorySearch();
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $searchModelProduct = new ProductSearch();
        $dataProviderProduct = ($searchModelProduct->allSearch(Yii::$app->request->queryParams));
        $model_shop = Shop::getShopFromThisUser(Yii::$app->user->identity->user_id);

        // Якщо є PJAX ця штука обов'язкова!!!!!
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('products', [
                // 'searchModel' => $searchModel,
                // 'dataProvider' => $dataProvider,
                'searchModelProduct' => $searchModelProduct,
                'dataProviderProduct' => $dataProviderProduct,
            ]);
        }
        else{
            return $this->render('products', [
                // 'searchModel' => $searchModel,
                // 'dataProvider' => $dataProvider,
                'searchModelProduct' => $searchModelProduct,
                'dataProviderProduct' => $dataProviderProduct,
                'model_shop' => $model_shop,
            ]);
        }
    }

    public function containsProduct($id, $category_id)
    {
        if($category_id > -1)
            $model_product = Product::find()->where(['product_id'=>$id, 'category_id'=>$category_id, 'active' => true])->one();
        else
            $model_product = Product::find()->where(['product_id'=>$id, 'active' => true])->one();


        $model_stock = Stock::find()->where(['product_id'=>$model_product->product_id])->one();
        $model_shop = $model_stock->shop;

        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();

        if($model_user->shop->shop_id === $model_shop->shop_id)
        {
            return true;
        }
        else return false;
    }

    public function actionEditProduct($id)
    {
        if($this->containsProduct($id, -1))
        {
            $editModel = Product::findOne($id);
            return $this->render('editProduct', compact('editModel', 'id'));
        }
        else{
            Yii::$app->session->setFlash('error', 'В вас немає такого товару!');
            return $this->redirect('products');
        }

    }

    public function actionUpdateProduct($id, $category_id)
    {
        $product = new Product();
        if ($product->load(Yii::$app->request->post()))
        {
            $model_product_old = Product::find()->where(["product_id"=>$id])->one();
            if(Product::containsProduct($id, $category_id, $product->name))
            {
                $product->category_id = $category_id;
                $product->active = true;

                $photo = UploadedFile::getInstance($product, 'image');

                if ( $photo != null && $product->price>0)
                {
                    $model_shop = Shop::getShopFromThisUser(Yii::$app->user->identity->user_id);
                    $rand = rand(0, 9999);
                    $newName = $rand . date("Y_m_d") . '.' . $photo->name;
                    // $product->photo_way = '/images/' . $newName;
                    Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web';
                    $path = 'users/'.$model_shop->shop_id.'/products/' . $newName;
                    $photo->saveAs($path);
                    $product->photo_name = $newName;
                    $model_product = Product::find()->where(['product_id'=>$id])->one();

                    if (file_exists(Yii::$app->basePath.'/web/users/'.$model_shop->shop_id.'/products/'.$model_product->photo_name))
                    {
                        unlink(Yii::$app->basePath.'/web/users/'.$model_shop->shop_id.'/products/'.$model_product->photo_name);
                    }
                }
                else{
                    $product->photo_name = $model_product_old->photo_name;
                }
                if($product->validate() && $product->price>0)
                {
                    $product->save();

                    $model_product = Product::find()->where(['product_id'=>$id])->one();
                    $model_product->active = false;
                    $model_product->save();

                    $shop_id = $this->getShopFromThisUser()->shop_id;

                    $model_stock = Stock::find()->where(['product_id'=>$id])->one();
                    $stock = new Stock();
                    $stock->quantity = $model_stock->quantity;
                    $stock->product_id = $product->product_id;
                    $stock->shop_id = $shop_id;
                    $stock->save();

                    Yii::$app->session->setFlash('success', 'Продукт успішно редагований!');
                    return $this->redirect('products');
                }
                else{
                    Yii::$app->session->setFlash('error', 'Ви ввели некоректні данні!');
                    return $this->redirect('products');
                }
            }
        }
        Yii::$app->session->setFlash('error', 'Щось пішло не так!');
        return $this->redirect('products');
    }

    public function actionDeleteProduct($id)
    {
        if($this->containsProduct($id, -1))
        {
            $product = Product::findOne($id);
            $product->active = false;
            $product->save();

            $model_shop = Shop::getShopFromThisUser(Yii::$app->user->identity->user_id);
            if (file_exists(Yii::$app->basePath.'/web/users/'.$model_shop->shop_id.'/products/'.$product->photo_name))
            {
                unlink(Yii::$app->basePath.'/web/users/'.$model_shop->shop_id.'/products/'.$product->photo_name);
            }

            Yii::$app->session->setFlash('success', 'Продукт успішно деактивований!');
            return $this->redirect('products');
        }
    }

    //------------------------------------------Edit Category-----------------------------------------

    public function actionCategorys()
    {
        // $searchModel = new CategorySearch();
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $searchModelCategory = new CategorySearch();
        $dataProviderCategory = ($searchModelCategory->search(Yii::$app->request->queryParams));
        $model_shop = Shop::getShopFromThisUser(Yii::$app->user->identity->user_id);

        // Якщо є PJAX ця штука обов'язкова!!!!!
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('categorys', [
                // 'searchModel' => $searchModel,
                // 'dataProvider' => $dataProvider,
                'searchModelCategory' => $searchModelCategory,
                'dataProviderCategory' => $dataProviderCategory,
            ]);
        }
        else{
            return $this->render('categorys', [
                // 'searchModel' => $searchModel,
                // 'dataProvider' => $dataProvider,
                'searchModelCategory' => $searchModelCategory,
                'dataProviderCategory' => $dataProviderCategory,
            ]);
        }
    }

    public function actionEditCategory($id)
    {
        $editModel = Category::findOne($id);
        if(Category::containsCategory($id, $editModel->name))
        {
            return $this->render('editCategory', compact('editModel', 'id'));
        }
        else{
            Yii::$app->session->setFlash('error', 'В вас немає такої категорії!');
            return $this->redirect('categorys');
        }
    }

    public function actionUpdateCategory($id)
    {
        $category = Category::find()->where(["category_id"=>$id])->one();
        if ($category->load(Yii::$app->request->post()))
        {
            if(Category::containsCategory($id, $category->name))
            {
                $photo = UploadedFile::getInstance($category, 'image');

                if ( $photo != null )
                {
                    $model_shop = Shop::getShopFromThisUser(Yii::$app->user->identity->user_id);
                    $rand = rand(0, 9999);
                    $newName = $rand . date("Y_m_d") . '.' . $photo->name;
                    Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web';
                    $path = 'users/'.$model_shop->shop_id.'/categorys/' . $newName;
                    $photo->saveAs($path);
                    $photo_name = $category->photo_name;
                    $category->photo_name = $newName;

                    if($category->validate())
                    {
                        $category->save();
                        if (file_exists(Yii::$app->basePath.'/web/users/'.$model_shop->shop_id.'/categorys/'.$photo_name))
                        {
                        unlink(Yii::$app->basePath.'/web/users/'.$model_shop->shop_id.'/categorys/'.$photo_name);
                        }
                        $shop_id = $this->getShopFromThisUser()->shop_id;

                        Yii::$app->session->setFlash('success', 'Категорія успішно редагована!');
                        return $this->redirect('categorys');
                    }
                }
                else{
                    $category->save();
                    Yii::$app->session->setFlash('success', 'Категорія успішно редагована!');
                    return $this->redirect('categorys');
                }
            }
            else{
                Yii::$app->session->setFlash('error', 'Продукт з такою назвою вже існує!');
                return $this->redirect('categorys');
            }
        }
        Yii::$app->session->setFlash('error', 'Щось пішло не так!');
        return $this->redirect('categorys');
    }

    public function actionDeleteCategory($id)
    {
        $model_shop = Shop::getShopFromThisUser(Yii::$app->user->identity->user_id);
        $shop_name = $model_shop->name;

        $products = Product::find()->where(['active'=>true, 'category_id'=>$id])->all();
        foreach($products as $product)
        {
            if (file_exists(Yii::$app->basePath.'/web/users/'.$shop_name.'/products/'.$product->photo_name))
            {
                unlink(Yii::$app->basePath.'/web/users/'.$shop_name.'/products/'.$product->photo_name);
            }
        }

        Yii::$app->db->createCommand()->update('product', ['category_id' => 666, 'active'=>false], ['category_id'=>$id])->execute();

        $category_shop = CategoryShop::find()->where(['category_id' => $id])->one();
        $category_shop->delete();

        $category = Category::find()->where(['category_id'=>$id])->one();
        if (file_exists(Yii::$app->basePath.'/web/users/'.$shop_name.'/categorys/'.$category->photo_name))
        {
            unlink(Yii::$app->basePath.'/web/users/'.$shop_name.'/categorys/'.$category->photo_name);
        }
        $category->delete();

        Yii::$app->session->setFlash('success', 'Категорію успішно видалено!');
        return $this->redirect('categorys');
    }

    //------------------------------------------ Coming -----------------------------------------

    public function actionChangeQuantity()
    {
        $searchModelProduct = new ProductSearch();
        $dataProviderProduct = ($searchModelProduct->allSearch(Yii::$app->request->queryParams));
        $model_shop = Shop::getShopFromThisUser(Yii::$app->user->identity->user_id);

        // Якщо є PJAX ця штука обов'язкова!!!!!
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('changeQuantity', [
                // 'searchModel' => $searchModel,
                // 'dataProvider' => $dataProvider,
                'searchModelProduct' => $searchModelProduct,
                'dataProviderProduct' => $dataProviderProduct,
            ]);
        }
        else{
            return $this->render('changeQuantity', [
                // 'searchModel' => $searchModel,
                // 'dataProvider' => $dataProvider,
                'searchModelProduct' => $searchModelProduct,
                'dataProviderProduct' => $dataProviderProduct,
            ]);
        }
    }

    public function actionMinus()
    {
        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();

        $stock = Stock::find()->where(['product_id'=>$_POST['product_id']])->one();


        if($model_user->shop->getProducts()->where(['product_id'=>$_POST['product_id']])->exists())
        {
            if(is_numeric($_POST['count']) && $_POST['date'])
            {
                if($stock->quantity >= $_POST['count'])
                {
                    if($_POST['count'] > 0){
                        $withdraw = new Withdraw();
                        $withdraw->product_id = $_POST['product_id'];
                        $withdraw->user_id = $user_id;
                        $withdraw->date_now = getdate()[0];
                        $withdraw->select_date = strtotime($_POST['date']);
                        $withdraw->quantity = $_POST['count'];
                        $withdraw->description = $_POST['comment'];
                        $withdraw->save();

                        $stock->quantity -= $withdraw->quantity;
                        $stock->save();

                        $name = Product::find()->where(['product_id'=>$_POST['product_id']])->one();
                        return $this->asJson(['name'=>$name->name]);
                    }
                    else return "Введіть більшу кількість продукту";
                }
                else return "На складі немає такої кількості продукту! (".$stock->quantity."шт)";
            }
            else return "Щось пішло не так";
        }
        else return "В вас немає такого продукту!";
    }

    public function actionPlus()
    {
        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();
        if($model_user->shop->getProducts()->where(['product_id'=>$_POST['product_id']])->exists())
        {
            if(is_numeric($_POST['count']) && $_POST['date'])
            {
                if($_POST['count'] > 0){
                    $coming = new Coming();
                    $coming->product_id = $_POST['product_id'];
                    $coming->user_id = $user_id;
                    $coming->date_now = getdate()[0];
                    $coming->select_date = strtotime($_POST['date']);
                    $coming->quantity = $_POST['count'];
                    $coming->description = $_POST['comment'];
                    $coming->save();

                    $stock = Stock::find()->where(['product_id'=>$_POST['product_id']])->one();
                    $stock->quantity += $coming->quantity;
                    $stock->save();

                    $name = Product::find()->where(['product_id'=>$_POST['product_id']])->one();
                    return $this->asJson(['name'=>$name->name]);
                }
                else return "Введіть більшу кількість продукту";
            }
            else return "Щось пішло не так";
        }
        else return "В вас немає такого продукту!";
    }



}
