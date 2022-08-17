<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use app\models\User;

/**
 * ContactForm is the model behind the contact form.
 */
class UserCreateForm extends User
{
    public $password;
    public $repassword;
    
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['password','repassword'], 'required'],
                ['password', 'validatePasswordInput'],
            ]
        );
    }

    public function attributeLabels()
    {
         return ArrayHelper::merge(
                parent::attributeLabels(),
                [
                    'password' => 'Пароль',
                    'repassword' => 'Подтверждение пароля',
                ]
            );
    }
    
    public function validatePasswordInput($attribute, $params)
    {
        if ($this->password !== $this->repassword)
        {
            $this->addError('repassword', 'Пароли не совпадают');
        } elseif (strlen($this->password) < 8) {
              $this->addError('password', 'Пароль должен состоять как минимум из 8 символов');
              
        } else {
            $this->setPassword($this->password);
        }
    }

}
