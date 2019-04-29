<?

namespace Local\Core\Model\Data;

use \Bitrix\Main\ORM\Fields;
use Bitrix\Seo\LeadAds\Field;

/**
 * ORM лога создания экспортных файлов
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>DATE_CREATE - Дата создания [2019-04-29 12:41:10] | Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [2019-04-29 12:41:10] | Fields\DatetimeField</li><li>STORE_ID - ID магазина | Fields\IntegerField</li><li>TP_ID - ID торговой площадки | Fields\IntegerField</li><li>RESULT - Результат | Fields\EnumField<br/>&emsp;SU => Успех<br/>&emsp;ER => Ошибка<br/></li><li>PRODUCTS_TOTAL - Всего отфильтрованных товаров | Fields\IntegerField</li><li>PRODUCTS_EXPORTED - Успешно экспортированных товаров | Fields\IntegerField</li><li>ERROR_TEXT - Текст ошибки | Fields\TextField</li></ul>
 * @package Local\Core\Model\Data
 */
class TradingPlatformExportLogTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_data_trading_platform_export_log';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
        'RESULT' => [
            'SU' => 'Успех',
            'ER' => 'Ошибка'
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

            new Fields\IntegerField('STORE_ID', [
                'title' => 'ID магазина',
            ]),
            new Fields\IntegerField('TP_ID', [
                'title' => 'ID торговой площадки',
            ]),
            new Fields\EnumField('RESULT', [
                'title' => 'Результат',
                'required' => true,
                'values' => self::getEnumFieldValues('RESULT'),
            ]),
            new Fields\IntegerField('PRODUCTS_TOTAL', [
                'title' => 'Всего отфильтрованных товаров',
            ]),
            new Fields\IntegerField('PRODUCTS_EXPORTED', [
                'title' => 'Успешно экспортированных товаров',
            ]),
            new Fields\TextField('ERROR_TEXT', [
                'title' => 'Текст ошибки',
            ]),
        ];
    }

    public static function onAfterAdd(\Bitrix\Main\ORM\Event $event)
    {
        $arFields = $event->getParameter('fields');
        if (!empty($arFields['TP_ID'])) {
            $rs = self::getList([
                'filter' => ['TP_ID' => $arFields['TP_ID']],
                'select' => ['ID'],
                'order' => ['DATE_CREATE' => 'DESC']
            ]);

            $intMaxLastLogCount = \Bitrix\Main\Config\Configuration::getInstance()
                                      ->get('tradingplatform')['export']['max_logs_count'] ?? 10;
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
    }

    public static function OnBeforeUpdate(\Bitrix\Main\ORM\Event $event)
    {
        $result = new \Bitrix\Main\ORM\EventResult();
        $arModifiedFields = [];

        # Вызывается строго в конце
        self::_OnBeforeUpdateBase($event, $result, $arModifiedFields);

        return $result;
    }

    public static function OnAfterUpdate(\Bitrix\Main\ORM\Event $event)
    {
        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
    }

    public static function OnDelete(\Bitrix\Main\ORM\Event $event)
    {
        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
    }
}