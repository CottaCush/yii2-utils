<?php

namespace CottaCush\Yii2\Controller;

use Yii;
use yii\base\Action;
use yii\console\Controller;
use yii\db\Connection;
use yii\db\Exception;
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
     * @param Action $action
     * @return bool
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function beforeAction(Action $action): bool
    {
        if (is_null($this->db)) {
            $this->db = Yii::$app->db;
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
     * @throws Exception
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
    public static function getColumnByField($table, $field, $value, $returnColumn = 'id'): bool|string|null
    {
        return (new Query)->select($returnColumn)
            ->from($table)->filterWhere([$field => $value])
            ->scalar();
    }

    public function getRandomOne($table, $column, $conditions = []): bool|array
    {
        return (new Query)->select($column)
            ->from($table)->filterWhere($conditions)->orderBy('rand()')->one();
    }

    public function getAll($table, $columns, $conditions = []): array
    {
        return (new Query)->select($columns)
            ->from($table)->andFilterWhere($conditions)->all();
    }

    /**
     * @param $table
     * @param array $columns
     * @return int
     * @throws Exception
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function insert($table, array $columns): int
    {
        return $this->db->createCommand()->insert($table, $columns)->execute();
    }

    /**
     * @param $table
     * @param array $columns
     * @param array $rows
     * @return bool|int
     * @throws Exception
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function batchInsert($table, array $columns, array $rows): bool|int
    {
        if (empty($rows)) {
            return true;
        }

        return $this->db->createCommand()->batchInsert($table, $columns, $rows)->execute();
    }

    public function stdout($string): bool|int
    {
        parent::stdout($string . PHP_EOL);
    }
}
