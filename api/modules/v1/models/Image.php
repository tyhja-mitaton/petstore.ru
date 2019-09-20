<?php


namespace api\modules\v1\models;


use yii\db\ActiveRecord;

class Image extends ActiveRecord
{
    public function rules()
    {
        return [
           ['pet_id', 'integer'],
            ['path', 'string', 'max' => 200]
        ];
    }

    public static function tableName()
    {
        return '{{%image}}';
    }

}