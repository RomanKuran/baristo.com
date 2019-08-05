<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shop".
 *
 * @property int $shop_id
 * @property string $name
 *
 * @property Basket[] $baskets
 * @property Personnel[] $personnels
 * @property Sold[] $solds
 * @property Stock[] $stocks
 */
class Shop extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 200],
            [['user_id', 'payment_id'], 'number'],
            [['date'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'shop_id' => 'Shop ID',
            'name' => 'Назва вашого підприємства',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBaskets()
    {
        return $this->hasMany(Basket::className(), ['shop_id' => 'shop_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonnels()
    {
        return $this->hasMany(Personnel::className(), ['shop_id' => 'shop_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSolds()
    {
        return $this->hasMany(Sold::className(), ['shop_id' => 'shop_id']);
    }

    public static function getShopFromThisUser($user_id)
    {
        $model_user = User::find()->where(['user_id'=>$user_id])->one();
        return $model_user->shop;
    }


    
    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getSoldsTwo()
    // {
    //     return $this->hasMany(Sold::className(), ['product_id' => 'product_id'])->via('productsTwo');
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getProductsTwo()
    // {
    //     return $this->hasMany(Product::className(), ['product_id' => 'product_id'])->via('stockTwo');
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getStockTwo()
    // {
    //     return $this->hasMany(Stock::className(), ['shop_id' => 'shop_id']);
    // }

    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['product_id' => 'product_id'])->via('stockProducts');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockProducts()
    {
        return $this->hasMany(Stock::className(), ['shop_id' => 'shop_id']);
    }







    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategorys()
    {
        return $this->hasMany(Category::className(), ['category_id' => 'category_id'])->via('categorysShop');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategorysShop()
    {
        return $this->hasMany(CategoryShop::className(), ['shop_id' => 'shop_id']);
    }



    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getCategorys()
    // {
    //     return $this->hasMany(Category::className(), ['category_id' => 'category_id'])->via('product');
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getProduct()
    // {
    //     return $this->hasMany(Product::className(), ['product_id' => 'product_id'])->via('stock');
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getProductWith()
    // {
    //     return $this->hasMany(Product::className(), ['product_id' => 'product_id'])->via('stock')->joinWith('category');
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStock()
    {
        return $this->hasMany(Stock::className(), ['shop_id' => 'shop_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStocks()
    {
        return $this->hasMany(Stock::className(), ['shop_id' => 'shop_id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getUser()
    {
        return $this->hasMany(User::class, ['user_id' => 'user_id'])->via('personnel');
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getPersonnel()
    {
        return $this->hasMany(Personnel::class, ['shop_id' => 'shop_id']);
    }



    /**
    * @return \yii\db\ActiveQuery
    */
    public function getCategory()
    {
        return $this->hasMany(Category::class, ['user_id' => 'user_id'])->via('personnel');
    }


    // /**
    // * @return \yii\db\ActiveQuery
    // */
    // public function getAuthAssignment()
    // {
    //     return $this->hasMany(AuthAssignment::class, ['user_id'=>'user_id']);
    // }



}
