<?php

namespace CottaCush\Yii2\Constants;

class Messages
{
    const RECORD_NOT_FOUND = 'Record not found';

    const TASK_CREATE = 'created';
    const TASK_UPDATE = 'updated';
    const TASK_DELETE = 'deleted';
    const TASK_ADD = 'added';
    const TASK_ACTIVATE = 'activated';
    const TASK_DEACTIVATE = 'deactivated';
    const TASK_SAVE = 'saved';

    const UNEXPECTED_ERROR_OCCURRED = 'An unexpected error occurred';
    const RECORD_USED_ALREADY = 'This record cannot be deleted as it has been used elsewhere';
    const RECORD_EXISTS_ALREADY = 'This record exists already';

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $entity
     * @param string $task
     * @return string
     */
    public static function getSuccessMessage($entity, $task = self::TASK_CREATE)
    {
        return sprintf('%s %s successfully', $entity, $task);
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $entity
     * @param string $creationTask
     * @return string
     */
    public static function getEmptyStateMessage($entity, $creationTask = self::TASK_CREATE)
    {
        return sprintf('No %s has been %s', $entity, $creationTask);
    }

    /**
     * @author Kehinde Ladipo <ladipokenny@gmail.com>
     * @param $entity
     * @param string $action
     * @return string
     */
    public static function getWarningMessage($entity, $action)
    {
        return sprintf('Are you sure you want to %s this %s?', $action, $entity);
    }

    public static function getIntegrityViolationMsg($entity, $task = self::TASK_DELETE)
    {
        return sprintf('This %s is in use, hence cannot be %s', $entity, $task);
    }
}
