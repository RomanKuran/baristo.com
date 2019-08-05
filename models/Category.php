<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $category_id
 * @property string $name
 * @property string $photo_way
 *
 * @property Product[] $products
 */
class Category extends \yii\db\ActiveRecord
{

    public $image;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'photo_name'], 'required'],
            [['name', 'photo_name'], 'string', 'max' => 250],
            [['name', 'photo_name'], 'safe'],
            [['image'], 'safe'],
            [['image'], 'file', /*'skipOnEmpty' => false,*/ 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'category_id' => 'Ід',
            'name' => 'Назва',
            'image' => 'Зображення',
            'photo_name' => 'НазваФото',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['category_id' => 'category_id']);
    }

    public static function containsCategory($id, $name){
        $model_user = User::find()->where(['user_id'=>Yii::$app->user->identity->user_id])->one();    
        if($model_user->shop->getCategorys()->where(['name'=>$name])->exists()){
            if($model_user->shop->getCategorys()->where(['category_id'=>$id])->andWhere(['name'=>$name])->exists()){
                return true;
            }
        }   
        else{
            return true;
        }
        return false;
    }
}
