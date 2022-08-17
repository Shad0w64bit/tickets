<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use app\models\User;

class ChangePasswordForm extends Model
{
    public $uid;
    public $newPassword;
    public $reNewPassword;
    
    public function rules()
    {
        return [
            [['uid', 'newPassword', 'reNewPassword'], 'required'],
            [['uid'], 'integer'],
            [['newPassword', 'reNewPassword'], 'string', 'min' => 8],            
            ['reNewPassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Пароли не совпадают'],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'id']],
        ];
    }
    
    public function attributeLabels() {
        return [
            'newPassword' => 'Новый пароль',
            'reNewPassword' => 'Новый пароль, ещё раз',
        ];
    }
    
}