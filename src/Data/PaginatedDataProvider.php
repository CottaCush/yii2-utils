<?php

namespace CottaCush\Yii2\Data;

use yii\data\ArrayDataProvider;

/**
 * Class PaginatedDataProvider
 * @package CottaCush\Yii2\Data
 * @author Kehinde Ladipo <ladipokenny@gmail.com>
 * @codeCoverageIgnore
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
