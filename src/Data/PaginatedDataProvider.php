<?php

namespace app\libs;

use yii\data\ArrayDataProvider;

/**
 * Class PaginatedDataProvider
 * @package app\libs
 * @author Kehinde Ladipo <ladipokenny@gmail.com>
 */
class PaginatedDataProvider extends ArrayDataProvider
{
    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        if (($models = $this->allModels) === null) {
            return [];
        }

        if (($sort = $this->getSort()) !== false) {
            $models = $this->sortModels($models, $sort);
        }

        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();

            if ($pagination->getPageSize() > 0) {
                $this->refresh();
                $this->totalCount = $pagination->totalCount;
            }
        }

        return $models;
    }
}
