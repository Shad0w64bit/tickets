<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use app\models\Event;
use app\models\User;
use app\models\Permission;

use yii\helpers\Url;

class Notification extends Model
{
    
    public static function send(Event $event)
    {   
        if (isset($event))
        {
//            $content = http_build_query([
//                'event' => $event->id,
//            ]);
            
            // Вызов curl с вызовом sendOut
            // Что бы не дожидаясь ответа, все отправить                    
//            self::fastRequest( '//tick/site/send-all?event='.$event->id);
//            die( Url::toRoute(['/site/send-all'], '') );
            self::fastRequest( Url::toRoute(['/site/send-all'], ''),
                $event->toArray() );// '//tick/site/send-all?event='.$event->id);
        }
        return true;
    }
    
    private static function fastRequest($url, $content)
    {
        //            Yii::$app->getRequest()->csrfParam => Yii::$app->getRequest()->getCsrfToken(),
        
        $body = http_build_query($content);

//        $content = '';
        $parts = parse_url($url);
        $fp = fsockopen( $parts['host'], 
            (isset($parts['port'])) ? $parts['port'] : 80,
            $errno,
            $errstr,
            10);               
        
        $out = "POST " . $parts['path'] . " HTTP/1.1\r\n"
            . "Host: " . $parts['host'] . "\r\n"
            . "Content-Type: application/x-www-form-urlencoded\r\n"
//            . "Content-Type: multipart/form-data\r\n"
            . "Content-Length: " . strlen($body) . "\r\n"
            . "Connection: Close\r\n\r\n";

/*    var_dump($out, $body);
    die();*/

        fwrite($fp, $out);
        fwrite($fp, $body);        

/*        header('Content-type: text/plain');
        while (!feof($fp))
        {
            echo fgets($fp, 1024);
        }
*/
        fclose($fp);
    }
    
    public static function sendOut(\app\models\Event $event)
    {        
        set_time_limit(180);

        $ticket = $event->ticket;
        $user = $event->user;

        $users = [];
        $staffs = [];
        //$admins = [];

        switch ($event->type) {
            case Event::TICKET_ASSIGN_DEPARTAMENT:
            case Event::TICKET_ASSIGN_USER:
            case Event::TICKET_OPEN:
            case Event::TICKET_REOPEN:
            case Event::TICKET_NEW_MESSAGE:
            case Event::TICKET_CLOSE:
                if ($event->user->isStaff())
                {
                    $users[] = $ticket->user;
                } else {
                    if ($ticket->assign_to)
                    {
                        $staffs[] = User::findOne( $ticket->assign_to );
                    } else {
                        $staffs = User::find()
                            ->join('INNER JOIN', 'permission', 'permission.group_id = '. $ticket->group_id . ' AND permission.access & '.Permission::ACCESS_NOTIFY)
                            ->where('user.id = permission.user_id')
                            ->andWhere(['IN', 'user.organization_id', Yii::$app->params['main_organizations'] ])
                            ->all();
                    }
                }

                break;

            case Event::USER_ACTIVATE:
            case Event::USER_CHANGE_EMAIL:
            case Event::USER_RESET_PASSWORD:
                $users[] = $user;
            default:
                break;
        }

        //$event->type

        // Users
        // Staff
        // Admin

        $mailer = null;
        if (isset($ticket))
        {
            $email = $ticket->group->sendEmail;

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
                ])
                ->compose()
                ->setFrom($email->mail);
        }

        foreach ($users as &$user) {
            $user->notify($event, $mailer);
        }

        foreach ($staffs as &$staff) {
            $staff->notify($event, $mailer);
        }
    }
    
    
}