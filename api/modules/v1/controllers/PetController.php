<?php


namespace api\modules\v1\controllers;


use yii\base\ErrorException;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\UploadedFile;
use api\modules\v1\models\UploadImage;
use api\modules\v1\models\Image;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use api\rbac\AuthorRule;

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
                    'actions' => ['update', 'index', 'view', 'upload', 'create', 'search', 'delete'],
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
            $timestamp = time();
            $imageLoader->image->saveAs("uploads/{$imageLoader->image->baseName}_{$timestamp}.{$imageLoader->image->extension}");
            $image->pet_id = $id;
            $image->path = "{$imageLoader->image->baseName}_{$timestamp}.{$imageLoader->image->extension}";
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

        if (\Yii::$app->request->post('breed')) {
            $query->andWhere(['breed' => \Yii::$app->request->post('breed')]);
        }
        if (\Yii::$app->request->post('category')) {
            $query->andWhere(['category' => \Yii::$app->request->post('category')]);
        }
        if (\Yii::$app->request->post('price')) {
            $query->andWhere(['<=', 'price', \Yii::$app->request->post('price')]);
        }
        if (\Yii::$app->request->post('status')) {
            $query->andWhere(['status' => \Yii::$app->request->post('status')]);
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

    protected function serializeData($data)
    {
        $data = parent::serializeData($data);
        try {
            $hasEnvelope = $data['items'];
        } catch (ErrorException $e) {
            $hasEnvelope = null;
        }
        try {
            $emptyData = $data[0]['message'];
        } catch (ErrorException $e) {
            $emptyData = null;
        }
        $image = (!$hasEnvelope && !$emptyData) ? Image::findOne(['pet_id' => $data['id']]) : null;
        if ($image) {
            $path = Url::base(true) . "/uploads/{$image->path}";
            return array_merge($data, ['image' => $path]);
        } else {
            return $data;
        }

    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if ($action === 'update' || $action === 'delete')
        {
            if ($model->user_id !== \Yii::$app->user->id && !\Yii::$app->user->can('admin'))
            {
                throw new \yii\web\ForbiddenHttpException(sprintf('You can only %s pets that you\'ve created.', $action));
            }
        }
    }

}