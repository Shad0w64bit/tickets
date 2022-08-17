<?php

namespace app\modules\ajax\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;

class UserController extends Controller
{

    private function sortOrganization($data)
    {
        $result = [];
        $group = null;

        foreach ($data as $row)
        {
            if ($group['text'] !== $row['organization'])
            {
                if (isset($group))
                {
                    array_push($result,  $group);
                }
                $group = [
                    'text' => $row['organization'],
                    'children' => []
                ];
            }
            $group['children'][] = [
                'id' => $row['id'] ,
                'text' => $row['last_name'] . ' ' .$row['first_name'],
            ];
        }
        if (isset($group))
        {
            array_push($result, $group);
        }
        return $result;
    }


    public function actionUserSortList($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = new Query;
        $query->select('user.id, user.first_name, user.last_name, organization.name as organization')
            ->from('user')
            ->leftJoin('organization', 'organization.id = user.organization_id')
            ->where(['not in', 'user.organization_id', Yii::$app->params['main_organizations']])
            ->orderBy(['organization' => SORT_ASC, 'last_name' => SORT_ASC])
            ->limit(20);


        if (!is_null($q)) {
                $query->andWhere(['OR', ['like', 'first_name', $q], ['like', 'last_name', $q]]);
        } elseif ($id > 0) {
            $query->andWhere(['user.id', $id])->limit(1);
        }

        $command = $query->createCommand();
        $data = $command->queryAll();

        return ['results' => $this->sortOrganization( array_values($data) )];
    }

}
