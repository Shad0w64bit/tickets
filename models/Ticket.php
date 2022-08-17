<?php

namespace app\models;
use yii\behaviors\TimestampBehavior;

use Yii;

/**
 * This is the model class for table "{{%ticket}}".
 *
 * @property int $id
 * @property int $category_id
 * @property int $group_id
 * @property int $organization_id
 * @property int $user_id
 * @property string $title
 * @property int $assign_to
 * @property int $status
 * @property int $created_at
 * @property int $closed_at
 *
 * @property Message[] $messages
 * @property Category $category
 * @property User $user
 */
class Ticket extends \yii\db\ActiveRecord
{
    const TICKET_STATUS_OPEN = 0;
    const TICKET_STATUS_CLOSE = 10;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ticket}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'user_id', 'title', 'status'], 'required'],
            [['group_id', 'organization_id', 'user_id', 'assign_to', 'status', 'created_at', 'updated_at', 'closed_at'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization' => 'id']],
            [['user_id', 'assign_to'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_id' => 'Отдел',
            'organization_id' => 'Организация',
            'user_id' => 'Пользователь №',
            'title' => 'Тема',
            'assigned' => 'Назначено',
            'status' => 'Статус',            
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'closed_at' => 'Закрыто',
            'assign_to' => 'Назначено', 
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function close($msg = null) {
        $this->status = Ticket::TICKET_STATUS_CLOSE;
        $this->touch('closed_at');
        $data = ($msg) ? ['msg' => $msg] : NULL;
        return ($this->save() && Event::add($this->id, Event::TICKET_CLOSE, $data, ($this->created_at+1)));
    }

    public function open($msg = null) {
        $this->status = Ticket::TICKET_STATUS_OPEN;
        $this->closed_at = null;
        $status = ($this->isNewRecord) ? Event::TICKET_OPEN : Event::TICKET_REOPEN;
        $data = ($msg) ? ['msg' => $msg] : NULL;
                
        return ($this->save() && Event::add($this->id, $status, $data, ($this->created_at-1)));
    }

    public function getOpened() {
        return ($this->status !== Ticket::TICKET_STATUS_CLOSE);
    }

    public function getClosed() {
        return ($this->status == Ticket::TICKET_STATUS_CLOSE);
    }
    
    public function getActions()
    {
        $actions = array_merge($this->messages, $this->events);
        
        usort($actions, function ($a, $b) {
            $da = (get_class($a) == Message::class) ? $a->created_at : $a->date;
            $db = (get_class($b) == Message::class) ? $b->created_at : $b->date;
            
            if ($da == $db) {
                return 0;
            }
            return ($da < $db) ? -1 : 1;
        });
        
        return $actions;
    }

/*    
    public function newEvent($type, $data = null) {
        $event = new Event();
        $event->user_id = Yii::$app->user->identity->id;
        $event->ticket_id = $this->id;
        $event->type = $type;
        
        return $event->save();
    }
*/
    
    public function getId()
    {
        return str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
    
    public function save($runValidation = true, $attributeNames = null) {
//        $new = $this->isNewRecord;
        if (!isset($this->organization_id))
        {
            $this->organization_id = $this->user->organization_id;
        }
        
        if (parent::save($runValidation, $attributeNames))
        {            
//            return ($new) ? Event::add($this->id, Event::TICKET_OPEN, json_encode($data), $this->created_at ) : true;
            return true;
        }
        return false;
    }
    
    public function assign(int $gid = null, int $uid = null, $msg = null)
    {        
        if (!Yii::$app->user->identity->perm($gid, Permission::ACCESS_ASSIGN_GROUP))
        {
            return false;
        }

        $group = Group::findOne( (isset($gid)) ? $gid : $this->group_id );        
        
        if ($this->group_id != $gid)
        {
            $this->group_id = $gid;
            $this->assign_to = null;
            Event::add($this->id, Event::TICKET_ASSIGN_DEPARTAMENT, [
                'departament' => $group->name,
                (!$msg) ?: 'msg' => $msg,
            ]);
        }
                
        if (isset($uid) && ($uid != $this->assign_to) && Yii::$app->user->identity->perm($gid, Permission::ACCESS_ASSIGN_USER))
        {            
            $user = User::findOne($uid);
            if ($user->perm($gid, Permission::ACCESS_READ))
            {
                $this->assign_to = $uid;
                Event::add($this->id, Event::TICKET_ASSIGN_USER, [
                    'assign_to' => $user->fullname,
                    (!$msg) ?: 'msg' => $msg,
                ]);
            }                        
        }
        
        return $this->save();   
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['ticket_id' => 'id'])->orderBy('created_at');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Event::className(), ['ticket_id' => 'id'])->orderBy('date');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssigned()
    {
        return $this->hasOne(User::className(), ['id' => 'assign_to']);
    }

    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'organization_id'])
                ->via('user');
    }
}
