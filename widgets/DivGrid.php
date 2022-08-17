<?php

namespace app\widgets;

use Yii;
use Closure;
use yii\widgets\BaseListView;
use yii\base\InvalidConfigException;
use yii\i18n\Formatter;
use app\widgets\DivGridAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use app\widgets\DataColumn;

class DivGrid extends BaseListView
{
    public $formatter;
    public $columns = [];
    public $filterModel;
    public $emptyCell = '&nbsp;';
    public $showHeader = true;
    public $tableOptions = ['class' => 'items'];
    public $rowOptions = ['class' => 'item'];
    public $filterRowOptions = ['class' => 'item filter'];
    public $dataColumnClass;
//    public $searchProvider;
    public $headerRowOptions = [ 'class' => 'item item-header' ];

    public function init()
    {
        parent::init();
        if ($this->formatter === null) {
            $this->formatter = Yii::$app->getFormatter();
        } elseif (is_array($this->formatter)) {
            $this->formatter = Yii::createObject($this->formatter);
        }
        if (!$this->formatter instanceof Formatter) {
            throw new InvalidConfigException('The "formatter" property must be either a Format object or a configuration array.');
        }
        if (!isset($this->filterRowOptions['id'])) {
            $this->filterRowOptions['id'] = $this->options['id'] . '-filters';
        }

        $this->initColumns();
    }   

    /**
     * Runs the widget.
     */
    public function run()
    {
        $id = $this->options['id'];
        $options = Json::htmlEncode($this->getClientOptions());
        $view = $this->getView();
        DivGridAsset::register($view);
        $view->registerJs("jQuery('#$id').yiiDivGrid($options);");
        parent::run();
    }
    
    public function renderItems()
    {        
        $tableHeader = $this->showHeader ? $this->renderTableHeader() : false;
        $tableBody = $this->renderTableBody();

        $content = array_filter([
            $tableHeader,
            $tableBody,
        ]);

        return Html::tag('div', implode("\n", $content), $this->tableOptions);
    }
    
    
    protected function initColumns()
    {
/*        if (empty($this->columns)) {
            $this->guessColumns();
        }*/
        foreach ($this->columns as $i => $column) {
            if (is_string($column)) {
                $column = $this->createDataColumn($column);
            } else {
                $column = Yii::createObject(array_merge([
                    'class' => $this->dataColumnClass ?: DataColumn::className(),
                    'grid' => $this,
                ], $column));
            }
            if (!$column->visible) {
                unset($this->columns[$i]);
                continue;
            }
            $this->columns[$i] = $column;
        }
    }
    
    protected function createDataColumn($text)
    {
        if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
            throw new InvalidConfigException('The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"');
        }

        return Yii::createObject([
            'class' => $this->dataColumnClass ?: DataColumn::className(),
            'grid' => $this,
            'attribute' => $matches[1],
            'format' => $matches[3] ?: 'text',
            'label' => $matches[5] ?: null,
        ]);
    }
    
    public function renderTableBody()
    {
        $models = array_values($this->dataProvider->getModels());
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach ($models as $index => $model) {
            $key = $keys[$index];
/*            if ($this->beforeRow !== null) {
                $row = call_user_func($this->beforeRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
*/
            $rows[] = $this->renderTableRow($model, $key, $index);
/*
            if ($this->afterRow !== null) {
                $row = call_user_func($this->afterRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }*/
        }

        if (empty($rows) && $this->emptyText !== false) {
//            $colspan = count($this->columns);

            return Html::tag('div', $this->renderEmpty());
        }

        return implode("\n", $rows);
    }
    
    protected function getClientOptions()
    {
        $filterUrl = isset($this->filterUrl) ? $this->filterUrl : Yii::$app->request->url;
        $id = $this->filterRowOptions['id'];
        $filterSelector = "#$id input, #$id select";
        if (isset($this->filterSelector)) {
            $filterSelector .= ', ' . $this->filterSelector;
        }

        return [
            'filterUrl' => Url::to($filterUrl),
            'filterSelector' => $filterSelector,
        ];
    }
    
    public function renderTableRow($model, $key, $index)
    {
        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }
        if ($this->rowOptions instanceof Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = is_array($key) ? json_encode($key) : (string) $key;

//        var_dump($cells); die();
        return Html::tag('div', implode('', $cells), $options);
    }
    
    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('div', implode('', $cells), $this->headerRowOptions);
        
        if (isset($this->filterModel))
        {
            $content .= $this->renderFilters();
        }
/*        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->filterPosition === self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }*/

        return $content;
    }
    
    public function renderFilters()
    {
        if ($this->filterModel !== null) {
            $cells = [];
            foreach ($this->columns as $column) {
                /* @var $column Column */
                $cells[] = $column->renderFilterCell();
            }

            return Html::tag('div', implode('', $cells), $this->filterRowOptions);
        }

        return '';
    }

}
