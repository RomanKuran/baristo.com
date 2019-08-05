<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "basket".
 *
 * @property int $basket_id
 * @property int $product_id
 * @property int $shop_id
 * @property int $quantity
 * @property string $seller_key
 *
 * @property Shop $shop
 * @property Product $product
 */
class Basket extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'basket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'shop_id', 'quantity', 'seller_key', 'user_id'], 'required'],
            [['product_id', 'shop_id', 'quantity', 'user_id'], 'integer'],
            [['seller_key'], 'string', 'max' => 200],
            // [['shop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::className(), 'targetAttribute' => ['shop_id' => 'shop_id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'product_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'basket_id' => 'Basket ID',
            'product_id' => 'Product ID',
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
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }

    public static function deleteProduct($id_product, $count, $key)
    {
        $product = Basket::find()->where(['product_id' => $id_product, 'quantity' => $count, 'seller_key' => $key])->one();
        if($product)
        {
            $product->delete();
            return true;
        }
        else
        {
            return false;
        }
    }
}
