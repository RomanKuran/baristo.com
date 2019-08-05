<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coming".
 *
 * @property int $coming_id
 * @property int $user_id
 * @property int $product_id
 * @property int $date_now
 * @property int $select_date
 * @property int $quantity
 *
 * @property User $user
 * @property Product $product
 */
class Coming extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'coming';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'product_id', 'date_now', 'select_date', 'quantity'], 'required'],
            [['user_id', 'product_id', 'date_now', 'select_date', 'quantity'], 'integer'],
            [['description'], 'string', 'max' => 250],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'product_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'coming_id' => 'Coming ID',
            'user_id' => 'User ID',
            'product_id' => 'Product ID',
            'date_now' => 'Date Now',
            'select_date' => 'Select Date',
            'quantity' => 'Quantity',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }
}
