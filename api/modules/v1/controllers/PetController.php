<?php


namespace api\modules\v1\controllers;


use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\UploadedFile;
use api\modules\v1\models\UploadImage;
use api\modules\v1\models\Image;
use yii\data\ActiveDataProvider;

class PetController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Pet';
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
                'actions' => ['delete'],
                'roles' => ['admin'],
            ],
            [
                'allow' => true,
                'actions' => ['update', 'index', 'view', 'upload', 'create', 'search'],
                'roles' => ['admin', 'user']
            ]
        ],
    ];

        return$behaviors;
    }

    public function actionUpload($id)
    {
        $imageLoader = new UploadImage();
        $image = new Image();
        $imageLoader->image = UploadedFile::getInstanceByName('image');
        if ($imageLoader->image != null && $imageLoader->validate())
        {
            $imageLoader->image->saveAs("uploads/{$imageLoader->image->baseName}.{$imageLoader->image->extension}");
            $image->pet_id = $id;
            $image->path = "{$imageLoader->image->baseName}.{$imageLoader->image->extension}";
            $image->save();
            return "{$imageLoader->image->baseName}.{$imageLoader->image->extension}";
        }
        return 'upload an image!';

    }

    public function actionSearch()
    {
        $requestParams = \Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = \Yii::$app->getRequest()->getQueryParams();
        }
        /* @var $modelClass \yii\db\BaseActiveRecord */
        $modelClass = $this->modelClass;
        $query = $modelClass::find();

        if ($requestParams['breed']) {
            $query->andWhere(['breed' => $requestParams['breed']]);
        }
        if ($requestParams['category']) {
            $query->andWhere(['category' => $requestParams['category']]);
        }
        if ($requestParams['price']) {
            $query->andWhere(['<=', 'price', $requestParams['price']]);
        }
        if ($requestParams['status']) {
            $query->andWhere(['status' => $requestParams['status']]);
        }

        return \Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
                'params' => $requestParams,
            ],
            'sort' => [
                'params' => $requestParams,
            ],
        ]);
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