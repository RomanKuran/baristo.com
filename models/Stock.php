<?php

namespace app\models;

use Yii;
use app\models\Basket;

/**
 * This is the model class for table "stock".
 *
 * @property int $stock_id
 * @property int $shop_id
 * @property int $product_id
 * @property int $quantity
 *
 * @property Shop $shop
 * @property Product $product
 */
class Stock extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stock';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shop_id', 'product_id', 'quantity'], 'required'],
            [['shop_id', 'product_id', 'quantity'], 'integer'],
            // [['shop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::className(), 'targetAttribute' => ['shop_id' => 'shop_id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'product_id']],
            [['shop_id', 'product_id', 'quantity'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stock_id' => 'Stock ID',
            'shop_id' => 'Shop ID',
            'product_id' => 'Product ID',
            'quantity' => 'Кількість',
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

    private static function getThisShop()
    {
        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();

        return $model_user->shop;
    }
    public static function reserveProducts($id_product_basket, $count_product_basket, $key){
        $productStock = Stock::find()->where(['product_id' => $id_product_basket])->one();
        if($productStock->quantity - $count_product_basket >= 0)
        {
            $basket = new Basket();

            $basket->shop_id = self::getThisShop()->shop_id;
            $basket->product_id = $id_product_basket;
            $basket->quantity = $count_product_basket;
            $basket->seller_key = $key;
            $basket->user_id =Yii::$app->user->identity->user_id;
            
            if($basket->validate())
            {
                $basket->save();

                $productStock->quantity -= $count_product_basket;
                $productStock->update($attributeNames = null);
                return true;
            }
        }
        else{
            return false;
        }
    }


    public static function addProducts($product_id, $product_count)
    {
        $product_update = Stock::find()->where(['product_id'=>$product_id])->one();
        $product_update->quantity += $product_count;
        $product_update->update($attributeNames = null);
    }


}
