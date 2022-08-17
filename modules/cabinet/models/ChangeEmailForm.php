<?php

namespace app\modules\cabinet\models;

use Yii;
use yii\base\Model;
use app\models\User;
use app\models\MessageTemplate;
use app\models\Event;
use yii\helpers\Html;
use yii\helpers\Url;

class ChangeEmailForm extends Model
{
    public $email;
    public $password;
    
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            [['password'], 'string', 'min' => 8],
            ['email', 'validateEmail'],
            ['password', 'validatePassword'],
        ];
    }    
    
    public function attributeLabels() {
        return [
            'email' => 'Новый Email',
            'password' => 'Ваш пароль',
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findOne(Yii::$app->user->id);

            if (!$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный пароль.');
            }
        }
    }


    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::find()->where(['email' => $this->email])->one();

            if (isset($user)) {
                $this->addError($attribute, 'Этот адрес уже используется в системе.');
            }
        }
    }

    public function changeEmail()
    {
        $key = Yii::$app->user->identity->generateEmailChangeToken($this->email);
        if (!$key)
        {
            Yii::$app->session->addFlash('danger', 'Не удалось сгенерировать ключ');
            return false;        
        }

        $data = [
            'uid' => Yii::$app->user->id,
            'email' => Html::encode($this->email),
            'link' => Url::to(['/site/change-email', 'token' => $key], true),
        ];

        $event = Event::add(null, Event::USER_CHANGE_EMAIL, $data);
        if (!$event)
        {
            Yii::$app->session->addFlash('danger', 'Ошибка при генерации события');
            return false;
        }

        return true;
/*
        $template = MessageTemplate::find()->where([
            'type' => MessageTemplate::TEMPLATE_EMAIL,
            'event' => Event::USER_CHANGE_EMAIL,
        ])->limit(1)->one();
        $tdata = json_decode($template->data);

        $data['%email%'] = Html::encode($this->email);
        $data['%link%'] = Url::to(['/site/change-email', 'token' => $key], true);
        
        $title = str_replace(array_keys($data), array_values($data), $tdata->title);
        $text = str_replace(array_keys($data), array_values($data), $tdata->text);

        if (Yii::$app->params['debugCommunication'])
        {

            file_put_contents( Yii::getAlias( Yii::$app->params['debugMailDir'] ) . $this->email . '_' . time() . '.html',
                ' <head><meta charset="UTF-8"></head>'.
                '<h1>' . $title . "</h1><br>" . $text
            );

            return true;
        }

        try
        {
            $this->mailer
                ->setTo($this->email)
                ->setSubject($title)
                ->setTextBody(strip_tags($text))
                ->setHtmlBody($text)
                ->send();
        } catch (\Exception $e)
        {
            Yii::error( 'Не удалось отправить сообщение '.$this->email. "\n"
                       .$title . "\n"
                       .$e->getMessage() );
            return false;
        }

        return true;*/
    }

}