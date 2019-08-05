<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "basket".
 *
 * @property int $basket_id
 * @property int $products_id
 * @property int $shop_id
 * @property int $quantity
 *
 * @property Shop $shop
 * @property Products $products
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
            [['products_id', 'shop_id', 'quantity', 'seller_key'], 'required'],
            [['products_id', 'shop_id', 'quantity'], 'integer'],
            [['seller_key'], 'string', 'max' => 200]
            [['shop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::className(), 'targetAttribute' => ['shop_id' => 'shop_id']],
            [['products_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['products_id' => 'products_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'basket_id' => 'Basket ID',
            'products_id' => 'Products ID',
            'shop_id' => 'Shop ID',
            'quantity' => 'Quantity',
            'seller_key' => 'seller_key',
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
}
