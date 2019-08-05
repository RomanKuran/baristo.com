<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reserve_shop".
 *
 * @property int $reserve_shop_id
 * @property string $surname
 * @property string $first_name
 * @property string $email
 * @property string $phone_number
 * @property string $login
 * @property string $password
 * @property string $shop_name
 * @property int $date
 * @property string $token
 */
class ReserveShop extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reserve_shop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surname', 'first_name', 'email', 'phone_number', 'login', 'password', 'shop_name'], 'required'],
            ['email', 'email'],
            [['date'], 'integer'],
            [['surname', 'first_name', 'email', 'password', 'shop_name'], 'string', 'max' => 100],
            [['phone_number'], 'string', 'max' => 19],
            [['login'], 'string', 'max' => 200],
            [['login', 'password'], 'string', 'min' => 8],
            [['token'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reserve_shop_id' => 'Reserve Shop ID',
            'surname' => 'Surname',
            'first_name' => 'First Name',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'login' => 'Login',
            'password' => 'Password',
            'shop_name' => 'Shop Name',
            'date' => 'Date',
            'token' => 'Token',
        ];
    }
}
