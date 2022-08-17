<?php

namespace app\modules\client_admin\models;

use Yii;
use yii\base\Model;
use app\models\User;

class ChangeEmailForm extends Model
{
    public $id;
    public $email;
    
    public function rules()
    {
        return [
            [['id','email'], 'required'],
            ['id', 'number'],
            ['email', 'email'],
        ];
    }
    
    public function attributeLabels() {
        return [     
            'email' => 'Новый Email',
        ];
    }
}