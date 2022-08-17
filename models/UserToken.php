<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_token}}".
 *
 * @property int $id
 * @property int $uid
 * @property int $type
 * @property string $token
 * @property array $data
 * @property int $created_at
 *
 * @property User $user
 */
class UserToken extends \yii\db\ActiveRecord
{
    const TYPE_ACTIVATE = 1;
    const TYPE_RESET = 3;
    const TYPE_CHANGE_EMAIL = 5;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_token}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'type', 'token'], 'required'],
            [['uid', 'type', 'created_at'], 'integer'],
            [['token'], 'string', 'max' => 64],
            [['data'], 'safe'],
            [['token'], 'unique'],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'type' => 'Type',
            'token' => 'Token',
            'date' => 'Date',
        ];
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert))
        {
            if (!isset($this->created_at)) {
                $this->created_at = time();
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
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
}
