<?php
namespace app\models;

use yii\base\Model;
use app\models\Organization;

/**
 * Signup form
 */
class OrganizationForm extends Model
{
    public $name;
    public $inn;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'string', 'max' => 500],

            ['inn', 'integer'],
            ['inn', 'string', 'min' => 10, 'max' => 12],
        ];
    }

    public function add() {
        if (!$this->validate()) {
            return null;
        }

        $org = new Organization();

        $org->name = $this->name;
        $org->inn = $this->inn;

        return $org->save() ? $org : null;
    }

}
