<?php

namespace Local\Core\Model\Data;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM;
use Local\Core\Inner\Client\Dadata\Exception\ArgumentException;


/**
 * <ul>
 * <li>ID</li>
 * <li>EXECUTE_BY</li>
 * <li>WORKER_CLASS_NAME</li>
 * <li>INPUT_DATA</li>
 * <li>ATTEMPTS_LEFT, попыток осталось, дефолт 10</li>
 * <li>STATUS</li>
 * <li>EXECUTE_AT</li>
 * <li>STATUS</li>
 * <li>IS_EXECUTE_NOW</li>
 * <li>LAST_EXECUTE_START</li>
 * <li>DATE_ADD</li>
 * <li>DATE_UPDATE</li>
 * </ul>
 * Class JobQueue
 * @package Local\Core\Model\Data
 * @see     JobQueueTable::STATUS_ENUM_NEW
 * @see     JobQueueTable::STATUS_ENUM_SUCCESS
 * @see     JobQueueTable::STATUS_ENUM_ERROR
 * @see     JobQueueTable::STATUS_ENUM_FAIL
 */
class JobQueueTable extends Entity\DataManager
{
    /**
     * Алгоритм вычисления хеша задания
     */
    const HASH_ALGO = 'md5';

    /**
     * New - Новый
     */
    const STATUS_ENUM_NEW = 'N';
    /**
     * Success - Успех
     */
    const STATUS_ENUM_SUCCESS = 'S';
    /**
     * Error - ошибка. Участвует в работе, если попытки остались
     */
    const STATUS_ENUM_ERROR = 'E';
    /**
     * Fail - окончательный провал. Более не исполняется
     */
    const STATUS_ENUM_FAIL = 'F';

    const EXECUTE_BY_DEFAULT = 'RUNNER';

    /**
     * @inheritdoc
     */
    public static function getTableName()
    {
        return 'a_model_data_job_queue';
    }

    /**
     * @inheritdoc
     */
    public static function getMap()
    {
        return array(
            new Orm\Fields\IntegerField(
                'ID', [
                    'primary' => true,
                    'autocomplete' => true,
                ]
            ),
            new Orm\Fields\StringField(
                'EXECUTE_BY', [
                    'primary' => true,
                    'default_value' => self::EXECUTE_BY_DEFAULT,
                ]
            ),
            new Orm\Fields\StringField(
                'WORKER_CLASS_NAME', ['required' => true]
            ),
            new Orm\Fields\TextField(
                'INPUT_DATA', [
                    'required' => true,
                    'serialized' => true,
                ]
            ),
            new Orm\Fields\StringField('HASH'),
            new Orm\Fields\IntegerField(
                'ATTEMPTS_LEFT', [
                    'required' => true,
                    'default_value' => 10,
                ]
            ),
            new Orm\Fields\EnumField(
                'STATUS', [
                    'values' => [
                        self::STATUS_ENUM_NEW,
                        self::STATUS_ENUM_SUCCESS,
                        self::STATUS_ENUM_ERROR,
                        self::STATUS_ENUM_FAIL,
                    ],
                    'default_value' => self::STATUS_ENUM_NEW,
                ]
            ),
            new Orm\Fields\DatetimeField(
                'EXECUTE_AT', [
                    'required' => true,
                ]
            ),
            new Orm\Fields\EnumField(
                'IS_EXECUTE_NOW', [
                    'values' => ['Y', 'N'],
                    'default_value' => 'N',
                ]
            ),
            new Orm\Fields\DatetimeField('LAST_EXECUTE_START'),
            new Orm\Fields\DatetimeField('DATE_ADD'),
            new Orm\Fields\DatetimeField('DATE_UPDATE'),
        );
    }

    public static function onBeforeAdd(Entity\Event $event)
    {
        $data = $event->getParameter("fields");

        $result = new \Bitrix\Main\Entity\EventResult();
        $result->unsetFields(['DATE_UPDATE', 'UPDATED_BY']);

        $result->modifyFields(
            [
                'HASH' => self::hash(
                    $data['WORKER_CLASS_NAME'],
                    $data['INPUT_DATA']
                ),
            ]
        );

        return $result;
    }

    public static function onBeforeUpdate(Entity\Event $event)
    {
        $primary = $event->getParameter("primary");
        $data = $event->getParameter("fields");

        $result = new \Bitrix\Main\Entity\EventResult();
        $result->unsetFields(['DATE_ADD', 'ADDED_BY']);

        $class = key_exists(
            'WORKER_CLASS_NAME',
            $data
        );
        $input = key_exists(
            'INPUT_DATA',
            $data
        );

        if( $class || $input )
        {
            $source = [];
            if( !$class || !$input )
            {
                $source = self::getById($primary)
                    ->fetch();
            }

            $result->modifyFields(
                [
                    'HASH' => self::hash(
                        ( $data['WORKER_CLASS_NAME'] ? : @$source['WORKER_CLASS_NAME'] ),
                        ( $data['INPUT_DATA'] ? : @$source['INPUT_DATA'] )
                    ),
                ]
            );
        }
        return $result;
    }

    /**
     * Получить хеш задания
     *
     * @param string $class      Имя класса воркера
     * @param array  $input_data Входящие параметры
     *
     * @return string
     * @throws ArgumentException
     */
    public static function hash(string $class, array $input_data = []): string
    {
        if( !( $class = trim($class) ) )
        {
            throw new ArgumentException('Не указан обязатльный параметр класс воркера');
        }

        $input_dump = self::dump($input_data);

        $str_to_hash = $class.'#'.$input_dump;

        return hash(
            self::HASH_ALGO,
            $str_to_hash
        );
    }

    /**
     * Получить дампа аргумента.
     *
     * @param $data
     *
     * @return string
     */
    protected static function dump($data): string
    {
        return \Local\Core\Assistant\Arrays::dump($data);
    }
}
