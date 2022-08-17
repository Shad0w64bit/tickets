<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use app\models\Communication;
use yii\helpers\Url;
use yii\helpers\Html;

class EmailCommunication extends Communication
{
    protected $type = 'Email';
//    protected $email;
    protected $mailer;

    public function send(Event $e) {
        if (!isset($this->user) || !isset($this->mailer))
        {
            throw new Exception('User and Mailer must be set');
        }
                
        $event = $e->type;

        switch ($event)
        {
            case Event::USER_ACTIVATE:
            case Event::USER_CHANGE_EMAIL:
            case Event::USER_RESET_PASSWORD:
                break;
            default:
                 $event = ($event | (($this->user->isStaff()) ? Event::STAFF : Event::CLIENT ));
        }

        $template = MessageTemplate::find()->where([
            'type' => MessageTemplate::TEMPLATE_EMAIL,
            'event' => $event,
        ])->limit(1)->one();

        $tdata = json_decode($template->data);
        $eventData = json_decode( $e->description );

        switch ($e->type)
        {
            case Event::USER_ACTIVATE:
            case Event::USER_CHANGE_EMAIL:
            case Event::USER_RESET_PASSWORD:
                $data = (isset($eventData)) ? json_decode($e->description, true) : [];
                break;

            default:
                $data = array_merge(
        //            Event::makeSingleArray( [ 'event' => $event->toArray() ] ),
                    Event::makeSingleArray( [ 'user' => $e->user->toArray() ] ),
                    Event::makeSingleArray( [ 'ticket' => $e->ticket->toArray() ] ),
                    Event::makeSingleArray( [ 'ticket.user' => $e->ticket->user->toArray() ] ),
                    ($e->ticket->assign_to > 0) ? Event::makeSingleArray( [ 'ticket.assigned' => $e->ticket->assigned->toArray() ] ) : [],
                    [
                        'ticket.string_id' => $e->ticket->getId(),
                        'ticket.link' => Url::to(['/cabinet/ticket/view', 'id' => $e->ticket->id], true),
                    ]
        //                            Event::makeSingleArray( [ 'ticket.assigned' => $ticket->assign()->toArray() ] )
        //           (isset($event->description)) ? $this->makeSingleArray( [ 'data' => json_decode($event->description) ] ) : []
                );


                // XSS Protect

                $data['ticket.title'] = Html::encode($data['ticket.title']);

                // End XSS Protect
                break;

        }

        switch ($e->type) {
            case Event::TICKET_OPEN:
            case Event::TICKET_REOPEN:
            case Event::TICKET_NEW_MESSAGE:
            case Event::TICKET_CLOSE:

                $message = Message::findOne($eventData->msg);
                $data = array_merge( $data,
                    Event::makeSingleArray( [ 'message' => $message->toArray() ] ),
                    Event::makeSingleArray( [ 'message.user' => $message->user->toArray() ] )
                );
                $data['message.text'] = Html::encode($data['message.text']);
                break;           

            default:
                break;
        }

        // Преобразуем к формату %string%

        if (is_array($data))
        {
            $oldKeys = array_keys($data);
            foreach ($oldKeys as $key)
            {
                $newKeys[] =  "%$key%";
            }

            foreach ($data as $key => $val)
            {
                if ( strpos($key, 'created_at') || strpos($key, 'updated_at')
                     || strpos($key, 'date') )
                {
                    $newValues[] =   Yii::$app->formatter->asDatetime($val);
                } else {
                    $newValues[] =  $val;
                }
            }

//                            var_dump( $newKeys, array_values($d) );

            $data = array_combine($newKeys, $newValues);

        } else {
            $data = [];
        }

        // Закончили преобразование

//        var_dump($data); die();

        $title = str_replace(array_keys($data), array_values($data), $tdata->title);
        $text = str_replace(array_keys($data), array_values($data), $tdata->text);

        if ($e->type == Event::USER_CHANGE_EMAIL)
        {
            $to = $eventData->email;
        } else {
            $to = $this->user->email;
        }


        if (Yii::$app->params['debugCommunication'])
        {

            file_put_contents( Yii::getAlias( Yii::$app->params['debugMailDir'] ) . $to . '_' . time() . '.html',
                ' <head><meta charset="UTF-8"></head>'. 
                '<h1>' . $title . "</h1><br>" . $text
            );

            return true;
        }

        try
        {
            $this->mailer
                ->setTo($to)
                ->setSubject($title)
                ->setTextBody(strip_tags($text))
                ->setHtmlBody($text)
                ->send();
        } catch (\Exception $ex)
        {
            Yii::error( 'Не удалось отправить сообщение '.$to. "\n"
                       .$title . "\n"
                       .$ex->getMessage() );
            return false;
        }

        return true;
    }
}

