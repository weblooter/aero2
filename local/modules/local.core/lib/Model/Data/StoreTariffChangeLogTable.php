<?

namespace Local\Core\Model\Data;

use \Bitrix\Main\ORM\Fields;

/**
 * ORM для логов изменения тарифов у магазинов
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>DATE_CREATE - Дата создания [01.04.2019 19:04:24] | Fields\DatetimeField</li><li>STORE_ID - ID магазина | Fields\IntegerField</li><li>TARIFF_CODE -
 * Символьный код тарифа | Fields\StringField</li><li>STORE_DATA - \Local\Core\Model\Data\Store | Fields\Relations\Reference</li><li>TARIFF_DATA - \Local\Core\Model\Data\Tariff |
 * Fields\Relations\Reference</li></ul>
 *
 * @package Local\Core\Model\Data
 */
class StoreTariffChangeLogTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_store_tariff_change_log';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [];

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
            new Fields\IntegerField('STORE_ID', [
                    'required' => true,
                    'title' => 'ID магазина'
                ]),
            new Fields\StringField('TARIFF_CODE', [
                    'required' => true,
                    'title' => 'Символьный код тарифа'
                ]),

            new Fields\Relations\Reference('STORE_DATA', StoreTable::class, \Bitrix\Main\ORM\Query\Join::on('this.STORE_ID', 'ref.ID')),
            new Fields\Relations\Reference('TARIFF_DATA', TariffTable::class, \Bitrix\Main\ORM\Query\Join::on('this.TARIFF_CODE', 'ref.CODE')),

        ];
    }
}