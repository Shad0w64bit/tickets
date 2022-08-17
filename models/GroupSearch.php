<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Group;

/**
 * GroupSearch represents the model behind the search form of `app\models\Group`.
 */
class GroupSearch extends Group
{
    public $organization_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'organization_id'], 'integer'],
            [['organization_name', 'name'], 'safe'],
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
        $query = Group::find();
        
        $query->joinWith('organization');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->sort->attributes['organization_name'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['organization.name' => SORT_ASC],
            'desc' => ['organization.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'group.id' => $this->id,
            'group.organization_id' => $this->organization_id,
        ]);

        $query->andFilterWhere(['like', 'group.name', $this->name])
              ->andFilterWhere(['like', 'organization.name', $this->organization_name]);

        return $dataProvider;
    }
}
