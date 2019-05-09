<?php

namespace Local\Core\Model\Data;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM;
use Local\Core\Inner\Client\Dadata\Exception\ArgumentException;


/**
 * <ul><li>ID - ID | Fields\IntegerField</li><li>DATE_CREATE - Дата создания [2019-05-09 20:24:02] | Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [2019-05-09 20:24:02] | Fields\DatetimeField</li><li>EXECUTE_BY - EXECUTE_BY [RUNNER] | Fields\StringField</li><li>WORKER_CLASS_NAME - WORKER_CLASS_NAME | Fields\StringField</li><li>INPUT_DATA - INPUT_DATA | Fields\TextField</li><li>HASH - HASH | Fields\StringField</li><li>ATTEMPTS_LEFT - ATTEMPTS_LEFT [10] | Fields\IntegerField</li><li>STATUS - STATUS [N] | Fields\EnumField<br/>&emsp;N<br/>&emsp;S<br/>&emsp;E<br/>&emsp;F<br/></li><li>EXECUTE_AT - EXECUTE_AT | Fields\DatetimeField</li><li>IS_EXECUTE_NOW - IS_EXECUTE_NOW [N] | Fields\EnumField<br/>&emsp;Y<br/>&emsp;N<br/></li><li>LAST_EXECUTE_START - LAST_EXECUTE_START | Fields\DatetimeField</li></ul>
 *
 * @package Local\Core\Model\Data
 * @see     JobQueueTable::STATUS_ENUM_NEW
 * @see     JobQueueTable::STATUS_ENUM_SUCCESS
 * @see     JobQueueTable::STATUS_ENUM_ERROR
 * @see     JobQueueTable::STATUS_ENUM_FAIL
 */
class JobQueueTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
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
            new Orm\Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new Orm\Fields\DatetimeField('DATE_CREATE', [
                'title' => 'Дата создания',
                'default_value' => function ()
                    {
                        return new \Bitrix\Main\Type\DateTime();
                    }
            ]),
            new Orm\Fields\DatetimeField('DATE_MODIFIED', [
                'title' => 'Дата последнего изменения',
                'default_value' => function ()
                    {
                        return new \Bitrix\Main\Type\DateTime();
                    }
            ]),
            new Orm\Fields\StringField('EXECUTE_BY', [
                'primary' => true,
                'default_value' => self::EXECUTE_BY_DEFAULT,
            ]),
            new Orm\Fields\StringField('WORKER_CLASS_NAME', ['required' => true]),
            new Orm\Fields\TextField('INPUT_DATA', [
                'required' => true,
                'serialized' => true,
            ]),
            new Orm\Fields\StringField('HASH'),
            new Orm\Fields\IntegerField('ATTEMPTS_LEFT', [
                'required' => true,
                'default_value' => 10,
            ]),
            new Orm\Fields\EnumField('STATUS', [
                'values' => [
                    self::STATUS_ENUM_NEW,
                    self::STATUS_ENUM_SUCCESS,
                    self::STATUS_ENUM_ERROR,
                    self::STATUS_ENUM_FAIL,
                ],
                'default_value' => self::STATUS_ENUM_NEW,
            ]),
            new Orm\Fields\DatetimeField('EXECUTE_AT', [
                'required' => true,
            ]),
            new Orm\Fields\EnumField('IS_EXECUTE_NOW', [
                'values' => ['Y', 'N'],
                'default_value' => 'N',
            ]),
            new Orm\Fields\DatetimeField('LAST_EXECUTE_START'),
        );
    }

    public static function onBeforeAdd($event)
    {
        $data = $event->getParameter("fields");

        $result = new \Bitrix\Main\Entity\EventResult();

        $arModifiedFields = [
            'HASH' => self::hash($data['WORKER_CLASS_NAME'], $data['INPUT_DATA']),
        ];

        # Вызывается строго в конце
        self::_OnBeforeUpdateBase($event, $result, $arModifiedFields);

        return $result;
    }

    public static function onBeforeUpdate($event)
    {
        $primary = $event->getParameter("primary");
        $data = $event->getParameter("fields");
        $arModifiedFields = [];

        $result = new \Bitrix\Main\Entity\EventResult();
        $result->unsetFields(['ADDED_BY']);

        $class = key_exists('WORKER_CLASS_NAME', $data);
        $input = key_exists('INPUT_DATA', $data);

        if ($class || $input) {
            $source = [];
            if (!$class || !$input) {
                $source = self::getById($primary)
                    ->fetch();
            }

            $arModifiedFields = [
                'HASH' => self::hash(($data['WORKER_CLASS_NAME'] ? : @$source['WORKER_CLASS_NAME']), ($data['INPUT_DATA'] ? : @$source['INPUT_DATA'])),
            ];
        }

        # Вызывается строго в конце
        self::_OnBeforeUpdateBase($event, $result, $arModifiedFields);

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
        if (!($class = trim($class))) {
            throw new ArgumentException('Не указан обязатльный параметр класс воркера');
        }

        $input_dump = self::dump($input_data);

        $str_to_hash = $class.'#'.$input_dump;

        return hash(self::HASH_ALGO, $str_to_hash);
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
