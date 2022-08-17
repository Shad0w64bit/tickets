<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Ticket;
use app\widgets\DivGrid;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TicketSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заявки';
$this->params['breadcrumbs'][] = $this->title;

$accessDelete = true;//Yii::$app->user->identity->perm($model->group_id, app\models\Permission::ACCESS_DELETE);
?>
<div class="ticket-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <div class="col-md-12">
        <?= Html::a('Создать заявку', ['create'], ['class' => 'btn btn-success']) ?>
        <?php if ($accessDelete): ?>        
        <?= Html::button('Удалить', ['class' => 'btn btn-danger pull-right', 
            'onclick' => new yii\web\JsExpression('                
                var keys = $("#ticket-grid").yiiDivGrid("getSelectedRows");
                if (keys.length == 0)
                {
                    alert("Выделите хотя бы одну заявку.");
                } else if (confirm("Удалить " + keys.join(", ") + " заявки?"))
                {
                    $.ajax({
                        url: "'. \yii\helpers\Url::toRoute(['delete-multiple']) .'",
                        type: "POST",
                        data: {keys: keys},
                        success: function(data){
                            if (data) {
                                location.reload();
                            } else {
                                alert("Ошибка при удалении.");
                            }
                        },
                        error: function(jqXHR, errMsg)
                        {
                            alert(errMsg);
                        }
                    });
                }
            ')]) ?>
        <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="mobile-view btn-group" role="group" aria-label="" >
            <?= Html::button('Сортировка', ['class' => 'btn btn-default',
                'onclick' => new yii\web\JsExpression('
                   $("#ticket-grid .sort").toggle();
                ')]) ?>

            <?= Html::button('Фильтры', ['class' => 'btn btn-default',
                'onclick' => new yii\web\JsExpression('
                   $("#ticket-grid .filter").toggle();
                ')]) ?>
        </div>
        </div>
    </div>
    
<?=  DivGrid::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'ticket-grid',
        'showOnEmpty' => true,
        'headerRowOptions' => [
            'class' => 'sort item-header',
        ],
        'filterRowOptions' => [
            'class' => 'item filter',
        ],
        'columns' => [      
            [
                'class' => 'app\widgets\CheckboxColumn',                    
                'options' => [ 'class' => 'check' ],                    
                'checkboxOptions' => function($model){
                    return ['value' => $model->id];
                }                
            ],            
            [
                'attribute' => 'id',
                'format' => 'html',
                'options' => ['class' => 'id'],
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => '№ Заявки',
                ],
                'value' => function ($model){
                    return Html::a($model->getId(), ['view', 'id'=>$model->id], ['style'=>'font-weight: bold;']);
                }
            ],
            [
                'attribute' => 'title',
                'options' => ['class' => 'title'],
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'Тема',
                ],
            ],            
            [
                'attribute' => 'organization_id',
                'label' => 'Организация',
//                'value' => 'organization.name',
                'options' => ['class' => 'organization'],
                'format' => 'html',
                'filter' => kartik\select2\Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'organization_id',
                    'initValueText' =>(isset($searchModel->organization_id)) 
                        ? app\models\Organization::findOne($searchModel->organization_id)->name
                        : '',
                    'options' => ['placeholder' => 'Организация'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => 'ru',
                        'ajax' => [
                            'url' => Url::to(['/ajax/organization/organization-list']),
                            'dataType' => 'json',
/*                            'data' => new yii\web\JsExpression(
                                'function(params){ return {q:params.term}; }'
                            ),*/
                        ],
                    ],
                ]),
                'value' => function($model){
                    $label = '<span class="mobile-view"><b>Организация: </b></span>';
                    return $label . Html::encode($model->organization->name);
                },
            ],        
                    
            [
                'attribute' => 'updated_at',
//                'format' => 'datetime',
                'format' => 'html',
                'options' => ['class' => 'updated'],
                'filter' => false,
                'value' => function ($model)
                {
                    $label = '<span class="mobile-view"><b>Обновлено: </b></span>';
                    return $label . Yii::$app->formatter->asDatetime($model->updated_at);
                }
            ],
            [                
                'attribute' => 'assign_to',
                'label' => 'Назначено',
                'options' => ['class' => 'assign'],
                'format' => 'html',
                'value' => function ($model)
                {
                    $label = '<span class="mobile-view"><b>Назначено: </b></span>';
                    return $label . ((isset($model->assign_to)) ? $model->assigned->fullname : 'Не назначено');
                }
            ],
            [
                'attribute'=>'status',
                'options' => ['class' => 'status'],
                'filter'=>[                    
                    Ticket::TICKET_STATUS_OPEN  => 'Открыто',
                    Ticket::TICKET_STATUS_CLOSE  => 'Закрыто',
                ],
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => 'Не указано',
                ],
                'format' => 'html',
                'value' => function ($model) {                    
                    switch ($model->status)
                    {
                        case Ticket::TICKET_STATUS_CLOSE:
                            return 'Закрыто';
                    }
                    return 'Открыто';
                }
            ],
            [
                'options' => ['class' => 'link'],
                'format' => 'html',
                'value' => function ($model){
                    return Html::a('<span class="desktop-view glyphicon glyphicon-eye-open"></span><span class="mobile-view">Просмотр</span>',
                        ['view', 'id' => $model->id]
                    );
                },
            ],
        ],
    ]);

    ?>
</div>
    
<?php

$css = <<<CSS
@media screen and (max-width: 759px) {

#ticket-grid .sort,
#ticket-grid .filter {
    display: none;
}

.item > div {
    display: block;
    padding: .2em .5em;
    border: none;
}
        
.item {
    display: block;
    position: relative;
    border-radius: 4px;
    padding: .6em .2em;
}        

.sort > .action-column,
.sort > .check {
    display: none;
}

.sort > div {
    display: block;
    position: relative;
}    

item > .title {
    padding: .5em;
}   

.check {
    position: absolute;            
}
.check input[type="checkbox"] {
    margin-right: -24px;
}

.status {
    position: absolute;
    font-weight: bold;
    right: .4em;
    top: .6em;
}

.item > .id {
    padding-left: 28px;
    font-weight: bold;
}


.link a {		
    border: 1px solid gray;
    text-align: center;
    margin: .4em .5em;
    line-height: 2em;	
    text-transform: uppercase;
}

.link a:hover {
    background-color: orange;
}

.link a {
    display: block;
    border-radius: 3px;
}

.filter .id,
.filter .status {
    position: initial;
    padding: 0 .5em;
}

.sort > .link,
.filter > .updated,
.filter > .link,
.filter > .assign {
    display: none;
}
	
.title {
    padding: .7em .5em;
}       
        
.sort .status {
    right: 0;
    top: 0;
}
        
}    
CSS;

$this->registerCss($css, ['depends' => ['app\widgets\DivGridAsset']]);