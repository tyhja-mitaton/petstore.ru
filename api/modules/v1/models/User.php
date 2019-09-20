<?php


namespace api\modules\v1\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


class User extends ActiveRecord implements IdentityInterface
{
    public function rules()
    {
        return [
            [['full_name', 'login', 'email', 'password', 'access_token', 'auth_key', 'phone'], 'string', 'max' => 200],
            ];
    }

    public function fields()
    {
        $fields =  parent::fields(); // TODO: Change the autogenerated stub
        unset($fields['auth_key'], $fields['access_token'], $fields['password']);
        return $fields;
    }

    public static function tableName()
    {
        return '{{%user}}';
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key == $authKey;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['login' => $username]);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generate accessToken string
     * @return string
     * @throws \yii\base\Exception
     */
    public function generateAccessToken()
    {
        $this->access_token = \Yii::$app->security->generateRandomString();
        $this->save();
        return $this->access_token;
    }

}