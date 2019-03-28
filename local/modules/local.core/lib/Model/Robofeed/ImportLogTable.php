<?

namespace Local\Core\Model\Robofeed;

use Bitrix\Main\ORM\Event;
use \Bitrix\Main\ORM\Fields;
use Local\Core\Model\Data\StoreTable;

/**
 * Класс для логрования результатов импорта Robofeed XML
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>DATE_CREATE - Дата создания [28.03.2019 20:24:15] | Fields\DatetimeField</li><li>STORE_ID - ID магазина | Fields\IntegerField</li><li>ROBOFEED_VERSION - Версия Robofeed XML | Fields\IntegerField</li><li>ROBOFEED_DATE - Дата создания Robofeed XML | Fields\DatetimeField</li><li>BEHAVIOR_IMPORT_ERROR - Поведение импорта при ошибке | Fields\EnumField<br/>&emsp;STOP_IMPORT => Не актуализировать данные<br/>&emsp;IMPORT_ONLY_VALID => Актуализировать только валидные<br/></li><li>PRODUCT_TOTAL_COUNT - Кол-во товаров в Robofeed XML | Fields\IntegerField</li><li>PRODUCT_SUCCESS_IMPORT - Кол-во валидных товаров в Robofeed XML | Fields\IntegerField</li><li>IMPORT_COMPLETED - Импорт завершен [N] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;E => Ошибка<br/>&emsp;N => Нет<br/></li><li>ERROR_TEXT - Ошибка | Fields\TextField</li><li>SYSTEM_ERROR - Системная ошибка импорта | Fields\TextField</li><li>STORE_DATA - \Local\Core\Model\Data\Store | Fields\Relations\Reference</li></ul>
 *
 * @package Local\Core\Model\Robofeed
 */
class ImportLogTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    const BEHAVIOR_IMPORT_ERROR_STOP_IMPORT = 'STOP_IMPORT';
    const BEHAVIOR_IMPORT_ERROR_IMPORT_ONLY_VALID = 'IMPORT_ONLY_VALID';

    public static function getTableName()
    {
        return 'a_model_robofeed_import_log';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
        'IMPORT_COMPLETED' => [
            'Y' => 'Да',
            'E' => 'Ошибка',
            'N' => 'Нет'
        ],
        'BEHAVIOR_IMPORT_ERROR' => [
            'STOP_IMPORT' => 'Не актуализировать данные',
            'IMPORT_ONLY_VALID' => 'Актуализировать только валидные',
        ],
    ];

    public static function getMap()
    {
        return [
            new Fields\IntegerField(
                'ID', [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => 'ID'
                ]
            ),
            new Fields\DatetimeField(
                'DATE_CREATE', [
                    'title' => 'Дата создания',
                    'required' => false,
                    'default_value' => function()
                        {
                            return new \Bitrix\Main\Type\DateTime();
                        }
                ]
            ),
            new Fields\IntegerField(
                'STORE_ID', [
                    'required' => true,
                    'title' => 'ID магазина'
                ]
            ),
            new Fields\IntegerField(
                'ROBOFEED_VERSION', [
                    'required' => false,
                    'title' => 'Версия Robofeed XML'
                ]
            ),
            new Fields\DatetimeField(
                'ROBOFEED_DATE', [
                    'required' => false,
                    'title' => 'Дата создания Robofeed XML'
                ]
            ),
            new Fields\EnumField(
                'BEHAVIOR_IMPORT_ERROR',
                [
                    'required' => false,
                    'title' => 'Поведение импорта при ошибке',
                    'values' => self::getEnumFieldValues('BEHAVIOR_IMPORT_ERROR'),
                ]
            ),
            new Fields\IntegerField(
                'PRODUCT_TOTAL_COUNT', [
                    'required' => false,
                    'title' => 'Кол-во товаров в Robofeed XML'
                ]
            ),
            new Fields\IntegerField(
                'PRODUCT_SUCCESS_IMPORT', [
                    'required' => false,
                    'title' => 'Кол-во валидных товаров в Robofeed XML'
                ]
            ),
            new Fields\EnumField(
                'IMPORT_COMPLETED', [
                    'required' => false,
                    'title' => 'Импорт завершен',
                    'values' => self::getEnumFieldValues('IMPORT_COMPLETED'),
                    'default_value' => 'N'
                ]
            ),
            new Fields\TextField(
                'ERROR_TEXT',
                [
                    'required' => false,
                    'title' => 'Ошибка',
                ]
            ),
            new Fields\TextField(
                'SYSTEM_ERROR',
                [
                    'required' => false,
                    'title' => 'Системная ошибка импорта',
                ]
            ),

            new Fields\Relations\Reference('STORE_DATA',
                StoreTable::class,
                    \Bitrix\Main\ORM\Query\Join::on('this.STORE_ID', 'ref.ID'),
                    [
                        'title' => 'ORM: Магазин'
                    ]
                )
        ];
    }

    public static function onAfterAdd(Event $event)
    {
        $arFields = $event->getParameter('fields');
        if( !empty($arFields['STORE_ID']) )
        {
            $rs = self::getList([
                'filter' => ['STORE_ID' => $arFields['STORE_ID'] ],
                'select' => ['ID'],
                'order' => ['DATE_CREATE' => 'DESC']
            ]);

            $intMaxLastLogCount = \Bitrix\Main\Config\Configuration::getInstance()->get('robofeed')['ImportLogTable']['max_last_log_count'] ?? 30;
            if( $rs->getSelectedRowsCount() > $intMaxLastLogCount )
            {
                $i = 0;
                while($ar = $rs->fetch())
                {
                    $i++;
                    if( $i > $intMaxLastLogCount )
                    {
                        self::delete($ar['ID']);
                    }
                }
            }
        }
    }
}