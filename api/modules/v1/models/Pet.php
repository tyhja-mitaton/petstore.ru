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

}