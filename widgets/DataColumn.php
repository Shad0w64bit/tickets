<?php

namespace app\widgets;

use Yii;
use yii\helpers\Inflector;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQueryInterface;
use yii\data\ArrayDataProvider;
use app\widgets\Column;

class DataColumn extends Column
{
    
    public $format = 'text';
    public $encodeLabel = true;
    public $sortLinkOptions = [];
    public $enableSorting = true;
    
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->content === null) {
            return $this->grid->formatter->format($this->getDataCellValue($model, $key, $index), $this->format);
        }

        return parent::renderDataCellContent($model, $key, $index);
    }
    
    public function getDataCellValue($model, $key, $index)
    {
        if ($this->value !== null) {
            if (is_string($this->value)) {
                return ArrayHelper::getValue($model, $this->value);
            }
            return call_user_func($this->value, $model, $key, $index, $this);
        } elseif ($this->attribute !== null) {
            return ArrayHelper::getValue($model, $this->attribute);
        }

        return null;
    }
    
    protected function renderHeaderCellContent()
    {
        if ($this->header !== null || $this->label === null && $this->attribute === null) {
            return parent::renderHeaderCellContent();
        }

        $label = $this->getHeaderCellLabel();
        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }

        if ($this->attribute !== null && $this->enableSorting &&
            ($sort = $this->grid->dataProvider->getSort()) !== false && $sort->hasAttribute($this->attribute)) {
            return $sort->link($this->attribute, array_merge($this->sortLinkOptions, ['label' => $label]));
        }

        return $label;
    }
    
    protected function getHeaderCellLabel()
    {
        $provider = $this->grid->dataProvider;

        if ($this->label === null) {
            if ($provider instanceof ActiveDataProvider && $provider->query instanceof ActiveQueryInterface) {
                /* @var $modelClass Model */
                $modelClass = $provider->query->modelClass;
                $model = $modelClass::instance();
                $label = $model->getAttributeLabel($this->attribute);
            } elseif ($provider instanceof ArrayDataProvider && $provider->modelClass !== null) {
                /* @var $modelClass Model */
                $modelClass = $provider->modelClass;
                $model = $modelClass::instance();
                $label = $model->getAttributeLabel($this->attribute);
            } elseif ($this->grid->filterModel !== null && $this->grid->filterModel instanceof Model) {
                $label = $this->grid->filterModel->getAttributeLabel($this->attribute);
            } else {
                $models = $provider->getModels();
                if (($model = reset($models)) instanceof Model) {
                    /* @var $model Model */
                    $label = $model->getAttributeLabel($this->attribute);
                } else {
                    $label = Inflector::camel2words($this->attribute);
                }
            }
        } else {
            $label = $this->label;
        }

        return $label;
    }
    
    protected function renderFilterCellContent()
    {
        if (is_string($this->filter)) {
            return $this->filter;
        }

        $model = $this->grid->filterModel;

        if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
            if ($model->hasErrors($this->attribute)) {
                Html::addCssClass($this->filterOptions, 'has-error');
                $error = ' ' . Html::error($model, $this->attribute, $this->grid->filterErrorOptions);
            } else {
                $error = '';
            }
            if (is_array($this->filter)) {
                $options = array_merge(['prompt' => ''], $this->filterInputOptions);
                return Html::activeDropDownList($model, $this->attribute, $this->filter, $options) . $error;
            } elseif ($this->format === 'boolean') {
                $options = array_merge(['prompt' => ''], $this->filterInputOptions);
                return Html::activeDropDownList($model, $this->attribute, [
                    1 => $this->grid->formatter->booleanFormat[1],
                    0 => $this->grid->formatter->booleanFormat[0],
                ], $options) . $error;
            }

            return Html::activeTextInput($model, $this->attribute, $this->filterInputOptions) . $error;
        }

        return parent::renderFilterCellContent();
    }
    
}