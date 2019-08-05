<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;
use \yii\web\IdentityInterface;

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
class User extends ActiveRecord implements IdentityInterface {

    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['first_name', 'surname', 'email', 'phone_number'], 'required'],
            [['user_id', 'date'], 'number'],
            [['authKey', 'accessToken'], 'default', 'value' => ''],
            [['password', 'authKey', 'accessToken'], 'string'],
            [['username'], 'string', 'max' => 200],
            [['first_name', 'surname', 'email'], 'string', 'max' => 100],
            [['phone_number'], 'string', 'max' => 19],
            [['password','username'], 'string', 'min' => 8],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id'       => 'ID',
            'username'      => 'Логін (Не менше 8 символів!)',
            'password'      => 'Пароль (Не менше 8 символів!)',
            'authKey'       => 'Auth Key',
            'accessToken'   => 'Access Token',
            'first_name'    => 'Ім\'я',
            'surname'       => 'Прізвище',
            'email'         => 'Електронна пошта',
            'phone_number'  => 'Номер телефону',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShop()
    {
        return $this->hasOne(Shop::class, ['shop_id' => 'shop_id'])->via('personnel');
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getPersonnel()
    {
        return $this->hasOne(Personnel::class, ['user_id' => 'id']);
    }


    //  /**
    // * @return \yii\db\ActiveQuery
    // */
    // public function getUser()
    // {
    //     return $this->hasMany(User::class, ['user_id' => 'user_id'])->via('authAssignment');
    // }


    // /**
    // * @return \yii\db\ActiveQuery
    // */
    // public function getRoles()
    // {
    //     return $this->hasMany(AuthRule::class, ['user_id'=>'user_id'])->via('authAssignment');
    // }


    /**
    * @return \yii\db\ActiveQuery
    */
    public function getAuthAssignment()
    {
        return $this->hasOne(AuthAssignment::class, ['user_id'=>'user_id']);
    }

   

    public static function containsUser($id, $email)
    {
        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();
        $user = User::find()->where(['user_id'=>$id, 'email'=>$email])->one();
        if(User::find()->where(['email'=>$email])->exists()){
            if($model_user->shop == $user->shop){
                return true;
            }
        }
        else{
            return true;
        }
        return false;
    }

}
