<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%event}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $ticket_id
 * @property int $type
 * @property string $description
 * @property int $date
 *
 * @property Ticket $ticket
 * @property User $user
 */
class Event extends \yii\db\ActiveRecord
{
    const TICKET_UNKNOWN = 0;   
    const STAFF = 0x40000000;
    const CLIENT = 0x80000000;
    
    // Действия //
    const TICKET_OPEN    = 1;
    const TICKET_REOPEN  = 2;
    const TICKET_CLOSE   = 3;
    const TICKET_ASSIGN_USER        = 4;
    const TICKET_ASSIGN_DEPARTAMENT = 5;
    // Действия //
    
    const TICKET_NEW_MESSAGE = 6;
    const USER_ACTIVATE = 50;
    const USER_CHANGE_EMAIL = 51;
    const USER_RESET_PASSWORD = 52;
   
    
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'date',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%event}}';
    }    
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type'], 'required'],
            [['user_id', 'ticket_id', 'type', 'date'], 'integer'],
            [['description'], 'string', 'max' => 200],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::className(), 'targetAttribute' => ['ticket_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'ticket_id' => 'Ticket ID',
            'type' => 'Type',
            'description' => 'Description',
            'date' => 'Date',
        ];
    }
    
    public static function getAllEvents()
    {
        return [
//            'Служебные' =>[
                Event::USER_ACTIVATE => 'Активация учетной записи',
                Event::USER_CHANGE_EMAIL => 'Изменение Email',
                Event::USER_RESET_PASSWORD => 'Сброс пароля',
            
//            ],
//            'Ответ Клиенту' => [
                Event::TICKET_OPEN | Event::CLIENT => 'Открытие заявки (Клиент)',
                Event::TICKET_REOPEN | Event::CLIENT => 'Переоткрытие заявки (Клиент)',
                Event::TICKET_CLOSE | Event::CLIENT=> 'Закрытие заявки (Клиент)',    
                Event::TICKET_ASSIGN_USER | Event::CLIENT=> 'Заявка назначена пользователю (Клиент)',
                Event::TICKET_ASSIGN_DEPARTAMENT | Event::CLIENT=> 'Заявка передана в отдел (Клиент)',

                Event::TICKET_NEW_MESSAGE | Event::CLIENT => 'Новое сообщение в заявке (Клиент)',    
/*            ],
            'Ответ сотруднику' => [*/
                Event::TICKET_OPEN | Event::STAFF => 'Открытие заявки (Сотрудник)',
                Event::TICKET_REOPEN | Event::STAFF => 'Переоткрытие заявки (Сотрудник)',
                Event::TICKET_CLOSE | Event::STAFF => 'Закрытие заявки (Сотрудник)',    
                Event::TICKET_ASSIGN_USER | Event::STAFF => 'Заявка назначена пользователю (Сотрудник)',
                Event::TICKET_ASSIGN_DEPARTAMENT | Event::STAFF => 'Заявка передана в отдел (Сотрудник)',

                Event::TICKET_NEW_MESSAGE | Event::STAFF => 'Новое сообщение в заявке (Сотрудник)',    
//            ],
        ];
    }    
    
    public static function add($ticketId, $type, $data = null, $date=null, $notification = true)
    {
        $event = new Event();
        $event->user_id = (isset($data['uid'])) ? $data['uid'] : Yii::$app->user->identity->id;
        $event->ticket_id = $ticketId;
        $event->type = $type;
//        $event->description = $data;
        if (isset($date))
        {
            $event->date = $date;    
        }
        if (isset($data))
        {
            $event->description = json_encode($data);    
        }
        
        
        if ( $event->save() )
        {
            if ($notification) { Notification::send($event); }            
            return $event;
        }
//        Yii::$app->session->addFlash('danger', print_r($event->errors, true));
        return null;        
    }

    public function getDescription() {
        return json_decode($this->description);
    }

    public function setDescription($data) {
        $this->description = json_encode($data);
    }
    
    static public function makeSingleArray($array, $path = '')
    {
        if (!is_array($array)) { return false; }

        $tmp = [];    
        foreach ($array as $key => $val)
        {        
            $id = (empty($path)) ? $key : "$path.$key";
            if (is_array($val))
            {
                $tmp = array_merge($tmp, self::makeSingleArray($val, $id));
            } else {
                $tmp[$id] = $val;
            }
        }
        return $tmp;
    }

    public function format() {
        $format = [
            Event::TICKET_UNKNOWN   
                => 'Unknown',
            Event::TICKET_OPEN      
                => 'Заявка <b>открыта</b> <i>%user%</i> %date%',
            Event::TICKET_REOPEN    
                => 'Заявка <b>переоткрыта</b> <i>%user%</i> %date%',
            Event::TICKET_CLOSE     
                => 'Заявка <b>закрыта</b> <i>%user%</i> %date%',
            Event::TICKET_ASSIGN_USER    
                => 'Заявка <b>назначена</b> пользователю <i>%assign_to%</i> пользователем <i>%user%</i> %date%',
            Event::TICKET_ASSIGN_DEPARTAMENT    
                => 'Заявка <b>передана</b> отделу <i> %departament%</i> пользователем <i>%user%</i> %date%',            
        ];
        
        if (isset($this->description) && !empty($this->description))
        {
            $data = self::makeSingleArray( json_decode($this->description, true) );
           
/*            foreach ($data as $key => $val)
            {
                $data["\%$key\%"] = $val;
                unset($key);
            }*/
            if (is_array($data))
            {
                $oldKeys = array_keys($data);
                foreach ($oldKeys as $key)
                {
                    $newKeys[] =  "%$key%";
                }

                $data = array_combine($newKeys, array_values($data));            
            } else {
                $data = [];
            }
            
        } else {
            $data = [];
        }
        
        $data = array_merge($data, [
            '%user%' => $this->user->getFullName(),
            '%date%' => Yii::$app->formatter->asDatetime($this->date),
        ]);
        
        $str = str_replace(
            array_keys($data), 
            array_values($data), 
            $format[$this->type]);
        
        $replace = [];
               
        
        if ( preg_match_all('/([0-9]{1,10})\:date/', $str, $dates, PREG_SET_ORDER ) ) 
        {
            foreach ($dates as $date)
            {
                $replace[ $date[0] ] = Yii::$app->formatter->asDatetime($date[1]);
            }
        }
    
        return str_replace(array_keys($replace), array_values($replace), $str);
    }
        


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::className(), ['id' => 'ticket_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
