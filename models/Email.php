<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%email}}".
 *
 * @property int $id
 * @property string $host
 * @property string $port
 * @property string $username
 * @property string $password
 * @property string $mail
 * @property string $encryption
 *
 * @property Group[] $groups
 */
class Email extends \yii\db\ActiveRecord
{
    public $newPassword;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%email}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['host', 'username', 'mail'], 'required'],
            ['mail','email'],
            ['newPassword', 'safe'],
            [['newPassword'], 'string', 'max' => 250],
            [['host'], 'string', 'max' => 255],
            [['username', 'mail'], 'string', 'max' => 320],
            [['password'], 'string', 'max' => 352],
            [['encryption','port'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'host' => 'Хост',
            'port' => 'Порт',
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'mail' => 'Почта',
            'encryption' => 'Шифрование',
            'newPassword' => 'Задать пароль',
        ];
    }
    
    public function beforeValidate() {
        if (!empty($this->newPassword))
        {
            $this->password = base64_encode(Yii::$app->getSecurity()->encryptByPassword($this->newPassword, Yii::$app->params['secretKey']));
        }        
        
        return parent::beforeValidate();
    }
    
    public function getEncryption()
    {
        switch ($this->encryption)
        {
            case 0: return '';
            case 1: return 'ssl';
            case 2: return 'tls';                    
        }
        return '';
    }        

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Group::className(), ['email' => 'id']);
    }
}
