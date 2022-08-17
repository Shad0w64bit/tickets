<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Permission;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PermissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Права доступа';
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['permission', 'id' => $user->id]];
$this->params['breadcrumbs'][] = ['label' => 'Сотрудники', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="permission-index">

    <h1><?= Html::encode($this->title . ' ' . $user->fullname) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php /*= DetailView::widget([
    'model' => $user,
    'attributes' => [
        [
            'label' => 'Организация',
            'value' => function ($model)
            {
                return $model->organization->name;
            }
        ],
        'email:email',
        'fullname'
    ],
    ]) */?>    
    
    <p> 
        <?php
        Modal::begin([
            'header' => '<h4>Добавить разрешения</h4>',
            'toggleButton' => [
                'label' => 'Добавить',
                'class' => 'btn btn-success',
            ],
            'options' => [
                'id' => 'permission-add',
                'tabindex' => false,
            ],
        ]); 
        ?>          

        <?php $form = ActiveForm::begin(['id' => 'permission-add-form',]);// 'action' => Url::to(['change-title', 'id' => $model->id]), 'method' => 'post']); ?>
        
            
        <?= $form->field($new, 'group_id')->dropDownList(
            ArrayHelper::map($availableGroups, 'id', 'name')
        )->label('Группа') ?>
        
        <?= $form->field($new, 'user_id')->hiddenInput()->label(false) ?>

        <?= $form->field($new, 'permission')->checkboxList([
/*            Permission::ACCESS_ASSIGN_GROUP => 'Назначение группе',
            Permission::ACCESS_ASSIGN_USER => 'Назначение сотруднику', */
            Permission::ACCESS_NOTIFY => 'Получать уведомления',
            Permission::ACCESS_CREATE => 'Создание', 
            Permission::ACCESS_READ => 'Чтение',
            Permission::ACCESS_WRITE => 'Запись', 
            Permission::ACCESS_DELETE => 'Удаление',
            Permission::ACCESS_ONLY_THEIR => 'Только свои заявки',
        ]) ?>

        
        <div class="form-group">
            <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary']); ?>
            <?= Html::button('Отмена', ['class'=>'btn btn-default', 'data-dismiss'=>'modal']); ?>
        </div>

        <?php ActiveForm::end();        

        Modal::end();
    ?>
    
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'group_id',
            [
                'attribute' => 'group_name',
                'label' => 'Группа',
                'value' => 'group.name'
            ],
//            'user_id',
/*            [
                'attribute' => 'user_email',
                'label' => 'Пользователь',
                'value' => 'user.email'
            ],*/
            [
                'attribute' => 'access',
                'label' => 'Доступ',
                'value' => function ($model) {
                    return $model->getTextPermission();
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model, $key, $index)
                {
                    if ($action === 'update') {
                        return Url::toRoute(['permission-update', 'id' => $model->id]);
                    } elseif ($action === 'delete') {
                        return Url::toRoute(['permission-delete', 'id' => $model->id]);
                    }
                }
            ],
        ],
    ]); ?>
</div>

<?php
$this->registerJs('
    $("#permission-add").on("beforeSubmit", function(){
    var data = $("#permission-add-form").serialize();
    $.ajax({
        url: "' . Url::to(['permission-add', 'id' => $model->id]) . '",
        type: "POST",
        data: data,
        success: function(r){
            if (r.error != null)
            {
                alert(r.error);
            } else {
                location.reload();
            }
        },
        error: function(){
            alert("error");
        }
    });
    return false;
});' 
);
