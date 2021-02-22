<?php

namespace CottaCush\Yii2\Model;

use CottaCush\Yii2\Date\DateUtils;
use CottaCush\Yii2\Text\Utils;
use Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class BaseModel
 * @property bool|mixed|string|null updated_at
 * @property bool|mixed|string|null created_at
 * @property mixed|null id
 * @property mixed|null is_active
 * @package app\models
 * @author Olawale Lawal <wale@cottacush.com>
 * @codeCoverageIgnore
 */
class BaseModel extends ActiveRecord
{
    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return bool
     */
    public function beforeValidate(): bool
    {
        if ($this->hasAttribute('updated_at')) {
            $this->updated_at = DateUtils::getMysqlNow();
        }

        if ($this->isNewRecord && $this->hasAttribute('created_at')) {
            $this->created_at = DateUtils::getMysqlNow();
        }

        return parent::beforeValidate();
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $query ActiveQuery
     * @param $offset
     * @param $limit
     * @return ActiveQuery
     */
    public static function addPaginationParameters($query, $offset, $limit): ActiveQuery
    {
        if (!is_null($offset)) {
            $query->offset($offset);
        }

        if (!is_null($limit)) {
            $query->limit($limit);
        }

        return $query;
    }

    /**
     * Get all model rows
     * @param null $offset
     * @param null $limit
     * @return array
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public static function getAll($offset = null, $limit = null): array
    {
        $query = self::find();
        self::addPaginationParameters($query, $offset, $limit);
        return $query->all();
    }

    /**
     * Get all model rows with the
     * @param array $relatedRecords
     * @param array $sort
     * @param null $limit
     * @return ActiveDataProvider
     * @throws Exception
     * @author Olawale Lawal <wale@cottacush.com>
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public static function getAllProvider($relatedRecords = [], $sort = [], $limit = null): ActiveDataProvider
    {
        $query = self::find()->with($relatedRecords)->orderBy($sort);
        return self::convertToProvider($query, [], $limit);
    }

    /**
     * @param $query
     * @param array $dataProviderSort
     * @param null $pageSize
     * @return ActiveDataProvider
     * @throws Exception
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public static function convertToProvider($query, $dataProviderSort = [], $pageSize = null): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => is_null($pageSize) ? self::getDefaultPageSize() : $pageSize
            ],
            'sort' => $dataProviderSort,
        ]);
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param null $countField
     * @return int|string
     */
    public static function getTotalCount($countField = null): int|string
    {
        $model = get_called_class();
        $model = new $model();
        if (is_null($countField)) {
            $countField = $model::primaryKey();
            if (is_array($countField) && $countField) {
                $countField = $countField[0];
            }
        }

        return self::find()->count($model::tableName() . '.' . $countField);
    }


    /**
     * Gets all active
     * @param array $orderBy
     * @param string $activeColumn
     * @param int $activeValue
     * @return array
     * @author Olawale Lawal <wale@cottacush.com>
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public static function getActive($orderBy = [], $activeColumn = 'is_active', $activeValue = 1): array
    {
        return self::findActive($orderBy, $activeColumn, $activeValue)->all();
    }

    /**
     * Gets all active
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param array $orderBy
     * @param string $activeColumn
     * @param int $activeValue
     * @return ActiveQuery
     */
    public static function findActive($orderBy = [], $activeColumn = 'is_active', $activeValue = 1): ActiveQuery
    {
        /** @var self $model */
        $model = get_called_class();
        return $model::find()
            ->where([$model::tableName() . '.' . $activeColumn => $activeValue])
            ->orderBy($orderBy);
    }


    /**
     * Returns the Model Id based on the field value
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $field
     * @param $value
     * @return self | null
     */
    public static function getIdByField($field, $value): ?BaseModel
    {
        $result = self::find()->where([$field => $value])->limit(1)->one();
        return ($result) ? $result->id : null;
    }

    /**
     * Returns model rows created between a date range
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $startDate
     * @param $endDate
     * @param string $createdAtColumn
     * @return array|ActiveQuery
     */
    public static function getByCreatedDateRange(
        $startDate,
        $endDate,
        $createdAtColumn = 'created_at'
    ): ActiveQuery|array {
        $model = get_called_class();
        $model = new $model;

        return self::find()
            ->andWhere(
                $model::tableName() . '.' . $createdAtColumn . ' BETWEEN :start_date AND :end_date',
                ['start_date' => $startDate, 'end_date' => $endDate]
            );
    }


    /**
     * Get hash for model id
     * @param $salt
     * @return string
     * @throws Exception
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function getIdHash($salt): string
    {
        return Utils::encodeId($this->id, $salt);
    }

    /**
     * @return mixed
     * @throws Exception
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public static function getDefaultPageSize(): mixed
    {
        return ArrayHelper::getValue(\Yii::$app->params, 'defaultPageSize', 20);
    }

    /**
     * Returns the first error in the model
     * @param null $attribute
     * @return string|null
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function getFirstError($attribute = null): ?string
    {
        if (!$this->errors) {
            return null;
        } elseif (is_null($attribute)) {
            $errors = $this->getErrors();
            reset($errors);
            $firstError = current($errors);
            $arrayKeys = array_keys($firstError);
            return $firstError[$arrayKeys[0]];
        }

        return parent::getFirstError($attribute);
    }

    /**
     * @return mixed
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function isActive(): mixed
    {
        if ($this->hasAttribute('is_active')) {
            return $this->is_active;
        }
        return true;
    }

    /**
     * Fetch dropdown data for model
     *
     * Usage
     *<code>
     *  // with default
     *  FormCategory::getDropdownMap('key', 'name', ['' => 'Select Category'])
     *
     *  // without default
     *  FormCategory::getDropdownMap('key', 'name')
     *</code>
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $keyAttribute
     * @param $valueAttribute
     * @param array $default
     * an array map of the value to the labels
     * @return array
     */
    public static function getDropdownMap($keyAttribute, $valueAttribute, array $default = []): array
    {
        $map = ArrayHelper::map(self::getActive(), $keyAttribute, $valueAttribute);
        if ($default) {
            $map = array_merge($default, $map);
        }

        return $map;
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $table
     * @param $columns
     * @param $rows
     * @return int
     * @throws \yii\db\Exception
     */
    public static function batchInsert($table, $columns, $rows): int
    {
        $db = self::getDb();

        if (is_null($table)) {
            /** @var BaseModel $model */
            $model = get_called_class();
            $table = $model::tableName();
        }

        return $db->createCommand()->batchInsert($table, $columns, $rows)->execute();
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $id
     * @param $relatedRecords
     * @param string $column
     * @return array|BaseModel|null|ActiveRecord
     */
    public static function fetchWithRelatedRecords(
        $id,
        $relatedRecords,
        $column = 'id'
    ): BaseModel|array|ActiveRecord|null {
        /** @var BaseModel $model */
        $model = get_called_class();
        $model = $model::find()->where([$model::tableName() . '.' . $column => $id])
            ->joinWith($relatedRecords)->limit(1)->one();

        return $model;
    }
}
