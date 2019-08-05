<?php

namespace app\models;


use \yii\db\ActiveRecord;
use \yii\web\IdentityInterface;
use \yii\base\Model;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $authKey
 * @property string $accessToken
 *
 * @property Blog[] $blogs
 */
class UserAdmin extends Model implements IdentityInterface {

    public $username = "";
    public $first_name = "";
    public $surname = "";
    public $email = "";
    public $phone_number = "";
    public $item_name = "";

    public function rules()
    {
        return [
            // [['username', 'password'], 'required'],
            // [['authKey', 'accessToken'], 'default', 'value' => ''],
            // [['password', 'authKey', 'accessToken'], 'string'],
            [['username'], 'string', 'max' => 200],
            [['first_name', 'surname', 'email'], 'string', 'max' => 100],
            [['phone_number'], 'string', 'max' => 19],
            [['item_name'], 'string', 'max' => 64],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id'     => 'ID',
            'username'      => 'Логін',
            // 'password'      => 'Пароль',
            // 'authKey'       => 'Auth Key',
            'accessToken'   => 'Access Token',
            'first_name'    => 'Ім\'я',
            'surname'       => 'Прізвище',
            'email'         => 'Електронна пошта',
            'phone_number'  => 'Номер телефону',
            'item_name'     => 'Професія'
        ];
    }

    public static function findIdentity($id)
    {
        // var_dump(static::findOne($id));
        // exit;
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->user_id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function findByUsername($username)
    {
        $user = self::find()->where('username = :username', [':username' => $username])->one();
        if (is_object($user) && isset($user->username) && strlen($user->username) > 1) {
            return new static($user);
        }

        return null;
    }

    public function validatePassword($password)
    {
        return $this->cryptPassword($password) === $this->password;
    }

    private function cryptPassword($password)
    {
        return md5($password);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBlogs()
    {
        return $this->hasMany(Blog::class, ['id_user' => 'id']);
    }

}
