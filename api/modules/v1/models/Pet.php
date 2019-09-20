<?php


namespace api\modules\v1\models;


use yii\db\ActiveRecord;

class Pet extends ActiveRecord
{
    public function rules()
    {
        return [
            [['nickname', 'breed', 'commentary'], 'string', 'max' => 200],
            [['category_id', 'user_id', 'price'], 'integer'],
            ['status', 'safe']
        ];
    }

    public static function tableName()
    {
        return '{{%pet}}';
    }

    private function getPhone()
    {
        $identity = \api\modules\v1\models\User::findOne(['id' => $this->user_id]);
        return $identity->phone;
    }

    private function getCategory()
    {
        $identity = \api\modules\v1\models\Category::findOne(['id' => $this->category_id]);
        return $identity->title;
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['category_id'], $fields['user_id']);
        $fields['phone'] = function () {
            return $this->getPhone();
        };
        $fields['category'] = function () {
            return $this->getCategory();
        };
        return $fields;
    }

}