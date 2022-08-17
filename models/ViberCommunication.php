<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use app\models\Communication;

class ViberCommunication extends Communication
{
    protected $type = 'Viber';
    protected $accessKey;
    protected $user;

    public function send(Event $e) {
//        $e
        if (!isset($user))

/*        if (!isset($data['title'], $data['text'], $this->accessKey))
        {
            throw new Exception('Subject, Text and AccessKey must be set');
        }

        $data['viber'] = $this->accessKey;

        file_put_contents( Yii::getAlias( Yii::$app->params['debugMailDir'] ) . 'Viber_' . uniqid() . '.txt',
     //           print_r($data,true)
            $data['title'] . "\r\n\r\n" . $data['text']
        );*/
        return true;
    }

    // $user : Пользователь
    // accessKey : Ключ доступа Viber
    public function __construct(array $config) {
        parent::__construct($config);

        
    }
}

