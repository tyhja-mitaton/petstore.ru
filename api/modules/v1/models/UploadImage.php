<?php


namespace api\modules\v1\models;


use yii\base\Model;

class UploadImage extends Model
{
    public $image;

    public function rules()
    {
        return [
            [['image'], 'file', 'extensions' => 'png, jpg'],
        ];
    }

}