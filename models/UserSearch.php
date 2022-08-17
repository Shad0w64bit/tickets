<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form of `app\models\User`.
 */
class UserSearch extends User
{
    public $organization_name;
    public $fullname;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'organization_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['organization_name', 'fullname', 'email', 'first_name', 'last_name', 'auth_key', 'password_hash'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();

        // add conditions that should always apply here
        $query->joinWith('organization');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['organization_name'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['organization.name' => SORT_ASC],
            'desc' => ['organization.name' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['fullname'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['user.last_name' => SORT_ASC],
            'desc' => ['user.last_name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user.id' => $this->id,
            'user.organization_id' => $this->organization_id,
            'user.status' => $this->status,
            'user.created_at' => $this->created_at,
            'user.updated_at' => $this->updated_at,
            'user.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'user.email', $this->email])
//            ->andFilterWhere(['like', 'user.first_name', $this->first_name])
//            ->andFilterWhere(['like', 'user.last_name', $this->last_name])
            ->andFilterWhere(['like', 'user.auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'user.password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'organization.name', $this->organization_name])
            ->andFilterWhere(['OR', ['like', 'user.first_name', $this->fullname], ['like', 'user.last_name', $this->fullname]] );

        return $dataProvider;
    }
}
