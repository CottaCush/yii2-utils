<?php

namespace CottaCush\Yii2\Model;

use CottaCush\Yii2\Date\DateUtils;
use CottaCush\Yii2\Text\Utils;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class BaseModel
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
    public function beforeValidate()
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
    public static function addPaginationParameters($query, $offset, $limit)
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
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param null $offset
     * @param null $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAll($offset = null, $limit = null)
    {
        $query = self::find();
        self::addPaginationParameters($query, $offset, $limit);
        return $query->all();
    }

    /**
     * Get all model rows with the
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     * @param array $relatedRecords
     * @param array|null $sort
     * @param null $limit
     * @return ActiveDataProvider
     */
    public static function getAllProvider($relatedRecords = [], $sort = [], $limit = null)
    {
        $query = self::find()->with($relatedRecords)->orderBy($sort);
        return self::convertToProvider($query, [], $limit);
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $query
     * @param array $dataProviderSort
     * @param null $pageSize
     * @return ActiveDataProvider
     */
    public static function convertToProvider($query, $dataProviderSort = [], $pageSize = null)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => is_null($pageSize) ? self::getDefaultPageSize() : $pageSize
            ],
            'sort' => $dataProviderSort,
        ]);

        return $dataProvider;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param null $countField
     * @return int|string
     */
    public static function getTotalCount($countField = null)
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
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     * @param array $orderBy
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getActive($orderBy = [])
    {
        $model = get_called_class();
        $model = new $model;
        return self::find()
            ->asArray()
            ->orderBy($orderBy)
            ->all();
    }

    /**
     * Gets all active
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findActive()
    {
        $model = get_called_class();
        $model = new $model;
        return self::find()
            ->all();
    }


    /**
     * Returns the Model Id based on the field value
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $field
     * @param $value
     * @return self | null
     */
    public static function getIdByField($field, $value)
    {
        $result = self::find()->where([$field => $value])->limit(1)->one();
        return ($result) ? $result->id : null;
    }

    /**
     * Returns model rows created between a date range
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $startDate
     * @param $endDate
     * @return array|ActiveQuery
     */
    public static function getByCreatedDateRange($startDate, $endDate)
    {
        $model = get_called_class();
        $model = new $model;

        return self::find()
            ->andWhere($model::tableName() . '.created_at BETWEEN :start_date AND :end_date')
            ->params(['start_date' => $startDate, 'end_date' => $endDate]);
    }


    /**
     * Get hash for model id
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $salt
     * @return string
     */
    public function getIdHash($salt)
    {
        return Utils::encodeId($this->id, $salt);
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @return mixed
     */
    public static function getDefaultPageSize()
    {
        return ArrayHelper::getValue(\Yii::$app->params, 'defaultPageSize', 20);
    }

    /**
     * Returns the first error in the model
     * @author Olawale Lawal <wale@cottacush.com>
     * @param string $attribute
     * @return string
     */
    public function getFirstError($attribute = null)
    {
        if (!$this->errors) {
            return null;
        } elseif (is_null($attribute)) {
            $errors = $this->getErrors();
            reset($errors);
            $firstError = current($errors);
            $arrayKeys = array_keys($firstError);
            $error = $firstError[$arrayKeys[0]];
            return $error;
        }

        return parent::getFirstError($attribute);
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @return bool|mixed
     */
    public function isActive()
    {
        if ($this->hasAttribute('is_active')) {
            return $this->is_active;
        }
        return true;
    }
}
