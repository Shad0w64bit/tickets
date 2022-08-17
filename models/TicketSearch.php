<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Ticket;

/**
 * TicketSearch represents the model behind the search form of `app\models\Ticket`.
 */
class TicketSearch extends Ticket
{
    public $organization_name;
    public $organization_id;
    public $user_email;
    public $category_name;
        
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'group_id', 'user_id', 'status', 'created_at', 'updated_at', 'closed_at'], 'integer'],
            [['title', 'organization_name', 'organization_id', 'user_email', 'category_name'], 'safe'],
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
        $query = Ticket::find();
        $query->leftJoin('user', 'user.id=ticket.user_id')
            ->leftJoin('organization', 'organization.id=user.organization_id')
            ->leftJoin('permission', 'permission.user_id='.\Yii::$app->user->identity->id)                
            ->leftJoin('group', 'group.id=ticket.group_id');

        /*
SELECT ticket.*
FROM `permission`
RIGHT JOIN `ticket` ON  ticket.group_id = permission.group_id AND permission.access & 2 AND ((permission.access & 128 AND ticket.assign_to = permission.user_id) OR (NOT permission.access & 128))
WHERE `permission`.`user_id` = 5
        */

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->sort->attributes['organization_id'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['organization.name' => SORT_ASC],
            'desc' => ['organization.name' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['organization_name'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['organization.name' => SORT_ASC],
            'desc' => ['organization.name' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['category_name'] = [
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

        $query->filterWhere(
            ['AND',
                ['AND',
                    'ticket.group_id = permission.group_id',
                    ['&', 'permission.access', Permission::ACCESS_READ],
                ],
                ['OR',
                    ['AND',
                        ['&', 'permission.access', Permission::ACCESS_ONLY_THEIR],
                        (Yii::$app->user->identity->isStaff())
                            ? 'ticket.assign_to = permission.user_id'
                            : 'ticket.user_id = permission.user_id',
                    ],
                    ['NOT',
                        ['&', 'permission.access', Permission::ACCESS_ONLY_THEIR],
                    ]
                ],
            ]
        );
        

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ticket.id' => $this->id,
            'ticket.group_id' => $this->group_id,
            'ticket.user_id' => $this->user_id,
            'ticket.status' => $this->status,
            'ticket.created_at' => $this->created_at,
            'ticket.closed_at' => $this->closed_at,
            'organization.id' => $this->organization_id,
        ]);
                       
        
        $query->andFilterWhere(['like', 'ticket.title', $this->title])
                ->andFilterWhere(['like', 'category.name', $this->category_name])
                ->andFilterWhere(['like', 'organization.name', $this->organization_name])
                ->andFilterWhere(['like', 'user.email', $this->user_email]);
//        $query->orderBy(['ticket.updated_at'=>SORT_DESC]);
        return $dataProvider;
    }
}
