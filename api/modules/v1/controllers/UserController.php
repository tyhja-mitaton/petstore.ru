<?php


namespace api\modules\v1\controllers;

use api\modules\v1\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\rest\ActiveController;
use yii\filters\Cors;
use yii\filters\ContentNegotiator;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class UserController
 */
class UserController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\User';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];


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
                'actions' => ['delete', 'ban'],
                'roles' => ['admin'],
            ],
            [
                'allow' => true,
                'actions' => ['login', 'create'],
                'roles' => ['?'],
            ],
            [
                'allow' => true,
                'actions' => ['logout', 'index', 'view', 'update'],
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
        $identity = \api\modules\v1\models\User::findOne(['login' => $username]);
        if ($identity && Yii::$app->security->validatePassword($password, $identity->password))
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

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        $actions['index']['prepareDataProvider'] = function () {
            return new ActiveDataProvider([
                'query' => $this->modelClass::find(),
                'pagination' => ['pageSize' => 20,]
            ]);

        };
        return $actions;
    }

    public function actionCreate()
    {
        $model = new User();

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->access_token = Yii::$app->security->generateRandomString();
        $model->auth_key = Yii::$app->security->generateRandomString();
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;

    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if ($action === 'update')
        {
            if ($model->id !== \Yii::$app->user->id && !\Yii::$app->user->can('admin'))
            {
                throw new \yii\web\ForbiddenHttpException(sprintf('You can only %s your own profile', $action));
            }
        }
    }

}