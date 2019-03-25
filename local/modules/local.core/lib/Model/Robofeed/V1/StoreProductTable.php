<?

namespace Local\Core\Model\Robofeed\V1;

use \Bitrix\Main\ORM\Fields,
    \Local\Core\Model\Reference,
    Local\Core\Inner\Cache;
use Local\Core\Model\Robofeed\StoreProductParamFactory;
use Local\Core\Model\Robofeed\StoreProductDeliveryFactory;
use Local\Core\Model\Robofeed\StoreProductPickupFactory;

/**
 * Класс для хранения товаро магазина
 *
 * @package Local\Core\Model\Robofeed
 */
class StoreProductTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    use \Local\Core\Model\Robofeed\Traites\TableByStore;

    public static function getTableName()
    {
        if( is_null(self::$intStoreId) )
        {
            throw new \Exception('Необходимо задать ID магазина');
        }

        return 'c_robofeed_store_'.self::$intStoreId.'_product';
    }

    public function __construct()
    {
        self::$arEnumFieldsValues['CURRENCY_CODE'] = $this->__getOrmValues(Reference\CurrencyTable::class, 'CODE');
        self::$arEnumFieldsValues['CURRENCY_CODE'] = array_combine(self::$arEnumFieldsValues['CURRENCY_CODE'], self::$arEnumFieldsValues['CURRENCY_CODE']);

        self::$arEnumFieldsValues['COUNTRY_OF_PRODUCTION_CODE'] = $this->__getOrmValues(Reference\CountryTable::class, 'CODE');
        self::$arEnumFieldsValues['COUNTRY_OF_PRODUCTION_CODE'] = array_combine(self::$arEnumFieldsValues['COUNTRY_OF_PRODUCTION_CODE'], self::$arEnumFieldsValues['COUNTRY_OF_PRODUCTION_CODE']);

        self::$arEnumFieldsValues['UNIT_OF_MEASURE'] = $this->__getOrmValues(Reference\MeasureTable::class, 'CODE');
        self::$arEnumFieldsValues['UNIT_OF_MEASURE'] = array_combine(self::$arEnumFieldsValues['UNIT_OF_MEASURE'], self::$arEnumFieldsValues['UNIT_OF_MEASURE']);

        self::$arEnumFieldsValues['WEIGHT_UNIT_CODE'] = self::$arEnumFieldsValues['UNIT_OF_MEASURE'];
        self::$arEnumFieldsValues['WIDTH_UNIT_CODE'] = self::$arEnumFieldsValues['UNIT_OF_MEASURE'];
        self::$arEnumFieldsValues['HEIGHT_UNIT_CODE'] = self::$arEnumFieldsValues['UNIT_OF_MEASURE'];
        self::$arEnumFieldsValues['LENGTH_UNIT_CODE'] = self::$arEnumFieldsValues['UNIT_OF_MEASURE'];
        self::$arEnumFieldsValues['VOLUME_UNIT_CODE'] = self::$arEnumFieldsValues['UNIT_OF_MEASURE'];
        self::$arEnumFieldsValues['WARRANTY_PERIOD_CODE'] = self::$arEnumFieldsValues['UNIT_OF_MEASURE'];
        self::$arEnumFieldsValues['EXPIRY_PERIOD_CODE'] = self::$arEnumFieldsValues['UNIT_OF_MEASURE'];
    }

    private static function __getOrmValues(string $strClass, string $strColumnName)
    {

        $arResult = [];

        if( class_exists($strClass) )
        {
            $obCache = \Bitrix\Main\Application::getInstance()
                ->getCache();
            if(
            $obCache->startDataCache(
                60 * 60 * 24 * 7,
                '\Local\Core\Inner\Robofeed\SchemeFields\ReferenceField_class='.$strClass,
                Cache::getCachePath(
                    [
                        'Robofeed',
                        'Scheme',
                        'ReferenceField'
                    ],
                    [
                        'class='.( implode('_', array_slice(explode('\\', $strClass), -2)) ),
                        'column_name='.$strColumnName
                    ]
                )
            )
            )
            {
                /** @var \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager $strClass */

                $rs = $strClass::getList(
                    [
                        'order' => ['SORT' => 'ASC'],
                        'select' => [$strColumnName]
                    ]
                );
                if( $rs->getSelectedRowsCount() < 1 )
                {
                    $obCache->abortDataCache();
                }
                else
                {
                    while( $ar = $rs->fetch() )
                    {
                        $arResult[] = $ar[$strColumnName];
                    }
                    $obCache->endDataCache($arResult);
                }
            }
            else
            {
                $arResult = $obCache->getVars();
            }
        }

        return $arResult;
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
        'MANUFACTURER_WARRANTY' => [
            'Y' => 'Да',
            'N' => 'Нет'
        ],
        'IS_SEX' => [
            'Y' => 'Да',
            'N' => 'Нет'
        ],
        'IS_SOFTWARE' => [
            'Y' => 'Да',
            'N' => 'Нет'
        ],
        'IN_STOCK' => [
            'Y' => 'Да',
            'N' => 'Нет'
        ],
        'DELIVERY' => [
            'Y' => 'Да',
            'N' => 'Нет'
        ],
        'PICKUP' => [
            'Y' => 'Да',
            'N' => 'Нет'
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
                'ROBOFEED_VERSION', [
                    'required' => false,
                    'title' => 'Версия Robofeed XML'
                ]
            ),

            new Fields\IntegerField(
                'PRODUCT_ID', [
                    'title' => 'ID товара',
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'PRODUCT_GROUP_ID', [
                    'title' => 'ID группы товара',
                    'required' => false,
                ]
            ),
            new Fields\StringField(
                'ARTICLE', [
                    'title' => 'Артикул товара',
                    'required' => true,
                ]
            ),
            new Fields\StringField(
                'FULL_NAME', [
                    'title' => 'Полное название товара',
                    'required' => true,
                ]
            ),
            new Fields\StringField(
                'SIMPLE_NAME', [
                    'title' => 'Простое название товара',
                    'required' => true,
                ]
            ),
            new Fields\StringField(
                'MANUFACTURER', [
                    'title' => 'Название компании производителя',
                    'required' => true,
                ]
            ),
            new Fields\StringField(
                'MODEL', [
                    'title' => 'Модель товара',
                    'required' => false,
                ]
            ),
            new Fields\StringField(
                'URL', [
                    'title' => 'Ссылка на детальную страницу товара',
                    'required' => false,
                ]
            ),
            new Fields\StringField(
                'MANUFACTURER_CODE', [
                    'title' => 'Код производителя для данного товара',
                    'required' => false,
                ]
            ),
            new Fields\FloatField(
                'PRICE', [
                    'title' => 'Текущая публичная стоимость товара',
                    'required' => true,
                ]
            ),
            new Fields\FloatField(
                'OLD_PRICE', [
                    'title' => 'Базовая / старая стоимость товара',
                    'required' => false,
                ]
            ),
            new Fields\EnumField(
                'CURRENCY_CODE', [
                    'title' => 'Символьный код валюты',
                    'required' => true,
                    'values' => self::getEnumFieldValues('CURRENCY_CODE')
                ]
            ),
            new Fields\IntegerField(
                'QUANTITY', [
                    'title' => 'Количество товара в единицах измерения',
                    'required' => true,
                ]
            ),
            new Fields\EnumField(
                'UNIT_OF_MEASURE', [
                    'title' => 'Символьный код единицы измерения',
                    'required' => true,
                    'values' => self::getEnumFieldValues('UNIT_OF_MEASURE')
                ]
            ),
            new Fields\IntegerField(
                'MIN_QUANTITY', [
                    'title' => 'Минимальное кол-во товара в заказе',
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'CATEGORY_ID', [
                    'title' => 'ID категории товара',
                    'required' => true,
                ]
            ),
            new Fields\TextField(
                'IMAGE', [
                    'title' => 'Изображения',
                    'required' => false,
                ]
            ),
            new Fields\EnumField(
                'COUNTRY_OF_PRODUCTION_CODE', [
                    'title' => 'Символьный код страны производства',
                    'required' => false,
                    'values' => self::getEnumFieldValues('COUNTRY_OF_PRODUCTION_CODE')
                ]
            ),
            new Fields\TextField(
                'DESCRIPTION', [
                    'title' => 'Описание товара',
                    'required' => false,
                ]
            ),
            new Fields\EnumField(
                'MANUFACTURER_WARRANTY', [
                    'title' => 'Официальная гарантия производителя',
                    'required' => true,
                    'values' => self::getEnumFieldValues('MANUFACTURER_WARRANTY')
                ]
            ),
            new Fields\EnumField(
                'IS_SEX', [
                    'title' => 'Товар имеет отношение к удовлетворению сексуальных потребностей либо иным образом эксплуатирует интерес к сексу',
                    'required' => false,
                    'values' => self::getEnumFieldValues('IS_SEX')
                ]
            ),
            new Fields\EnumField(
                'IS_SOFTWARE', [
                    'title' => 'Товар является программным обеспечением',
                    'required' => false,
                    'values' => self::getEnumFieldValues('IS_SOFTWARE')
                ]
            ),
            new Fields\IntegerField(
                'WEIGHT', [
                    'title' => 'Вес товара в выбранных единицах измерения',
                    'required' => false,
                ]
            ),
            new Fields\EnumField(
                'WEIGHT_UNIT_CODE', [
                    'title' => 'Единица измерения веса',
                    'required' => false,
                    'values' => self::getEnumFieldValues('WEIGHT_UNIT_CODE')
                ]
            ),
            new Fields\FloatField(
                'WIDTH', [
                    'title' => 'Ширина товара в выбранных единицах измерения',
                    'required' => false,
                ]
            ),
            new Fields\EnumField(
                'WIDTH_UNIT_CODE', [
                    'title' => 'Единица измерения ширины',
                    'required' => false,
                    'values' => self::getEnumFieldValues('WIDTH_UNIT_CODE')
                ]
            ),
            new Fields\FloatField(
                'HEIGHT', [
                    'title' => 'Высота товара в выбранных единицах измерения',
                    'required' => false,
                ]
            ),
            new Fields\EnumField(
                'HEIGHT_UNIT_CODE', [
                    'title' => 'Единица измерения высоты',
                    'required' => false,
                    'values' => self::getEnumFieldValues('HEIGHT_UNIT_CODE')
                ]
            ),
            new Fields\FloatField(
                'LENGTH', [
                    'title' => 'Длина товара в выбранных единицах измерения',
                    'required' => false,
                ]
            ),
            new Fields\EnumField(
                'LENGTH_UNIT_CODE', [
                    'title' => 'Единица измерения длины',
                    'required' => false,
                    'values' => self::getEnumFieldValues('LENGTH_UNIT_CODE')
                ]
            ),
            new Fields\FloatField(
                'VOLUME', [
                    'title' => 'Объем товара',
                    'required' => false,
                ]
            ),
            new Fields\EnumField(
                'VOLUME_UNIT_CODE', [
                    'title' => 'Единица измерения объема',
                    'required' => false,
                    'values' => self::getEnumFieldValues('VOLUME_UNIT_CODE')
                ]
            ),
            new Fields\IntegerField(
                'WARRANTY_PERIOD', [
                    'title' => 'Срок официальной гарантии товара',
                    'required' => false,
                ]
            ),
            new Fields\EnumField(
                'WARRANTY_PERIOD_CODE', [
                    'title' => 'Единица измерения срока официальной гарантии товара',
                    'required' => false,
                    'values' => self::getEnumFieldValues('WARRANTY_PERIOD_CODE')
                ]
            ),
            new Fields\IntegerField(
                'EXPIRY_PERIOD', [
                    'title' => 'Срок годности / срок службы товара от даты производства',
                    'required' => false,
                ]
            ),
            new Fields\EnumField(
                'EXPIRY_PERIOD_CODE', [
                    'title' => 'Единица измерения срока годности / срока службы товара',
                    'required' => false,
                    'values' => self::getEnumFieldValues('EXPIRY_PERIOD_CODE')
                ]
            ),
            new Fields\DatetimeField(
                'EXPIRY_DATE', [
                    'title' => 'Дата истечения срока годности товара',
                    'required' => false,
                ]
            ),
            new Fields\EnumField(
                'DELIVERY_AVAILABLE', [
                    'title' => 'Имеется ли служба доставки',
                    'required' => true,
                    'values' => self::getEnumFieldValues('DELIVERY')
                ]
            ),
            new Fields\EnumField(
                'PICKUP_AVAILABLE', [
                    'title' => 'Имеется ли возможность самовывоза из магазина или со склада',
                    'required' => true,
                    'values' => self::getEnumFieldValues('PICKUP')
                ]
            ),
            new Fields\EnumField(
                'IN_STOCK', [
                    'title' => 'Товар есть в наличии',
                    'required' => true,
                    'values' => self::getEnumFieldValues('IN_STOCK')
                ]
            ),
            new Fields\StringField(
                'SALES_NOTES', [
                    'title' => 'Условия продажи товара',
                    'required' => false,
                ]
            ),

            new Fields\Relations\OneToMany(
                'PARAMS',
                get_class(StoreProductParamFactory::factory(1)->setStoreId( self::$intStoreId )),
                'PRODUCT'
            ),
            new Fields\Relations\OneToMany(
                'DELIVERIES',
                get_class(StoreProductDeliveryFactory::factory(1)->setStoreId( self::$intStoreId )),
                'PRODUCT'
            ),
            new Fields\Relations\OneToMany(
                'PICKUPS',
                get_class(StoreProductPickupFactory::factory(1)->setStoreId( self::$intStoreId )),
                'PRODUCT'
            ),
        ];
    }
}