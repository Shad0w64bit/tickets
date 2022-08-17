<?php

namespace app\models;

use Yii;
use app\models\Event;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%message}}".
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property string $text
 * @property int $created_at
 *
 * @property Ticket $ticket
 * @property User $user
 * @property MessageFile[] $messageFiles
 */
class Message extends \yii\db\ActiveRecord
{
    public $files;
    public $close;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'user_id', 'text',], 'required'],
            [['ticket_id', 'user_id', 'created_at'], 'integer'],
            [['text'], 'string', 'max' => 255],
            [['files', 'close'], 'safe'],
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
            'ticket_id' => 'Ticket ID',
            'user_id' => 'User ID',
            'text' => 'Text',
            'created_at' => 'Created At',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function beforeValidate() {
        if (parent::beforeValidate()) {

            if ($this->isNewRecord) {
                if (!isset($this->created_at))
                {
                    $this->created_at = time();
                }

                if (!isset($this->user_id))
                {
                    if (Yii::$app->user->isGuest)
                    {
                        throw new \yii\base\Exception('User is not authorized');
                    }

                    $this->user_id = Yii::$app->user->identity->id;
                }
            }
            return true;
        }
        return false;
    }
    
    public function save($notify = true, $runValidation = true, $attributeNames = null) {
        if (!isset($this->ticket->assign_to ))
        {
            if ( Yii::$app->user->identity->isStaff()
               && Yii::$app->user->identity->perm( 
                $this->ticket->group_id, 
                Permission::ACCESS_WRITE)
            ){
                $this->ticket->assign($this->ticket->group_id, Yii::$app->user->id);
                        
//                $this->ticket->assign_to = Yii::$app->user->id;
            }
        }
        
        if (parent::save($runValidation, $attributeNames))
        {
                                   
            foreach ($this->allFiles as $file)
            {
                $old = Yii::getAlias( Yii::$app->params['tmpDir'] . $file->file);

                $security = new \yii\base\Security();

                $uniq = $security->generateRandomString();
                $uniq = substr_replace($uniq,               // Dirt hack for Windows compatibility
                    strtolower( substr($uniq, 0, 2) ),
                    0, 2);
                $ext = pathinfo($old, PATHINFO_EXTENSION);
                $dir = function ($name) {
                    $res = Yii::getAlias(Yii::$app->params['uploadDir']) . substr($name, 0, 1) . '/' . substr($name, 1, 1) . '/';
                    @mkdir($res, 0777, true);
                    return $res;
                };
                $new = $dir($uniq) . $uniq . '.' . $ext;
                while (file_exists($new))
                {
                    $uniq .= rand(0, 9);
                    $new = $dir($uniq) . $uniq . '.' . $ext;
                }

//                die( json_encode(['error' => $new]));


//                $new = Yii::getAlias( Yii::$app->params['uploadDir'] . $file->file);
                
                if (@rename($old, $new))
                {
                    $f = new File();
                    $f->file = basename($new);
                    $f->name = $file->name;                    
                    $f->size = filesize($new);                     
                    if ($f->save())
                    {
                        $mf = new MessageFile();
                        $mf->file_id = $f->id;
                        $mf->message_id = $this->id;
                        $mf->save();
                    }                    
                }
                
                
            }
            if ($notify)
            {
                Event::add($this->ticket_id, Event::TICKET_NEW_MESSAGE, ['msg' => $this->id]);
            }

            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        
        //  Добавляем файлы в БД       
        
        if ($this->ticket->closed)
        {
            $this->ticket->open($this->id);
        } elseif ($this->close) {
            $this->ticket->close($this->id);
        }       
        
        $this->ticket->touch('updated_at');        
    }
    
    public function getAllFiles() {        
        if (isset($this->files['name']))
        {
            $keys = array_keys($this->files['name']);

            $files = [];
            foreach ($keys as $key)
            {
                $files[] = [
                    'name' => $this->files['name'][$key],
                    'file' => $this->files['file'][$key],
                    'size' => $this->files['size'][$key],
                ]; 
            }
            return json_decode(json_encode($files), FALSE);
        }
        return [];        
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


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageFiles()
    {       
        return $this->hasMany(File::className(), ['id' => 'file_id'])
                ->viaTable('message_file', ['message_id' => 'id']);
    }
}
