<?php

namespace app\modules\ajax\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use app\models\Permission;

class AssignController extends Controller
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
    
    public function actionStaffGroupList($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //SELECT `group`.id, `group`.name FROM permission JOIN `group` ON (`group`.id = permission.group_id) where permission.user_id = 4 AND permission.access & 1
        $query = new Query;
        $query->select('group.id, group.name')
            ->from('permission')
            ->leftJoin('group', 'group.id = permission.group_id')
            ->where(['group.organization_id' => Yii::$app->params['main_organizations']])
            ->andWhere(['AND', ['permission.user_id' => Yii::$app->user->id], ['&', 'permission.access', Permission::ACCESS_ASSIGN_GROUP]])
            ->orderBy(['group.name' => SORT_ASC])
            ->limit(20);


        if (!is_null($q)) {
                $query->andWhere(['like', 'group.name', $q]);
        } elseif ($id > 0) {
            $query->andWhere(['group.id', $id])->limit(1);
        }

        $command = $query->createCommand();
        $data = array_values($command->queryAll());
        foreach ($data as $row)
        {
            $out[] = [
                'id' => $row['id'],
                'text' => $row['name'],
            ];
        }

        return ['results' => $out];
    }

    public function actionStaffUserList($group, $q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // SELECT `user`.* FROM permission join `user` ON (`user`.`id` = `permission`.`user_id`) where permission.group_id = 2 AND `user`.id != 4
        if (!Yii::$app->user->identity->perm(
            $group,
            Permission::ACCESS_ASSIGN_USER))
        {
            return ['results' => [
                [
                    'text' => 'Недостаточно прав для выбора сотрудника',
                    'disabled' => true,
                ],
            ]];
        }

        $query = new Query;
        $query->select('user.id, user.first_name, user.last_name')
            ->from('permission')
            ->leftJoin('user', 'user.id = permission.user_id')
//            ->where(['not in', 'user.organization_id', Yii::$app->params['main_organizations']])
            ->where( ['AND',
                ['permission.group_id' => $group],
//                ['!=', 'user.id', Yii::$app->user->id],
                ['user.organization_id' => Yii::$app->params['main_organizations']]
            ])
            ->orderBy(['user.first_name' => SORT_ASC, 'user.last_name' => SORT_ASC])
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
