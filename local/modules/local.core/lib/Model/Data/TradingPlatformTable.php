<?

namespace Local\Core\Model\Data;

use Bitrix\Main\ORM\Event;
use \Bitrix\Main\ORM\Fields;

/**
 * ORM торговых пощадок
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>ACTIVE - Активность [N] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/></li><li>DATE_CREATE - Дата создания [12.04.2019 19:21:57] |
 * Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [12.04.2019 19:21:57] | Fields\DatetimeField</li><li>NAME - Название | Fields\StringField</li><li>STORE_ID - ID магазина |
 * Fields\IntegerField</li><li>HANDLER - Обработчик | Fields\StringField</li><li>HANDLER_RULES - Правила обработчика | Fields\TextField</li><li>PAYED_FROM - Оплачено с |
 * Fields\DatetimeField</li><li>PAYED_TO - Оплачено до | Fields\DatetimeField</li><li>STORE - \Local\Core\Model\Data\Store | Fields\Relations\Reference</li></ul>
 *
 * @package Local\Core\Model\Data
 */
class TradingPlatformTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_data_trading_platform';
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
            new Fields\EnumField('ACTIVE', [
                'title' => 'Активность',
                'values' => self::getEnumFieldValues('ACTIVE'),
                'default_value' => 'N'
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

            new Fields\StringField('NAME', [
                'title' => 'Название',
                'required' => true
            ]),
            new Fields\StringField('CODE', [
                'title' => 'Символьный код',
            ]),
            new Fields\IntegerField('STORE_ID', [
                'title' => 'ID магазина',
                'required' => true
            ]),
            new Fields\StringField('HANDLER', [
                'title' => 'Обработчик',
                'required' => true
            ]),
            new Fields\TextField('PRODUCT_FILTER', [
                'title' => 'Фильт товаров',
                'save_data_modification' => function ()
                    {
                        return [
                            function ($v)
                                {
                                    if (empty($v) || is_null($v)) {
                                        $v = [];
                                    }
                                    return json_encode($v, JSON_UNESCAPED_UNICODE);
                                }
                        ];
                    },
                'fetch_data_modification' => function ()
                    {
                        return [
                            function ($v)
                                {
                                    if (empty($v) || is_null($v)) {
                                        $v = [];
                                    }
                                    else
                                    {
                                        $v = json_decode($v, true);
                                    }
                                    return $v;
                                }
                        ];
                    }
            ]),
            new Fields\TextField('HANDLER_RULES', [
                'title' => 'Правила обработчика',
                'required' => true,
                'save_data_modification' => function ()
                    {
                        return [
                            function ($v)
                                {
                                    if (empty($v) || is_null($v)) {
                                        $v = [];
                                    }
                                    return json_encode($v, JSON_UNESCAPED_UNICODE);
                                }
                        ];
                    },
                'fetch_data_modification' => function ()
                    {
                        return [
                            function ($v)
                                {
                                    if (empty($v) || is_null($v)) {
                                        $v = [];
                                    }
                                    else
                                    {
                                        $v = json_decode($v, true);
                                    }
                                    return $v;
                                }
                        ];
                    }
            ]),
            new Fields\DatetimeField('PAYED_FROM', [
                'title' => 'Оплачено с'
            ]),
            new Fields\DatetimeField('PAYED_TO', [
                'title' => 'Оплачено до'
            ]),

            new Fields\Relations\Reference('STORE', StoreTable::class, \Bitrix\Main\ORM\Query\Join::on('this.STORE_ID', 'ref.ID'), [
                    'title' => 'ORM: Магазин'
                ]),
        ];
    }

    public static function onBeforeAdd(Event $event)
    {
        $result = new \Bitrix\Main\ORM\EventResult();

        $arFields = $event->getParameter('fields');
        if (!is_null($arFields['PAYED_FROM']) && !is_null($arFields['PAYED_TO'])) {
            if ($arFields['PAYED_FROM']->getTimestamp() > $arFields['PAYED_TO']->getTimestamp()) {
                $result->addError(new \Bitrix\Main\ORM\EntityError('Дата начала оплаты должно быть меньше даты окончания оплаты.'));
            }
        }

        $arFields['CODE'] = sha1($arFields['NAME'].'#'.$GLOBALS['USER_ID'].'#'.$arFields['STORE_ID'].'#'.time());

        return $result;
    }

    public static function OnBeforeUpdate(\Bitrix\Main\ORM\Event $event)
    {
        $result = new \Bitrix\Main\ORM\EventResult();
        $arModifiedFields = [];

        $arFields = $event->getParameter('fields');

        if (!is_null($arFields['PAYED_FROM']) || !is_null($arFields['PAYED_TO'])) {
            try {
                $arElemData = self::getByPrimary($event->getParameter('primary')['ID'], ['select' => ['PAYED_FROM', 'PAYED_TO']])
                    ->fetch();
                if (!is_null($arFields['PAYED_FROM']) && !is_null($arFields['PAYED_TO'])) {
                    if ($arFields['PAYED_FROM']->getTimestamp() > $arFields['PAYED_TO']->getTimestamp()) {
                        throw new \Exception();
                    }
                } elseif (!is_null($arFields['PAYED_FROM'])) {
                    if ($arFields['PAYED_FROM']->getTimestamp() > $arElemData['PAYED_TO']->getTimestamp()) {
                        throw new \Exception();
                    }
                } elseif (!is_null($arFields['PAYED_TO'])) {
                    if ($arElemData['PAYED_FROM']->getTimestamp() > $arFields['PAYED_TO']->getTimestamp()) {
                        throw new \Exception();
                    }
                }
            } catch (\Exception $e) {
                $result->addError(new \Bitrix\Main\ORM\EntityError('Дата начала оплаты должно быть меньше даты окончания оплаты.'));
            }
        }

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