<?

namespace Local\Core\Model\Robofeed;

use Bitrix\Main\ORM\Event;
use \Bitrix\Main\ORM\Fields;
use Local\Core\Model\Data\StoreTable;

/**
 * Класс для логрования результатов импорта Robofeed XML
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>DATE_CREATE - Дата создания [31.03.2019 13:05:47] | Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [31.03.2019 13:05:47] |
 * Fields\DatetimeField</li><li>STORE_ID - ID магазина | Fields\IntegerField</li><li>ROBOFEED_VERSION - Версия Robofeed XML | Fields\IntegerField</li><li>ROBOFEED_DATE - Дата создания Robofeed XML |
 * Fields\DatetimeField</li><li>BEHAVIOR_IMPORT_ERROR - Поведение импорта при ошибке | Fields\EnumField<br/>&emsp;STOP_IMPORT => Не актуализировать данные<br/>&emsp;IMPORT_ONLY_VALID =>
 * Актуализировать только валидные<br/></li><li>ALERT_IF_XML_NOT_MODIFIED - Информировать о не изменившемся Robofeed XML? [Y] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N =>
 * Нет<br/></li><li>PRODUCT_TOTAL_COUNT - Кол-во товаров в Robofeed XML | Fields\IntegerField</li><li>PRODUCT_SUCCESS_IMPORT - Кол-во валидных товаров в Robofeed XML |
 * Fields\IntegerField</li><li>IMPORT_RESULT - Результат импорта | Fields\EnumField<br/>&emsp;SU => Успех<br/>&emsp;ER => Ошибка<br/>&emsp;NU => Robofeed XML не изменялся<br/></li><li>ERROR_TEXT -
 * Ошибка | Fields\TextField</li><li>STORE_DATA - \Local\Core\Model\Data\Store | Fields\Relations\Reference</li></ul>
 *
 * @package Local\Core\Model\Robofeed
 */
class ImportLogTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    const BEHAVIOR_IMPORT_ERROR_STOP_IMPORT = 'STOP_IMPORT';
    const BEHAVIOR_IMPORT_ERROR_IMPORT_ONLY_VALID = 'IMPORT_ONLY_VALID';
    const ALERT_IF_XML_NOT_MODIFIED_Y = 'Y';
    const ALERT_IF_XML_NOT_MODIFIED_N = 'N';

    public static function getTableName()
    {
        return 'a_model_robofeed_import_log';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
        'IMPORT_RESULT' => [
            'SU' => 'Успех',
            'ER' => 'Ошибка',
            'NU' => 'Robofeed XML не изменялся',
        ],
        'BEHAVIOR_IMPORT_ERROR' => [
            'STOP_IMPORT' => 'Не актуализировать данные',
            'IMPORT_ONLY_VALID' => 'Актуализировать только валидные',
        ],
        'ALERT_IF_XML_NOT_MODIFIED' => [
            'Y' => 'Да',
            'N' => 'Нет',
        ],
    ];

    public static function getMap()
    {
        return [
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID'
            ]),
            new Fields\DatetimeField('DATE_CREATE', [
                'title' => 'Дата создания',
                'required' => false,
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
            new Fields\IntegerField('STORE_ID', [
                'required' => true,
                'title' => 'ID магазина'
            ]),
            new Fields\IntegerField('ROBOFEED_VERSION', [
                'required' => false,
                'title' => 'Версия Robofeed XML'
            ]),
            new Fields\DatetimeField('ROBOFEED_DATE', [
                'required' => false,
                'title' => 'Дата создания Robofeed XML'
            ]),
            new Fields\EnumField('BEHAVIOR_IMPORT_ERROR', [
                'required' => false,
                'title' => 'Поведение импорта при ошибке',
                'values' => self::getEnumFieldValues('BEHAVIOR_IMPORT_ERROR'),
            ]),
            new Fields\EnumField('ALERT_IF_XML_NOT_MODIFIED', [
                'required' => false,
                'title' => 'Информировать о не изменившемся Robofeed XML?',
                'values' => self::getEnumFieldValues('ALERT_IF_XML_NOT_MODIFIED'),
                'default_value' => 'Y'
            ]),
            new Fields\IntegerField('PRODUCT_TOTAL_COUNT', [
                'required' => false,
                'title' => 'Кол-во товаров в Robofeed XML'
            ]),
            new Fields\IntegerField('PRODUCT_SUCCESS_IMPORT', [
                'required' => false,
                'title' => 'Кол-во валидных товаров в Robofeed XML'
            ]),
            new Fields\EnumField('IMPORT_RESULT', [
                'required' => false,
                'title' => 'Результат импорта',
                'values' => self::getEnumFieldValues('IMPORT_RESULT'),
                'default_value' => ''
            ]),
            new Fields\TextField('ERROR_TEXT', [
                'required' => false,
                'title' => 'Ошибка',
            ]),

            new Fields\Relations\Reference('STORE_DATA', StoreTable::class, \Bitrix\Main\ORM\Query\Join::on('this.STORE_ID', 'ref.ID'), [
                'title' => 'ORM: Магазин'
            ])
        ];
    }

    public static function onAfterAdd(Event $event)
    {
        $arFields = $event->getParameter('fields');
        if (!empty($arFields['STORE_ID'])) {
            $rs = self::getList([
                'filter' => ['STORE_ID' => $arFields['STORE_ID']],
                'select' => ['ID'],
                'order' => ['DATE_CREATE' => 'DESC']
            ]);

            $intMaxLastLogCount = \Bitrix\Main\Config\Configuration::getInstance()
                                      ->get('robofeed')['ImportLogTable']['max_last_log_count'] ?? 10;
            if ($rs->getSelectedRowsCount() > $intMaxLastLogCount) {
                $i = 0;
                while ($ar = $rs->fetch()) {
                    $i++;
                    if ($i > $intMaxLastLogCount) {
                        self::delete($ar['ID']);
                    }
                }
            }
        }

        \Local\Core\Model\Data\StoreTable::clearComponentsCache([
            'ID' => $arFields['STORE_ID']
        ]);
    }


    public static function OnBeforeUpdate(\Bitrix\Main\ORM\Event $event)
    {
        $result = new \Bitrix\Main\ORM\EventResult();
        $arModifiedFields = [];

        # Вызывается строго в конце
        self::_OnBeforeUpdateBase($event, $result, $arModifiedFields);

        return $result;
    }
}