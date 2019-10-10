<?php


namespace console\controllers;


use Yii;
use yii\console\Controller;

class AdminController extends Controller
{
    public function actionAppoint($username, $password, $role)
    {
        $identity = new \api\modules\v1\models\User();
        $identity->login = $username;
        $identity->password = Yii::$app->security->generatePasswordHash($password);
        $identity->auth_key = Yii::$app->security->generateRandomString();
        $identity->access_token = Yii::$app->security->generateRandomString();
        $identity->email = '';
        $identity->save();
        $userRole = Yii::$app->authManager->getRole($role);
        Yii::$app->authManager->assign($userRole, $identity->id);
    }

    public function actionIndex()
    {
        echo "Hello World\n";
    }

}