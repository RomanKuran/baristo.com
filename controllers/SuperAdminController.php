<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\Response;
// use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;
use app\models\Shop;
use app\models\AuthAssignment;
use app\models\UserAdmin;
use app\models\AuthItemChild;
use app\models\Personnel;
use app\models\ReportOne;
use \yii\db\Query;
use yii\db\Expression;

// use app\models\Category;
// use app\models\Stock;
// use yii\web\UploadedFile;


class SuperAdminController extends Controller
{
    public $layout = "super_admin_layout";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['personnel', 'create-user', 'add-user', 'edit-user', 
                        'update-user', 'delete-user', 'reports', 'download-reports', 'index'],
                        'allow' => true,
                        'roles' => ['superAdmin'],
                    ],
                    // [
                    //     'allow' => true,
                    //     'roles' => ['@'],
                    // ],
                   
                ]
            ]
        ];
    }

    // public function actionIndex(){
    //     return 1;
    // }

    public function actionPersonnel()
    {

        $user_id = Yii::$app->user->identity->user_id;
        $model_shop = Shop::find()->where(["user_id"=>$user_id])->one();
        $shop = Shop::find()->where( [ 'shop_id' => $model_shop->shop_id ] )->one();

        $users = $shop->getUser()->joinWith('authAssignment')
        ->where(['<>', 'item_name', 'superAdmin'])
        ->andWhere(['<>', 'item_name', 'noneUser'])
        ->andWhere(['<>', 'item_name', 'noneAdmin'])
        ->andWhere(['<>', 'item_name', 'noneSuperAdmin']);

        $dataProvider = new ActiveDataProvider([
            'query' => $users,
            'pagination' => ['pageSize' => 10]
        ]);

        // $this->view->title = 'Блог';

        return $this->render('personnel', compact('dataProvider'));
    }

    public function getRoles(){
        $users = AuthItemChild::find()->where(['<>', 'parent', 'superAdmin'])
        ->andWhere(['<>', 'parent', 'NoneAdmin'])
        ->andWhere(['<>', 'parent', 'NoneUser'])->all();
        $roles = [];
        foreach($users as $c) 
            $roles[$c->child] =  $c->parent;
        return $roles;
    }

    public function actionCreateUser() 
    {
        $model_user = new User();
        $role_user = new AuthAssignment();
        $roles = $this->getRoles();
        
        return $this->render('createUser', compact('model_user', 'role_user', 'roles'));
    }

    public function actionAddUser() 
    {
        $model_user = new User();
        $role_user = new AuthAssignment();
        $model_personnel = new Personnel();


        if ($model_user->load(Yii::$app->request->post()) && $role_user->load(Yii::$app->request->post())) 
        {
            
            if($model_user->validate())
            {

                // $contains = User::find()
                //     ->where( [ 'email' =>  $model_user->email ] )
                //     ->exists(); 

                $contains_email = User::find()->where( [ 'email' => $model_user->email ] )->one();
                
                // $contains_email->shop;
                // var_dump($contains_email->shop);
                // exit;

                

                if(!$contains_email->shop)
                {
                    $login = rand(0, 9);
                    $contains = true;
                    while($contains)
                    {
                        for($i = 0; $i < 4; $i++){
                            $login .= chr(rand(0, 9)+rand(65, 81));
                            $login .= chr(rand(0, 9)+rand(97, 113));
                        }
                        $contains = User::find()
                        ->where( [ 'username' => $login ] )
                        ->exists(); 
                    }
                    $pass = rand(0, 9);
                    for($i = 0; $i < 4; $i++){
                        $pass .= chr(rand(0, 9)+rand(65, 81));
                        $pass .= chr(rand(0, 9)+rand(97, 113));
                    }

                    $model_user->username = $login;
                    $model_user->password = md5($pass);
                    $model_user->date = getdate()[0];
                    $model_user->save();
                    
                    
                    $roles = AuthItemChild::find()->where(["child"=>$role_user->item_name])->one();
                    $role_user->item_name = $roles->parent;
                    $role_user->user_id = $model_user->user_id;
                    $role_user->active = true;
                    $role_user->save();

                    

                    $user_id = Yii::$app->user->identity->user_id;
                    $model_shop = Shop::find()->where(["user_id"=>$user_id])->one();
                    // var_dump($model_user->user_id, $model_shop->shop_id);
                    // exit;
                    $model_personnel->shop_id = $model_shop->shop_id;
                    $model_personnel->user_id = $model_user->user_id;
                    $model_personnel->save();

                    Yii::$app->mailer->compose()
                    ->setFrom('romanxxx2811@gmail.com')
                    ->setTo($model_user->email)
                    ->setSubject('Ваші данні для входу в наш сервіс:')
                    ->setHtmlBody('Логін: '.$login.'<br>Пароль: '.$pass)
                    ->send();

                    Yii::$app->session->setFlash('success', 'Користувач успішно добавлений!!'); 
                    return $this->redirect('personnel');
                }
                else{
                    Yii::$app->session->setFlash('error', 'Користувач з такою поштою вже існує!!'); 
                    $roles = $this->getRoles();
                    return $this->render('createUser', compact('model_user', 'role_user', 'roles'));
                }
            }          
        }
    }

    public function actionEditUser($id, $rule) 
    {
        $model = User::findOne($id);
        $editModel = new UserAdmin();

        $editModel->first_name = $model->first_name;
        $editModel->surname = $model->surname;
        $editModel->email = $model->email;
        $editModel->phone_number = $model->phone_number;
        $roles = $this->getRoles();
        $editModel->item_name = $rule;

        $email = $editModel->email;

        // Запитати в Ярослава "Що це?"!!!!

        // $_SESSION['BLOG_MODE'] = 'EDIT';
        // $_SESSION['BLOG_ID'] = $model->id;

        return $this->render('editUser', compact('editModel', 'roles', 'id', 'email'));
    }
    
    public function actionUpdateUser($id) 
    {
        $model = new UserAdmin();
        if ($model->load(Yii::$app->request->post())) {
            $model_user = User::find()->where( [ 'email' => $model->email ] )->one();
            $query=new Query();
            $query->addSelect(['p.*'])
                    ->from ([User::tableName().' u'])
                    ->leftJoin(Personnel::tableName().' p','u.user_id = p.user_id')
                    ->leftJoin(Shop::tableName().' s','s.shop_id = p.shop_id')
                    ->leftJoin(AuthAssignment::tableName().' a','u.user_id = a.user_id')
                    ->where(['u.email' => $model->email]); 

            if(User::containsUser($id, $model->email))
            {
                $model_user = User::findOne($id);
                $model_rule = AuthAssignment::find()->where(['user_id' => $id])->one();
                
                if ($model->validate() && $model_user && $model_rule) 
                {
                    $model_user->first_name = $model->first_name;
                    $model_user->surname = $model->surname;
                    $model_user->email = $model->email;
                    $model_user->phone_number = $model->phone_number;
                    $rule = AuthItemChild::find()->where(['child' => $model->item_name])->one();
                    $model_rule->item_name = $rule->parent;
                    $model_user->save();
                    $model_rule->save();
                    Yii::$app->session->setFlash('success', 'Користувач \'' . $model->first_name . '\' успішно редагований!');
                    return $this->redirect('personnel');
                }
            }
            else
            {
                $roles = $this->getRoles();
                $editModel = $model;

                Yii::$app->session->setFlash('error', 'Користувач з такою електронною поштою вже існує!');
                return $this->render('editUser', compact('editModel', 'roles', 'id'));
            }
        }
        else{                
            Yii::$app->session->setFlash('error', 'Користувач \'' . $model->first_name . '\' щось пішло не так!');
        }
    }

    public function actionDeleteUser($id) 
    {
        $model = AuthAssignment::find()->where(["user_id"=>$id])->one();
        if($model->active){
            if($model->item_name === "user"){
                $model->item_name = "NoneUser";
            }
            elseif($model->item_name === "admin"){
                $model->item_name = "NoneAdmin";
            }
             
            $model->active = 0;
            $model->save();
            Yii::$app->session->setFlash('success', 'Користувач успішно ДЕАКТИВОВАНИЙ!');            
        }
        else{
            Yii::$app->session->setFlash('error', 'Щось пішло не так!');            
        }
        
        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    //------------------------------------------ Report -----------------------------------------

    public function query_products($start_mon, $end_date){
        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();

        $products = $model_user->shop->getProducts()->where([">=","product.date", $start_mon])->andWhere(["<=", "product.date", $end_date])
        ->select(["product.*",
        "(SELECT SUM(sold.quantity) from sold where `sold`.`product_id`=`product`.`product_id` and (`sold`.`date` >= $start_mon) and (`sold`.`date` <=$end_date)) AS `total`,
        (SELECT SUM(coming.quantity) from coming where `coming`.`product_id`=`product`.`product_id` and (`coming`.`select_date` >= $start_mon) and (`coming`.`select_date` <= $end_date))AS `sum_coming`,
        (SELECT SUM(withdraw.quantity) from withdraw where `withdraw`.`product_id`=`product`.`product_id` and (`withdraw`.`select_date` >= $start_mon) and (`withdraw`.`select_date` <= $end_date))AS `sum_withdraw`"])
        ->groupBy(['product.product_id'
        ]);
        return $products;
    }

    public function actionReports()
    {
        $start_date = $_GET['start_date'];
        $end_date = $_GET['end_date'];
        if(!$start_date || !$end_date)
        {
            $this_date = date('Y-m-d');
            $start_this_date = getdate()['year'].'-'.getdate()['mon'].'-01';
            $start_date = getdate()['year'].'/'.getdate()['mon'].'/1';
            $start_mon = strtotime(getdate()['year']."-".getdate()['mon']."-1");
        }
        else
        {
            $this_date = $end_date;
            $start_this_date = $start_date;
            $start_mon = strtotime($start_date);
            $end_date = strtotime($end_date);
        }
        
        if(is_numeric($start_mon) && is_numeric($end_date))
        {
            $products = $this->query_products($start_mon, $end_date);
            $dataProvider = new ActiveDataProvider([
                'query' => $products,
                'pagination' => ['pageSize' => 10]
            ]);
            return $this->render('reports', compact('dataProvider', 'this_date', 'start_this_date'));
        }
        else{
            $this_date = date('Y-m-d');
            $start_this_date = getdate()['year'].'-'.getdate()['mon'].'-01';
            $start_date = getdate()['year'].'/'.getdate()['mon'].'/1';
            $start_mon = strtotime(getdate()['year']."-".getdate()['mon']."-1");
            $end_date = strtotime($this_date);

            $products = $this->query_products($start_mon, $end_date);

            $dataProvider = new ActiveDataProvider([
                'query' => $products,
                'pagination' => ['pageSize' => 10]
            ]);

            return $this->render('reports', compact('dataProvider', 'this_date', 'start_this_date'));
        }
    }

    public function actionDownloadReports()
    {      
        $start_date = $_GET['start_date'];
        $end_date = $_GET['end_date'];
        if(!$start_date || !$end_date)
        {
            $this_date = date('Y-m-d');
            $start_this_date = getdate()['year'].'-'.getdate()['mon'].'-01';
            $start_date = getdate()['year'].'/'.getdate()['mon'].'/1';
            $start_mon = strtotime(getdate()['year']."-".getdate()['mon']."-1");
        }
        else
        {
            $this_date = $end_date;
            $start_this_date = $start_date;
            $start_mon = strtotime($start_date);
            $end_date = strtotime($end_date);
        }
        
        if(is_numeric($start_mon) && is_numeric($end_date)/* || !$start_mon || !$end_date*/)
        {
            $products = $this->query_products($start_mon, $end_date);

            $dataProvider = new ActiveDataProvider([
                'query' => $products,
                'pagination' => ['pageSize' => 10]
            ]);
            // return $this->render('reports', compact('dataProvider', 'this_date', 'start_this_date'));
        }
        else{
            $this_date = date('Y-m-d');
            $start_this_date = getdate()['year'].'-'.getdate()['mon'].'-01';
            $start_date = getdate()['year'].'/'.getdate()['mon'].'/1';
            $start_mon = strtotime(getdate()['year']."-".getdate()['mon']."-1");
            $end_date = strtotime($this_date);

            $products = $this->query_products($start_mon, $end_date);

            $dataProvider = new ActiveDataProvider([
                'query' => $products,
                'pagination' => ['pageSize' => 10]
            ]);

            // return $this->render('reports', compact('dataProvider', 'this_date', 'start_this_date'));
        }

        

        $products = $products->all();
        $count = count($products)+3;
        $all_sum = 0;
        $info = "";
        foreach($products as $product) {
            $info .= $product->name . "\t";
            $info .= $product->price . "\t";
            if($product->active) $info .= "+" . "\t";
            else $info .= "-" . "\t";
            $info .= $product->stocks[0]->quantity . "\t";
            if($product->sum_coming) $info .= $product->sum_coming . "\t";
            else $info .= "0" . "\t";
            if($product->sum_withdraw) $info .= $product->sum_withdraw . "\t";
            else $info .= "0" . "\t";
            if($product->total) $info .= $product->total . "\t";
            else $info .= "0" . "\t";
            $info .= $product->total * $product->price . "\t";
            $info .= "\n";
            $all_sum += $product->total * $product->price;
        }

        ob_start();

        ob_get_clean();

        header( "Content-Type: application/vnd.ms-excel" );
        header( "Content-disposition: attachment; filename=".date("Y-m-d H:i:s").".xls" );
        echo 'Назва' . "\t" . 'Ціна' . "\t" . 'Активний' . "\t" . 'Залишок' . "\t" . 'Прихід' . "\t" . 'Списано' . "\t" . 'Продано' . "\t" . 'Сума' . "\t\t" . "Загальна сума:" . "\t" . $all_sum ."Грн" ."\n";
        echo $info;
        die();

        return $this->render('reports', compact('dataProvider', 'this_date', 'start_this_date'));
    }

}