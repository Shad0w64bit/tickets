<?php
namespace app\models;

use yii\base\Model;
use app\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $organization_id;
    public $email;
    public $password;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['organization_id', 'required'],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 140],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->organization_id = $this->organization_id;
        $user->email = $this->email;
		//$user->first_name = 'Admin';
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->status = ( User::USER_ACTIVE & User::USER_ACTIVE & User::USER_MODER );

        if (!$user->validate())
        {
            return var_dump($user->errors);
        }


        return $user->save() ? $user : null;
    }
}
