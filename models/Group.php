<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%group}}".
 *
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property int $manager 
 * @property int $email 
 *
 * @property Category[] $categories
 * @property Email $email 
 * @property User $manager 
 * @property Organization $organization
 * @property Permission[] $permissions
 */
class Group extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%group}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['organization_id', 'name'], 'required'],
            [['organization_id', 'manager', 'email'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['email'], 'exist', 'skipOnError' => true, 'targetClass' => Email::className(), 'targetAttribute' => ['email' => 'id']], 
            [['manager'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['manager' => 'id']], 
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'organization_id' => 'Organization ID',
            'name' => 'Название',
            'manager' => 'Менеджер',
            'email' => 'Почта',
        ];
    }
    
    public function beforeValidate() {        
        if ($this->manager <= 0)
        {
            $this->manager = null;
        }

        if ($this->email <= 0)
        {
            $this->email = null;
        }            
        
        return parent::beforeValidate();
    }

    public function getSendEmail()
    {
        if (isset($this->email))
        {
            return $this->getEmail0();
        }
        return Email::findOne( Yii::$app->params['emailDefault'] );

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'organization_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(Permission::className(), ['group_id' => 'id']);
    }
    
    /** 
      * @return \yii\db\ActiveQuery 
      */ 
    public function getEmail0() 
    { 
        return $this->hasOne(Email::className(), ['id' => 'email']); 
    } 

    /** 
     * @return \yii\db\ActiveQuery 
     */ 
    public function getManager0() 
    { 
        return $this->hasOne(User::className(), ['id' => 'manager']); 
    }

}
