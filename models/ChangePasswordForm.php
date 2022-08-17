<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class ChangePasswordForm extends Model
{
//    public $token;
    public $newPassword;
    public $reNewPassword;
    
    public function rules()
    {
        return [
            [['newPassword', 'reNewPassword'], 'required'],
//            [['token'], 'string', 'min' => 64, 'max' => 64],
            [['newPassword', 'reNewPassword'], 'string', 'min' => 8],
            ['reNewPassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Пароли не совпадают'],
        ];
    }
    
    public function attributeLabels() {
        return [
            'newPassword' => 'Новый пароль',
            'reNewPassword' => 'Новый пароль, ещё раз',
        ];
    }

    public function save(UserToken $token)
    {
        try
        {
            $user = $token->user;
            $user->setPassword($this->newPassword);
            $user->generateAuthKey();
            $user->save();
            $token->delete();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    
}