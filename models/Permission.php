<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%permission}}".
 *
 * @property int $id
 * @property int $group_id
 * @property int $user_id
 * @property int $access
 *
 * @property Group $group
 * @property User $user
 */
class Permission extends \yii\db\ActiveRecord
{    
    const ACCESS_NOTIFY = 1 << 0;       // 1
    const ACCESS_READ = 1 << 1;         // 2
    const ACCESS_CREATE = 1 << 2;       // 4
    const ACCESS_WRITE = 1 << 3;        // 8
    const ACCESS_DELETE = 1 << 4;       // 16
    const ACCESS_ASSIGN_GROUP = 1 << 5; // 32
    const ACCESS_ASSIGN_USER = 1 << 6;  // 64
    const ACCESS_ONLY_THEIR = 1 << 7;   // 128
    
    const ACCESS_FULL = Permission::ACCESS_ASSIGN_GROUP | Permission::ACCESS_ASSIGN_USER |
                        Permission::ACCESS_CREATE | Permission::ACCESS_DELETE |
                        Permission::ACCESS_READ | Permission::ACCESS_WRITE |
                        Permission::ACCESS_NOTIFY; 
    
//    public $permission;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%permission}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'user_id', 'access'], 'required'],
            [['group_id', 'user_id', 'access'], 'integer'],
            [['permission'],'safe'],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
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
            'group_id' => 'Group ID',
            'user_id' => 'User ID',
            'access' => 'Доступ',
        ];
    }
    
    public function getTextPermission()
    {
        $access = [];
        if ($this->access & Permission::ACCESS_ONLY_THEIR)
        {
            $access[] = 'Только свои заявки';
        }

/*        if ($this->access & Permission::ACCESS_FULL)
        {
            $access[] = 'Полный доступ';
            return implode(', ', $access);
        }*/
                    
        if ($this->access & Permission::ACCESS_ASSIGN_GROUP)
        {
            $access[] = 'Назначение группе';
        }
        
        if ($this->access & Permission::ACCESS_ASSIGN_USER)
        {
            $access[] = 'Назначение сотруднику';
        }
        
        if ($this->access & Permission::ACCESS_NOTIFY)
        {
            $access[] = 'Уведомление';
        }
        
        if ($this->access & Permission::ACCESS_READ)
        {
            $access[] = 'Чтение';
        }
        
        if ($this->access & Permission::ACCESS_CREATE)
        {
            $access[] = 'Создание';
        }
        
        if ($this->access & Permission::ACCESS_WRITE)
        {
            $access[] = 'Запись';
        }       
        
        if ($this->access & Permission::ACCESS_DELETE)
        {
            $access[] = 'Удаление';
        }
        return implode(', ', $access);
    }
        
    public function getPermission() {
        $access = [];
        if ($this->access & Permission::ACCESS_ASSIGN_GROUP)
        {
            $access[] = Permission::ACCESS_ASSIGN_GROUP;
        }
        
        if ($this->access & Permission::ACCESS_ASSIGN_USER)
        {
            $access[] = Permission::ACCESS_ASSIGN_USER;
        }
        
        if ($this->access & Permission::ACCESS_NOTIFY)
        {
            $access[] = Permission::ACCESS_NOTIFY;
        }
        
        if ($this->access & Permission::ACCESS_READ)
        {
            $access[] = Permission::ACCESS_READ;
        }
        
        if ($this->access & Permission::ACCESS_CREATE)
        {
            $access[] = Permission::ACCESS_CREATE;
        }
        
        if ($this->access & Permission::ACCESS_WRITE)
        {
            $access[] = Permission::ACCESS_WRITE;
        }
        
        if ($this->access & Permission::ACCESS_DELETE)
        {
            $access[] = Permission::ACCESS_DELETE;
        }

        if ($this->access & Permission::ACCESS_ONLY_THEIR)
        {
            $access[] = Permission::ACCESS_ONLY_THEIR;
        }
        
        return $access;        
    }
    
    /*  BadFix */    
    public function setAttributes($values, $safeOnly = true)
    {
        if (is_array($values)) {
            $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    $method = 'set'.$name;
                    if (method_exists($this, $method))
                    {
                        $this->$method($value);
                    } else {
                        $this->$name = $value;
                    }                    
                } elseif ($safeOnly) {
                    $this->onUnsafeAttribute($name, $value);
                }
            }
        }
    }
    
    
    public function setPermission($value) {
        $p = 0;        
        if (is_array($value))
        {
            foreach ($value as $a)
            {
                $p = $p + $a;
            }
        }
        $this->access = $p;
    }    
    
    public function beforeSave($insert) {
        if ($this->isNewRecord)
        {
            $item = Permission::find()->where([
                'group_id' => $this->group_id,
                'user_id' => $this->user_id,
            ])->limit(1)->one();            
            if (isset($item))
            {
                \Yii::$app->session->addFlash('danger', 'Правило уже существует');
                return false;
            }
        }
        return parent::beforeSave($insert);
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
}
