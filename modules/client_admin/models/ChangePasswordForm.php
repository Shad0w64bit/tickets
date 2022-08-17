<?php

namespace app\modules\staff\models;

use Yii;
use yii\base\Model;
use app\models\User;

class ChangePasswordForm extends Model
{
    public $oldPassword;
    public $newPassword;
    public $reNewPassword;
    
    public function rules()
    {
        return [
            [['oldPassword', 'newPassword', 'reNewPassword'], 'required'],
            [['oldPassword', 'newPassword', 'reNewPassword'], 'string', 'min' => 8],            
            ['reNewPassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Пароли не совпадают'],
            ['oldPassword', 'validateOldPassword'],
        ];
    }
    
    public function validateOldPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findOne(Yii::$app->user->id);

            if (!$user->validatePassword($this->oldPassword)) {
                $this->addError($attribute, 'Неверный пароль.');
            }
        }
    }    
    
    public function attributeLabels() {
        return [
            'oldPassword' => 'Старый пароль',
            'newPassword' => 'Новый пароль',
            'reNewPassword' => 'Новый пароль, ещё раз',
        ];
    }
}