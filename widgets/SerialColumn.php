<?php

namespace app\widgets;

class SerialColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public $header = '#';


    /**
     * {@inheritdoc}
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $pagination = $this->grid->dataProvider->getPagination();
        if ($pagination !== false) {
            return $pagination->getOffset() + $index + 1;
        }

        return $index + 1;
    }
}
