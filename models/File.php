<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%file}}".
 *
 * @property int $id
 * @property string $file
 * @property string $name
 * @property int $size
 * @property int $user_id
 * @property int $created_at
 *
 * @property User $user
 * @property MessageFile[] $messageFiles
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%file}}';
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file', 'name', 'size'], 'required'],
            [['size', 'user_id', 'created_at'], 'integer'],
            [['file'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 64],
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
            'file' => 'File',
            'name' => 'Name',
            'size' => 'Size',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }
    
    public function beforeValidate() {
        if(parent::beforeValidate())
        {
            if (!isset($this->user_id))
            {
                if (Yii::$app->user->isGuest)
                {
                    throw new \yii\base\Exception('User is not authorized');
                }

                $this->user_id = Yii::$app->user->identity->id;
            }
            return true;
        }
        return false;
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
        return $this->hasMany(MessageFile::className(), ['file_id' => 'id']);
    }
}
