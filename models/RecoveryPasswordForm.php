<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Event;
use yii\helpers\HtmlPurifier;

class RecoveryPasswordForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['email'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Проверочный код',
        ];
    }

    public function send()
    {
        $user = User::findByEmail($this->email);
        if (!isset($user))
        {
            Yii::$app->session->addFlash('danger', 'Пользователь не сущестует');
            return false;
        }

        $token = $user->generatePasswordResetToken();

        if ($token === false)
        {
            Yii::$app->session->addFlash('danger', 'Ошибка при генерации ключа');
            return false;
        }

        $data = [
            'uid' => $user->id,
            'email' => $user->email,
            'link' => Url::to(['site/reset', 'token' => $token], true),
        ];

        $event = Event::add(null, Event::USER_RESET_PASSWORD, $data);
        if (!$event)
        {
            Yii::$app->session->addFlash('danger', 'Ошибка при генерации события');
            return false;
        }
        
        return true;
    }

    
}
