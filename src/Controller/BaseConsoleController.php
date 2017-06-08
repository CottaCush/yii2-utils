<?php

namespace CottaCush\Yii2\Controller;

use yii\console\Controller;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;

/**
 * Class BaseConsoleController
 * @package CottaCush\Yii2\Controller
 * @author Olawale Lawal <wale@cottacush.com>
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @codeCoverageIgnore
 */
class BaseConsoleController extends Controller
{
    /** @var  Connection */
    protected $db;

    /** @var  Transaction */
    protected $transaction;

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        if (is_null($this->db)) {
            $this->db = \Yii::$app->db;
        }
        return parent::beforeAction($action);
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function beginTransaction()
    {
        $this->transaction = $this->db->beginTransaction();
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function commitTransaction()
    {
        $this->transaction->commit();
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function rollbackTransaction()
    {
        $this->transaction->rollBack();
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $table
     * @param $field
     * @param $value
     * @param string $returnColumn
     * @return false|null|string
     */
    public static function getColumnByField($table, $field, $value, $returnColumn = 'id')
    {
        return (new Query)->select($returnColumn)
            ->from($table)->filterWhere([$field => $value])
            ->scalar();
    }

    public function getRandomOne($table, $column, $conditions = [])
    {
        return (new Query)->select($column)
            ->from($table)->filterWhere($conditions)->orderBy('rand()')->one();
    }

    public function getAll($table, $columns, $conditions = [])
    {
        return (new Query)->select($columns)
            ->from($table)->andFilterWhere($conditions)->all();
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $table
     * @param array $columns
     * @return int
     */
    public function insert($table, array $columns)
    {
        return $this->db->createCommand()->insert($table, $columns)->execute();
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $table
     * @param array $columns
     * @param array $rows
     * @return int
     */
    public function batchInsert($table, array $columns, array $rows)
    {
        if (empty($rows)) {
            return true;
        }

        return $this->db->createCommand()->batchInsert($table, $columns, $rows)->execute();
    }

    public function stdout($string)
    {
        parent::stdout($string . PHP_EOL);
    }
}
