<?php


namespace api\modules\v1\models;


use yii\db\ActiveRecord;

class Category extends ActiveRecord
{
    public function rules()
    {
        return [
            ['title', 'string', 'max' => 200]
        ];
    }

    public static function tableName()
    {
        return '{{%category}}';
    }

}