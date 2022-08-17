<?php

namespace app\modules\ajax\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;

class OrganizationController extends Controller
{

    public function actionBranchList($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = new Query;
        $query->select('id, name as text')
            ->from('organization')
            ->where(['id' => Yii::$app->params['main_organizations']])
            ->orderBy(['text' => SORT_ASC])
            ->limit(20);


        if (!is_null($q)) {
                $query->andWhere(['like', 'name', $q]);
        } elseif ($id > 0) {
//            return ['results' => ['LOL']];
            $query->andWhere(['id', $id])->limit(1);
        }

        $command = $query->createCommand();
        $data = $command->queryAll();

        return ['results' => $data];
    }

    public function actionOrganizationList($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = new Query;
        $query->select('id, name as text')
            ->from('organization')
            ->where(['not in', 'id', Yii::$app->params['main_organizations']])
            ->orderBy(['text' => SORT_ASC])
            ->limit(20);


        if (!is_null($q)) {
                $query->andWhere(['like', 'name', $q]);
        } elseif ($id > 0) {
            $query->andWhere(['id', $id])->limit(1);
        }

        $command = $query->createCommand();
        $data = $command->queryAll();

        return ['results' => $data];
    }
}
