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
use app\models\Personnel;
use app\models\AuthAssignment;
use app\models\ReserveShop;
// use app\models\UserAdmin;
// use app\models\AuthItemChild;
use \yii\db\Query;

// use app\models\Category;
// use app\models\Stock;
// use yii\web\UploadedFile;


class BaristoController extends Controller
{

    // public $layout = "admin_layout";

    // public function behaviors()
    // {
    //     return [
    //         'access' => [
    //             'class' => \yii\filters\AccessControl::className(),
    //             'rules' => [
    //                 [
    //                     'actions' => ['personnel', 'create-user', 'add-user', 'edit-user', 'update-user', 'delete-user'],
    //                     'allow' => true,
    //                     'roles' => ['superAdmin'],
    //                 ],
    //                 // [
    //                 //     'allow' => true,
    //                 //     'roles' => ['@'],
    //                 // ],
                   
    //             ]
    //         ]
    //     ];
    // }


    public function actionIndex()
    {
        $model_reserve_shop = new ReserveShop();
        return $this->render('index', compact('model_reserve_shop'));
    }

    public function actionSentMessage()
    {
        $model_reserve_shop = new ReserveShop();
        if ($model_reserve_shop->load(Yii::$app->request->post())) 
        {
            if($model_reserve_shop->validate())
            {
                for($i = 0; $i < 25; $i++){
                    $token .= chr(rand(0, 9)+rand(65, 81));
                    $token .= chr(rand(0, 9)+rand(97, 113));
                    $token .= rand(0, 9);
                }
                $token.=getdate()[0];
                
                $contains_login = ReserveShop::find()
                ->where( [ 'login' => $model_reserve_shop->login ] )
                ->exists(); 

                $contains_email = ReserveShop::find()
                ->where( [ 'email' => $model_reserve_shop->email ] )
                ->exists(); 

                $contains_login_user = User::find()
                ->where( [ 'username' => $model_reserve_shop->login ] )
                ->exists(); 

                $contains_email_user = User::find()
                ->where( [ 'email' => $model_reserve_shop->email ] )
                ->exists(); 


                if($contains_login || $contains_login_user)
                {
                    Yii::$app->session->setFlash('error', 'Користувач з таким логіном вже існує!'); 
                    return $this->render('index', compact('model_reserve_shop'));
                }

                if($contains_email || $contains_email_user)
                {
                    Yii::$app->session->setFlash('error', 'Користувач з такою поштою вже залогінений!'); 
                    return $this->render('index', compact('model_reserve_shop'));
                }



                $sent = Yii::$app->mailer->compose()
                ->setFrom('romanxxx2811@gmail.com')
                ->setTo($model_reserve_shop->email)
                ->setSubject('Перейдіть для підтвердження вашого профілю:')
                ->setHtmlBody('<a href="http://baristo/baristo/reserve-shop?token='.$token.'">Натисніть для підтвердження</a>')
                ->send();

                if($sent)
                {
                    $model_reserve_shop->password = md5($model_reserve_shop->password);
                    $model_reserve_shop->token = $token;
                    $model_reserve_shop->date = getdate()[0];
                    $model_reserve_shop->save();
                    Yii::$app->session->setFlash('success', 'На вашу пошту відправлений лист для підтвердження профілю'); 
                }
            }
        }

        return $this->redirect('index');
    }

    public function actionReserveShop($token)
    {
        $model_reserve_shop = ReserveShop::find()->where( [ 'token' => $token ] )->one(); 
        if($model_reserve_shop)
        {
            $model_user = new User();
            $model_shop = new Shop();
            $model_role = new AuthAssignment();
            $model_personnel = new Personnel();

            $model_user->username = $model_reserve_shop->login;
            $model_user->password = $model_reserve_shop->password;
            $model_user->first_name = $model_reserve_shop->first_name;
            $model_user->surname = $model_reserve_shop->surname;
            $model_user->email = $model_reserve_shop->email;
            $model_user->phone_number = $model_reserve_shop->phone_number;
            $model_user->date = $model_reserve_shop->date;
            $model_user->save();

            $model_role->item_name = "superAdmin";
            $model_role->user_id = $model_user->user_id;
            $model_role->active = true;
            $model_role->save();

            $model_shop->user_id = $model_user->user_id;

            // Доробити при "оформленні фінансових транзакцій"!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $model_shop->payment_id = 1;

            $model_shop->name = $model_reserve_shop->shop_name;
            $model_shop->date = getdate()[0];
            $model_shop->save();

            $model_personnel->shop_id = $model_shop->shop_id;
            $model_personnel->user_id = $model_user->user_id;
            $model_personnel->save();

            $model_reserve_shop->delete();

            $structure_products = './users/'. $model_shop->shop_id . '/products/';
            $structure_category = './users/'. $model_shop->shop_id . '/categorys/';

            if (!mkdir($structure_products, 0777, true)) {
                die('Не удалось создать директории...');
            }
            if (!mkdir($structure_category, 0777, true)) {
                die('Не удалось создать директории...');
            }

            Yii::$app->session->setFlash('success', 'Введіть будь ласка свій логін та пароль'); 
            return $this->redirect('/site/login');
        }
        else{
            Yii::$app->session->setFlash('error', 'Цей токен не є дійсний! Зареєструйтесь будь ласка повторно!'); 
        }
        Yii::$app->session->setFlash('error', 'Щось пішло не так, зареєструйтесь будь ласка повторно!'); 
        return $this->redirect('index');
    }



}