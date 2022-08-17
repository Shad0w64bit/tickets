<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use app\models\Email;

class EmailTestForm extends Model
{
    public $sendFrom;
    public $sendTo;
    public $subject = 'Тестовое сообщение';
    public $body = "Поздравляем!\nТестовое сообщение успешно доставлено.";
    
    public function rules()
    {
        return [
            [['sendFrom','sendTo','subject','body'], 'required'],
            [['subject','body'], 'string'],
            [['sendTo'], 'email'],
            [['sendFrom','sendTo','subject','body'],'safe']
        ];
    }

    public function attributeLabels()
    {
         return [
            'sendFrom'  => 'Отправитель',
            'sendTo'    => 'Получатель',
            'subject'   => 'Тема письма',
            'body'      => 'Сообщение',             
        ];
    }
    
    public function send() {
        $email = Email::findOne($this->sendFrom);
        
        $mailer = Yii::createObject([
           'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => $email->host,
                'port' => $email->port,
                'encryption' => $email->getEncryption(),
                'username' => $email->username,
                'password' => Yii::$app->getSecurity()->decryptByPassword( base64_decode($email->password), Yii::$app->params['secretKey']),
            ],
        ]);
        
        try
        {
            $mailer->compose()
            ->setFrom($email->mail)
            ->setTo($this->sendTo)
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
            return true;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Yii::$app->session->addFlash('danger',
                mb_convert_encoding($msg, 'utf-8', 'cp1251'
            ));           
        }        
        return false;
    }
    
}
