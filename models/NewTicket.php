<?php

namespace app\models;

use Yii;

class NewTicket extends Ticket {

    public $message;
    public $text;
    public $files;
    public $category_id;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['category_id', 'text'], 'required'],
            [['category_id','text','files'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'text' => 'Сообщение',
            'category_id' => 'Категория',
        ]);
    }

    function __construct()
    {
        parent::__construct();
        $this->message = new Message();
    }

    public function beforeValidate() {
        $this->status = 0;
        $this->closed_at = 0;        
        
        if (!isset($this->group_id))
        {
            $cat = Category::findOne($this->category_id);       
            $this->group_id = $cat->group_id;
        }
        

        if (!isset($this->user_id))
        {
            if (Yii::$app->user->isGuest)
            {
                throw new \yii\base\Exception('User is not authorized');
            }

            $this->user_id = Yii::$app->user->identity->id;
        }

        return parent::beforeValidate();
    }

    public function save($runValidation = true, $attributeNames = null) {        
        if (parent::save($runValidation, $attributeNames))
        {
            $this->message->ticket_id = $this->id;
            $this->message->text = $this->text;
            $this->message->files = $this->files;

//            var_dump($this->text);
/*            if (count($this->message->errors) > 0)
            {*/
//                var_dump($this->message->errors);
            //}

            if ($this->user_id !== Yii::$app->user->id)
            {
                $user = User::findOne($this->user_id);
                $data['user'] = $user->fullname;
            }
            //Event::add($this->id, Event::TICKET_OPEN, json_encode($data), $this->created_at )
            if ($this->message->save(false))
            {
                $data['msg'] = $this->message->id;
                Event::add($this->id, Event::TICKET_OPEN, $data, $this->created_at ) ;
                return true;
            }
            return false;

//            return ($this->message->save(true)) ? true : false;

        }
    }


}