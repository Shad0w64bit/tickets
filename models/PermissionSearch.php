<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Permission;

/**
 * PermissionSearch represents the model behind the search form of `app\models\Permission`.
 */
class PermissionSearch extends Permission
{
    public $user_email;
    public $group_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'group_id', 'user_id', 'access'], 'integer'],
            [['group_name', 'user_email'], 'safe']
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
        $query = Permission::find();

        // add conditions that should always apply here
        
        $query->joinWith('group');
        $query->joinWith('user');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->sort->attributes['group_name'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['group.name' => SORT_ASC],
            'desc' => ['group.name' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['user_email'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['user.email' => SORT_ASC],
            'desc' => ['user.email' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'permission.id' => $this->id,
            'permission.group_id' => $this->group_id,
            'permission.user_id' => $this->user_id,
            'permission.access' => $this->access,
        ]);
        
        $query->andFilterWhere(['like', 'user.email', $this->user_email])
              ->andFilterWhere(['like', 'group.name', $this->group_name]);

        return $dataProvider;
    }
}
