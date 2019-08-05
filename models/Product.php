<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

class Product extends \yii\db\ActiveRecord
{
    public $image;
    public $total = 0;
    public $sum_coming = 0;
    public $sum_withdraw = 0;

    public function rules()
    {
        return [
            [['product_id'], 'number'],                                 
            [['name'], 'string', 'max' => 200],
            [['price'], 'double'],
            [['photo_name'], 'string', 'max' => 200],
            [['category_id', 'date'], 'number'], 
            [['active'], 'boolean'],
            [['name', 'price', 'category_id'], 'safe'],
            [['name', 'price', 'category_id'], 'required'],
            [['image'], 'safe'],
            [['image'], 'file', /*'skipOnEmpty' => false,*/ 'extensions' => 'png, jpg'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Назва',
            'price' => 'Ціна',
            'image' => 'Зображення',
            'category_id' => 'Категорія',
        ];
    }

    public function getCategorys(){
        return hesMany(Category::class, ['category_id' => 'category_id']);
    }

    public function getCategory(){
        return hesOne(Category::class, ['category_id' => 'category_id']);
    }


    // -------------------------------

    public function getStock(){
        return hesOne(Category::class, ['product_id' => 'product_id']);
    }

    // public function getSolds(){
    //     return hesMany(Sold::class, ['product_id' => 'product_id']);
    // }

    // ---------------------------------

    public function getStocks(){
        return $this->hasMany(Stock::class, ['product_id' => 'product_id']);
    }

    public function getSolds(){
        return $this->hasMany(Sold::class, ['product_id' => 'product_id']);
    }

    public function getComings(){
        return $this->hasMany(Coming::class, ['product_id' => 'product_id']);
    }

    public function getWithdraws(){
        return $this->hasMany(Withdraw::class, ['product_id' => 'product_id']);
    }
    

    public static function containsProduct($id, $category_id, $name)
    {
        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();
        if($model_user->shop->getProducts()->where(['name'=>$name])->andwhere(['category_id'=>$category_id])->exists()){
            if($model_user->shop->getProducts()->where(['product_id'=>$id])->andWhere(['name'=>$name])->exists()){
                return true;
            }
        }   
        else{
            return true;
        }
        return false;
    }
}