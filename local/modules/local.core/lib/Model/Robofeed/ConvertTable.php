<?

namespace Local\Core\Model\Robofeed;

use Bitrix\Main\ORM\Event;
use \Bitrix\Main\ORM\Fields;

/**
 * Класс ORM конвертера
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>ACTIVE - Активность [Y] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/></li><li>DATE_CREATE - Дата создания [30.03.2019 18:52:55] |
 * Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [30.03.2019 18:52:55] | Fields\DatetimeField</li><li>USER_ID - ID пользователя [1] | Fields\IntegerField</li><li>HANDLER -
 * Обработчик | Fields\EnumField<br/>&emsp;YML => Яндекс YML (.xml)<br/></li><li>STATUS - Статус работы [WA] | Fields\EnumField<br/>&emsp;WA => Ожидается конвертация<br/>&emsp;IN => В
 * процессе<br/>&emsp;SU => Успешно сконвертирован<br/>&emsp;ER => Не удалось сконвертировать<br/></li><li>ORIGINAL_FILE_ID - ID изначального файла | Fields\IntegerField</li><li>ORIGINAL_FILE_NAME -
 * Название файла | Fields\StringField</li><li>EXPORT_FILE_ID - ID готового файла | Fields\IntegerField</li><li>ERROR_MESSAGE - Сообщение об ошибке конвертации |
 * Fields\TextField</li><li>VALID_ERROR_MESSAGE - Сообщение об ошибке валидации | Fields\TextField</li></ul>
 *
 * @package Local\Core\Model\Data
 */
class ConvertTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_robofeed_convert';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
        'STATUS' => [
            'WA' => 'Ожидается конвертация',
            'IN' => 'В процессе',
            'SU' => 'Успешно сконвертирован',
            'ER' => 'Не удалось сконвертировать'
        ],
        'HANDLER' => [
            'YML' => 'Яндекс YML (.xml)'
        ]
    ];

    public static function getMap()
    {
        return [
            new Fields\IntegerField('ID', [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => 'ID'
                ]),
            new Fields\EnumField('ACTIVE', [
                    'title' => 'Активность',
                    'values' => self::getEnumFieldValues('ACTIVE'),
                    'default_value' => 'Y'
                ]),
            new Fields\DatetimeField('DATE_CREATE', [
                    'title' => 'Дата создания',
                    'default_value' => function ()
                        {
                            return new \Bitrix\Main\Type\DateTime();
                        }
                ]),
            new Fields\DatetimeField('DATE_MODIFIED', [
                    'title' => 'Дата последнего изменения',
                    'default_value' => function ()
                        {
                            return new \Bitrix\Main\Type\DateTime();
                        }
                ]),
            new Fields\IntegerField('USER_ID', [
                    'title' => 'ID пользователя',
                    'default_value' => function ()
                        {
                            return $GLOBALS['USER']->GetId();
                        }
                ]),
            new Fields\EnumField('HANDLER', [
                    'title' => 'Обработчик',
                    'required' => true,
                    'values' => self::getEnumFieldValues('HANDLER')
                ]),
            new Fields\EnumField('STATUS', [
                    'title' => 'Статус работы',
                    'values' => self::getEnumFieldValues('STATUS'),
                    'default_value' => 'WA'
                ]),
            new Fields\IntegerField('ORIGINAL_FILE_ID', [
                    'title' => 'ID изначального файла',
                ]),
            new Fields\StringField('ORIGINAL_FILE_NAME', ['title' => 'Название файла']),
            new Fields\IntegerField('EXPORT_FILE_ID', [
                    'title' => 'ID готового файла',
                ]),
            new Fields\TextField('ERROR_MESSAGE', [
                    'title' => 'Сообщение об ошибке конвертации',
                ]),
            new Fields\TextField('VALID_ERROR_MESSAGE', [
                    'title' => 'Сообщение об ошибке валидации',
                ]),
        ];
    }

    public static function onBeforeAdd(Event $event)
    {
        $result = new \Bitrix\Main\ORM\EventResult();

        $arFields = $event->getParameter('fields');
        if (empty($arFields['USER_ID'])) {
            $arFields['USER_ID'] = $GLOBALS['USER']->GetId();
        }

        $intNowInQueue = self::getList([
                'filter' => [
                    'USER_ID' => $arFields['USER_ID'],
                    'STATUS' => 'WA',
                    'IN'
                ],
                'select' => ['ID']
            ])
            ->getSelectedRowsCount();

        $intMaxInQueue = \Bitrix\Main\Config\Configuration::getInstance()
                             ->get('robofeed')['convert']['max_in_queue'] ?? 1;

        if ($intNowInQueue >= $intMaxInQueue) {
            $result->addError(new \Bitrix\Main\ORM\EntityError('Максимально доступное кол-во файлов в очереди на конвертацию - '.$intMaxInQueue
                                                               .'. Дождитесь окончания конвертации и повторите попытку.'));
            \Local\Core\Inner\BxModified\CFile::Delete($arFields['ORIGINAL_FILE_ID']);
        }

        return $result;
    }

    public static function onAfterAdd(Event $event)
    {
        $intId = $event->getParameter('primary')['ID'];
        if ($intId > 0) {
            $worker = new \Local\Core\Inner\JobQueue\Worker\RobofeedConvert(['ID' => $intId]);
            $dateTime = new \Bitrix\Main\Type\DateTime();
            \Local\Core\Inner\JobQueue\Job::addIfNotExist($worker, $dateTime, 1);
        }
    }

    /**
     * Метод возвращает объект подготовленный \Bitrix\Main\ORM\Query\Result
     *
     * @return \Bitrix\Main\ORM\Query\Result
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getOrmFiles()
    {
        return self::query()
            ->where(\Bitrix\Main\ORM\Query\Query::filter()
                ->logic('or')
                ->where([
                        ['ORIGINAL_FILE_ID', '!=', false],
                        ['EXPORT_FILE_ID', '!=', false]
                    ]))
            ->setSelect(['ORIGINAL_FILE_ID', 'EXPORT_FILE_ID'])
            ->exec();
    }
}