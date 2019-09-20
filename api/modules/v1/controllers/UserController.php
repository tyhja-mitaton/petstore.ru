<?php


namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\Cors;
use yii\filters\ContentNegotiator;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;

/**
 * Class UserController
 */
class UserController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\User';


    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => \yii\web\Response::FORMAT_JSON,
            ],
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['login', 'create'],
        ];
        $behaviors['access'] = [
        'class' => AccessControl::class,
        'rules' => [
            [
                'allow' => false,
                'actions' => ['create', 'index', 'view', 'update', 'delete'],
                'roles' => ['banned'],
            ],
            [
                'allow' => true,
                'actions' => ['update', 'delete', 'ban'],
                'roles' => ['admin'],
            ],
            [
                'allow' => true,
                'actions' => ['login', 'create'],
                'roles' => ['?'],
            ],
            [
                'allow' => true,
                'actions' => ['logout', 'index', 'view'],
                'roles' => ['admin', 'user']
            ]
        ],
    ];

        return $behaviors;
    }

    public function actionBan($id)
    {
        Yii::$app->authManager->assign(Yii::$app->authManager->getRole('banned'), $id);
        return "user $id was banned";
    }


    public function actionLogin($username, $password)
    {
        $identity = \api\modules\v1\models\User::findOne(['login' => $username, 'password' => $password]);
        if ($identity)
        {
            $identity->generateAccessToken();
            Yii::$app->user->login($identity);
        }
        return Yii::$app->user->identity->access_token;
    }

    public function actionLogout()
    {
        return Yii::$app->user->logout();
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }

}