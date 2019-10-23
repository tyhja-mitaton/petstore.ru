<?php


namespace api\modules\v1\controllers;


use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\ActiveController;

class CategoryController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Category';
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
                    'actions' => ['delete', 'update', 'create'],
                    'roles' => ['admin'],
                ],
                [
                    'allow' => true,
                    'actions' => ['index', 'view'],
                    'roles' => ['admin', 'user']
                ]
            ],
        ];

        return$behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = function () {
            return new ActiveDataProvider([
                'query' => $this->modelClass::find(),
                'pagination' => ['pageSize' => 20,]
            ]);

        };
        return $actions;
    }

}