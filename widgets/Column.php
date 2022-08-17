<?php

namespace app\widgets;

use Yii;
use Closure;
use yii\helpers\Html;
use yii\base\BaseObject;

class Column extends BaseObject
{
    
    public $header;
    public $format;
    public $grid;
    public $visible = true;
    public $label;
    public $filter;
    public $value;
    public $attribute;
    public $contentOptions = [];
    public $headerOptions = [];
    public $filterOptions = [];    
    public $filterInputOptions = [ 'class' => 'form-control' ];
    public $content;
    public $options = [];


    public function renderHeaderCell()
    {
        return Html::tag('div', $this->renderHeaderCellContent(), array_merge($this->options, $this->headerOptions));
    }
    
    protected function renderHeaderCellContent()
    {
        return trim($this->header) !== '' ? $this->header : $this->getHeaderCellLabel();
    }
    
    protected function getHeaderCellLabel()
    {
        return $this->grid->emptyCell;
    }
    
    public function renderDataCell($model, $key, $index)
    {
        if ($this->contentOptions instanceof Closure) {
            $options = call_user_func($this->contentOptions, $model, $key, $index, $this);
        } else {
            $options = $this->contentOptions;
        }

        return Html::tag('div', $this->renderDataCellContent($model, $key, $index), array_merge($this->options, $options));
    }
    
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->content !== null) {
            return call_user_func($this->content, $model, $key, $index, $this);
        }
        
        return $this->grid->emptyCell;
    }
    
    protected function renderFilterCellContent()
    {
        return $this->grid->emptyCell;
    }
        
    public function renderFilterCell()
    {
        return Html::tag('div', $this->renderFilterCellContent(), array_merge($this->options, $this->filterOptions));
    }
    
}