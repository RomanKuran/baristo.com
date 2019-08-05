<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sold".
 *
 * @property int $sold_id
 * @property int $products_id
 * @property int $shop_id
 * @property int $quantity
 *
 * @property Shop $shop
 * @property Products $products
 */
class Sold extends \yii\db\ActiveRecord
{
    // function __construct($product_id, $quantity) {
    //     $this->shop_id = 1;
    //     $this->product_id = $product_id;
    //     $this->quantity = $quantity;
    // }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sold';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'shop_id', 'quantity', 'seller_key', 'user_id', 'date'], 'required'],
            [['product_id', 'shop_id', 'quantity', 'user_id', 'date'], 'integer'],
            [['seller_key'], 'string', 'max' => 200],
            [['shop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::className(), 'targetAttribute' => ['shop_id' => 'shop_id']],
            // [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['product_id' => 'product_id']],
            [['products_id', 'shop_id', 'quantity', 'seller_key'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sold_id' => 'Sold ID',
            'products_id' => 'Products ID',
            'shop_id' => 'Shop ID',
            'quantity' => 'Quantity',
            'seller_key' => 'Seller Key',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShop()
    {
        return $this->hasOne(Shop::className(), ['shop_id' => 'shop_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasOne(Products::className(), ['products_id' => 'products_id']);
    }

    private static function getThisShop()
    {
        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();

        return $model_user->shop;
    }

    public static function addProduct($product_id, $count, $key)
    {
        $sold = new Sold();
        $sold->product_id = $product_id;
        $sold->shop_id = self::getThisShop()->shop_id;
        $sold->quantity = $count;
        $sold->seller_key = $key;
        $sold->user_id = Yii::$app->user->identity->user_id;
        $sold->date = getdate()[0];
        $sold->save();
    }
}
