<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property int $organization_id
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Message[] $messages
 * @property Permission[] $permissions
 * @property Ticket[] $tickets
 * @property Organization $organization
 */
class User extends ActiveRecord implements IdentityInterface
{
    const USER_ACTIVE = 1 << 0;
    const USER_ADMIN = 1 << 1;
    const USER_MODER = 1 << 2;
    const USER_BANNED = 1 << 3;

    private $_fullname = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['organization_id', 'email'], 'required'],
            [['organization_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['email'], 'string', 'max' => 140],
            [['email'], 'email'],
            ['permission','safe'],
            [['first_name', 'last_name'], 'string', 'max' => 30],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash'], 'string', 'max' => 180],
/*            [['password_reset_token'], 'string', 'max' => 100],
            [['password_reset_token'], 'unique'],*/
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
        ];
    }

/*    public function can($role) {
        return $this->status == $role;
    }*/
    
    public function perm($group_id, $permission)
    {        
        foreach ($this->permissions as $perm)
        {
            if ($perm->group_id == $group_id)
            {
                return (bool)($perm->access & $permission);
            }
        }
        return false;
    }
    
    public function alertPermission($group_id, $permission)
    {
        if (!$this->perm($group_id, $permission))
        {
            $group = Group::findOne($group_id)->name;
            switch ($permission)
            {
                case Permission::ACCESS_ONLY_THEIR:
                    throw new ForbiddenHttpException('Вы можете просматривать только свои заявки в группе '. $group);
                case Permission::ACCESS_ASSIGN_GROUP:
                    throw new ForbiddenHttpException('У вас нет прав на переназначение заявок в группе '. $group);
                case Permission::ACCESS_ASSIGN_USER:
                    throw new ForbiddenHttpException('У вас нет прав на назначение заявок сотрудникам группы '. $group);                    
                case Permission::ACCESS_NOTIFY:
                    throw new ForbiddenHttpException('У вас нет прав на получение уведомлений о заявках в группе '.$group);                    
                case Permission::ACCESS_READ:
                    throw new ForbiddenHttpException('У вас нет прав на чтение заявки в группе '.$group);
                case Permission::ACCESS_WRITE:
                    throw new ForbiddenHttpException('У вас нет прав на запись заявок в группу '.$group);                    
                case Permission::ACCESS_DELETE:
                    throw new ForbiddenHttpException('У вас нет прав на удаление заявок в группе '.$group);                                        
            }            
        }
        return true;  
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'organization_id' => 'Организация',
            'email' => 'Email',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'fullname' => 'Полное имя',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'status' => 'Статус',
            'permission' => 'Разрешения',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->generateAuthKey();
                if (empty($this->first_name))
                {
                    unset($this->first_name);
                }
                if (empty($this->last_name))
                {
                    unset($this->last_name);
                }

            }
            return true;
        }
        return false;
    }
    
    public function getAvatar() {
        return '/img/' . (($this->isUser()) ? 'user.png' : 'support.png');        
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public static function findIdentity($id)
    {
        return static::find()->where(['id' => $id])->one();
    }

    public static function findByEmail($email)
    {
        return static::find()->where(['email'=>$email])->one();
//        return static::findOne(['inn' => $inn]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString(32);
    }

    public function generatePasswordResetToken()
    {
        $data = ['uid' => $this->id, 'type' => UserToken::TYPE_RESET];

        UserToken::deleteAll($data);
        $token = new UserToken($data);
        $token->token = Yii::$app->security->generateRandomString(64);
        return ($token->save()) ? $token->token : false;
    }

    public function generateEmailChangeToken($email)
    {
        $data = ['uid' => $this->id, 'type' => UserToken::TYPE_CHANGE_EMAIL];
        UserToken::deleteAll($data);
        $data['data'] = json_encode(['email' => $email]);

        $token = new UserToken($data);
        $token->token = Yii::$app->security->generateRandomString(64);
        return ($token->save()) ? $token->token : false;
    }

    public function generateActivationToken()
    {
        $data = ['uid' => $this->id, 'type' => UserToken::TYPE_ACTIVATE];
        UserToken::deleteAll($data);

        $token = new UserToken($data);
        $token->token = Yii::$app->security->generateRandomString(64);
        return ($token->save()) ? $token->token : false;
    }

    public function validatePassword($password)
    {
//        return true;
        return Yii::$app->security->validatePassword($password, $this->password_hash);
//        return ($password === $this->inn);
    }

    public function notify(Event &$e, $mailer = null)
    {
        // GetCommunication for User

        if (($e->user->id === $this->id)
            && !($e->type == Event::USER_ACTIVATE)
            && !($e->type == Event::USER_CHANGE_EMAIL)
            && !($e->type == Event::USER_RESET_PASSWORD))
        {
            return true;
        }

        $comms = [];
        
        if (isset($this->email))
        {
            $email = Email::findOne(Yii::$app->params['emailDefault']);

            if (!isset($mailer))
            {
                $mailer = Yii::createObject([
                    'class' => 'yii\swiftmailer\Mailer',
                    'transport' => [
                         'class' => 'Swift_SmtpTransport',
                         'host' => $email->host,
                         'port' => $email->port,
                         'encryption' => $email->getEncryption(),
                         'username' => $email->username,
                         'password' => Yii::$app->getSecurity()->decryptByPassword(  base64_decode($email->password), Yii::$app->params['secretKey']),
                     ],
                ])
                ->compose()
                ->setFrom($email->mail);
            }
            

            $comms[] = new EmailCommunication([
                'user' => $this,
                'mailer' => $mailer,
            ]);
        }

        foreach ($comms as &$communication)
        {
            $communication->send($e);
        }

        

//        if (isset($this->)



        return true;
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
    
    public function getPermission() {
        $access = [];
        if ($this->status & User::USER_ACTIVE)
        {
            $access[] = User::USER_ACTIVE;
        }

        if ($this->status & User::USER_ADMIN)
        {
            $access[] = User::USER_ADMIN;
        }

        if ($this->status & User::USER_MODER)
        {
            $access[] = User::USER_MODER;
        }

        if ($this->status & User::USER_BANNED)
        {
            $access[] = User::USER_BANNED;
        }
        
        return $access;        
    }    


/*    public function setPermission($value) {
        $p = 0;        
        if (is_array($value))
        {
            foreach ($value as $a)
            {
                $p = $p + $a;
            }
        }
        $this->status = $p;
    }    
  */  
/*    public function onlyTheir()
    {
        return (bool)($this->status & User::ROLE_ONLY_THEIR);
    }*/

    public function isUser() {
        return !$this->isStaff();
    }

    public function isStaff() {
        return in_array($this->organization_id, Yii::$app->params['main_organizations']);
    }

    public function isAdmin()
    {
        return ($this->status & User::USER_ADMIN);
    }

    public function isModerator()
    {
        return ($this->status & User::USER_MODER);
    }

    public function isBanned()
    {
        return ($this->status & User::USER_BANNED);
    }

    public function getFullName() {
//        static $fullname = null;
        if (!isset($this->_fullname))
        {
            $this->_fullname = (isset($this->first_name) || isset($this->last_name))
                    ?  $this->last_name . ' ' . $this->first_name
                    : $this->email;
        }
        return $this->_fullname;
    }

    public function getCommunications()
    {
        return [            
            new EmailCommunication(['email' => $this->email]),
            new ViberCommunication(['accessKey' => 'LolYKey']),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(Permission::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'organization_id']);
    }
}
