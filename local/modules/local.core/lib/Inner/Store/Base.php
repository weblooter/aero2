<?

namespace Local\Core\Inner\Store;

use Local\Core\Model\Data\StoreTable;
use Local\Core\Model\Robofeed\ImportLogTable;
use function Sodium\add;

/**
 * Класс для работы с магазинами
 *
 * @package Local\Core\Inner\Store
 */
class Base
{
    /** @var array $__register Регистр магазинов */
    private static $__register = [];

    /**
     * Заполняет регистр сайта
     *
     * @param integer $intStoreId ID магазина
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __fillStoreRegister($intStoreId)
    {
        if (is_null(self::$__register[$intStoreId])) {
            $arTmp = \Local\Core\Model\Data\StoreTable::getList([
                'filter' => ['ID' => $intStoreId],
                'select' => [
                    'ID',
                    'NAME',
                    'DOMAIN',
                    'COMPANY_ID',
                    'COMPANY_DATA_' => 'COMPANY',
                    'TARIFF_CODE',
                    'DATE_LAST_SUCCESS_IMPORT',
                    'PRODUCT_SUCCESS_IMPORT',
                    'LAST_IMPORT_VERSION',
                    'LAST_SUCCESS_IMPORT_VERSION',
                ]
            ])
                ->fetch();

            $ar = [
                'ID' => $arTmp['ID'],
                'NAME' => $arTmp['NAME'],
                'DOMAIN' => $arTmp['DOMAIN'],
                'COMPANY_ID' => $arTmp['COMPANY_ID'],
                'COMPANY_USER_OWN_ID' => $arTmp['COMPANY_DATA_USER_OWN_ID'],
                'TARIFF_CODE' => $arTmp['TARIFF_CODE'],
                'LAST_IMPORT_RESULT' => $arTmp['LAST_IMPORT_RESULT'],
                'DATE_LAST_SUCCESS_IMPORT' => $arTmp['DATE_LAST_SUCCESS_IMPORT'],
                'PRODUCT_SUCCESS_IMPORT' => $arTmp['PRODUCT_SUCCESS_IMPORT'],
                'LAST_IMPORT_VERSION' => $arTmp['LAST_IMPORT_VERSION'],
                'LAST_SUCCESS_IMPORT_VERSION' => $arTmp['LAST_SUCCESS_IMPORT_VERSION'],
            ];

            self::$__register[$intStoreId] = $ar;
        }
    }

    /**
     * Возвращает регистр магазинов
     *
     * @param integer $intStoreId ID магазина
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __getStoreRegister($intStoreId)
    {
        self::__fillStoreRegister($intStoreId);
        return self::$__register[$intStoreId];
    }

    /**
     * Очищает регистр
     *
     * @param $intStoreId
     */
    public static function clearRegister($intStoreId)
    {
        unset(self::$__register[$intStoreId]);
    }


    const ACCESS_STORE_IS_MINE = 0x001;
    const ACCESS_STORE_NOT_FOUND = 0x002;
    const ACCESS_STORE_NOT_MINE = 0x003;

    /**
     * Проверяет права на магазин.<br/>
     * Результат необходимо сравнить с константами класса:<br/>
     * <ul>
     * <li>ACCESS_STORE_IS_MINE</li>
     * <li>ACCESS_STORE_NOT_FOUND</li>
     * <li>ACCESS_STORE_NOT_MINE</li>
     * </ul>
     *
     * @param integer $intStoreId ID магазина
     * @param integer $intUserId  ID пользователя
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function checkUserAccess($intStoreId, $intUserId = 0)
    {
        if ($intUserId < 1) {
            $intUserId = $GLOBALS['USER']->GetID();
        }

        $ar = self::__getStoreRegister($intStoreId);

        if (!empty($ar)) {
            if ($ar['COMPANY_USER_OWN_ID'] == $intUserId) {
                return self::ACCESS_STORE_IS_MINE;
            } else {
                return self::ACCESS_STORE_NOT_MINE;
            }
        } else {
            return self::ACCESS_STORE_NOT_FOUND;
        }

    }

    /**
     * Получить домен магазина
     *
     * @param integer $intStoreId ID магазина
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getStoreDomain($intStoreId)
    {
        $ar = self::__getStoreRegister($intStoreId);
        return $ar['DOMAIN'];
    }

    /**
     * Получить название сайта
     *
     * @param integer $intStoreId ID магазина
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getStoreName($intStoreId)
    {
        $ar = self::__getStoreRegister($intStoreId);
        return $ar['NAME'];
    }

    /**
     * Получить тариф магазина
     *
     * @param integer $intStoreId ID магазина
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getTariffCode($intStoreId)
    {
        $ar = self::__getStoreRegister($intStoreId);
        return $ar['TARIFF_CODE'];
    }

    /**
     * Сменяет тариф магазину, проверяя пользователя, баланс и списывая деньги
     *
     * @param int $intStoreId ID магазина
     * @param string $strTariffCode Код тарифа
     * @param bool $boolSendEmail Признак отправлять уведомление по почте или нет
     * @param bool $boolRecalculateBalance Если указан true, то перерасчет производится в любом случае. Если false - перерасчет происходит только если цена нового тарифа дороже старого.
     *
     * @return \Bitrix\Main\Result
     */
    public static function changeStoreTariff($intStoreId, $strTariffCode, $boolSendEmail = true, $boolRecalculateBalance = false)
    {
        $obResult = new \Bitrix\Main\Result();

        try {
            $oldTariffPrice = \Local\Core\Inner\Tariff\Base::getPrice(\Local\Core\Inner\Store\Base::getTariffCode($intStoreId));

            /* *********************** */
            /* Меняет тариф у магазина */
            /* *********************** */

            $arTariff = \Local\Core\Model\Data\TariffTable::getList([
                'filter' => [
                    'CODE' => $strTariffCode,
                    'ACTIVE' => 'Y',
                    [
                        'LOGIC' => 'OR',
                        ['TYPE' => 'PUB'],
                        ['TYPE' => 'PER', 'PERSONAL_BY_STORE' => $intStoreId],
                    ]
                ]
            ])
                ->fetch();

            if (empty($arTariff)) {
                throw new \Exception('Ну удалось найти тариф.');
            }

            if (\Local\Core\Inner\Store\Base::getTariffCode($intStoreId) == $strTariffCode) {
                throw new \Exception('Невозможно изменить тариф на тот же.');
            }

            $obUpdateRes = StoreTable::update($intStoreId, [
                'TARIFF_CODE' => $strTariffCode
            ]);

            if( $obUpdateRes->isSuccess() )
            {
                \Local\Core\Inner\Store\Base::clearRegister($intStoreId);

                if ($boolSendEmail) {
                    $arUser = \Bitrix\Main\UserTable::getByPrimary(\Local\Core\Inner\Store\Base::getOwnUserId($intStoreId), ['select' => ['EMAIL']])
                        ->fetch();

                    $arMailFields = [
                        'EMAIL' => $arUser['EMAIL'],
                        'STORE_NAME' => \Local\Core\Inner\Store\Base::getStoreName($intStoreId),
                        'NEW_TARIFF_NAME' => $arTariff['NAME'],
                        'NEW_TARIFF_PRODUCT_LIMIT' => number_format($arTariff['LIMIT_IMPORT_PRODUCTS'], 0, '.', ' '),
                    ];

                    if (!is_null($arTariff['DATE_ACTIVE_TO'])) {
                        $arMailFields['DATE_ACTIVE_TO'] = $arTariff['DATE_ACTIVE_TO']->format('Y-m-d H:i');
                        $arMailFields['NEXT_TARIFF_NAME'] = (!empty($arTariff['SWITCH_AFTER_ACTIVE_TO']) ? \Local\Core\Inner\Tariff\Base::getTariffByCode($arTariff['SWITCH_AFTER_ACTIVE_TO']) : \Local\Core\Inner\Tariff\Base::getDefaultTariff())['NAME'];
                    }

                    \Local\Core\Inner\TriggerMail\Store::tariffChanged($arMailFields);
                }

                /* ******************************** */
                /* Делаем возврат за оставшиеся дни */
                /* ******************************** */

                $newTariffPrice = $arTariff['PRICE_PER_TRADING_PLATFORM'];

                if( $oldTariffPrice < $newTariffPrice || $boolRecalculateBalance)
                {
                    # Если новый тариф дороже

                    $rsTpList = \Local\Core\Model\Data\TradingPlatformTable::getList([
                        'filter' => [
                            'STORE_ID' => $intStoreId,
                            '>PAYED_TO' => new \Bitrix\Main\Type\DateTime()
                        ],
                        'select' => ['ID', 'PAYED_TO', 'PAYED_FROM', 'NAME']
                    ]);
                    while ($ar = $rsTpList->fetch())
                    {
                        if( $ar['PAYED_TO'] instanceof \Bitrix\Main\Type\DateTime)
                        {
                            # ТП была ранее хоть раз активирована

                            $intBetweenDays = ( $ar['PAYED_TO']->getTimestamp() - $ar['PAYED_FROM']->getTimestamp() )/60/60/24;
                            $intLeftDaysPayed = floor(( $ar['PAYED_TO']->getTimestamp() - strtotime('now') )/60/60/24);

                            $intNeedMoneyBack = floor($oldTariffPrice / $intBetweenDays * $intLeftDaysPayed);
                            if( $intNeedMoneyBack > 0 )
                            {
                                \Local\Core\Inner\Balance\Base::payToAccount($intNeedMoneyBack, \Local\Core\Inner\Store\Base::getOwnUserId($intStoreId), 'Возврат оставшегося оплаченного периода торговой площадки "'.$ar['NAME'].'" магазина "'.\Local\Core\Inner\Store\Base::getStoreName($intStoreId).'" по причине смены тарифного плана магазина.');
                            }
                            \Local\Core\Model\Data\TradingPlatformTable::update($ar['ID'], ['PAYED_TO' => new \Bitrix\Main\Type\DateTime(date('Y-m-d 00:00:01'), 'Y-m-d H:i:s')]);
                        }
                    }

                    \Local\Core\Inner\TradingPlatform\MonthlyPayment::execute(['STORE_ID' => $intStoreId]);
                }

                /* ********************************** */
                /* Создадим воркер импорта и экпортов */
                /* ********************************** */

                $worker = new \Local\Core\Inner\JobQueue\Worker\StoreRobofeedImport(['STORE_ID' => $intStoreId]);
                $dateTime = new \Bitrix\Main\Type\DateTime();
                \Local\Core\Inner\JobQueue\Job::addIfNotExist($worker, $dateTime, 1);

                $rsTpList = \Local\Core\Model\Data\TradingPlatformTable::getList([
                    'filter' => [
                        'STORE_ID' => $intStoreId,
                        'ACTIVE' => 'Y'
                    ],
                    'select' => ['ID']
                ]);

                while ($ar = $rsTpList->fetch())
                {
                    $worker = new \Local\Core\Inner\JobQueue\Worker\TradingPlatformExport(['TP_ID' => $ar['ID']]);
                    $dateTime = (new \Bitrix\Main\Type\DateTime())->add('+1 hour');
                    \Local\Core\Inner\JobQueue\Job::addIfNotExist($worker, $dateTime, 1);
                }
            }
            else
            {
                throw new \Exception(implode(', ', $obUpdateRes->getErrorMessages()));
            }

        } catch (\Exception $e) {
            $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
        }

        return $obResult;
    }

    /**
     * Получает самый предпочительный тариф, отталкиваясь от данных магазина
     *
     * @param $intStoreId
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function recommendTariff($intStoreId)
    {
        $arReturn = [];
        $arStore = \Local\Core\Model\Data\StoreTable::getByPrimary($intStoreId)
            ->fetch();

        $arCurrentTariff = \Local\Core\Inner\Tariff\Base::getTariffByCode($arStore['TARIFF_CODE']);
        if ($arStore['PRODUCT_TOTAL_COUNT'] > 0 && $arCurrentTariff['LIMIT_IMPORT_PRODUCTS'] < $arStore['PRODUCT_TOTAL_COUNT']) {

            $arTariff = \Local\Core\Model\Data\TariffTable::getList([
                'filter' => [
                    '>=LIMIT_IMPORT_PRODUCTS' => $arStore['PRODUCT_TOTAL_COUNT'],
                    'ACTIVE' => 'Y',
                    [
                        'LOGIC' => 'OR',
                        ['TYPE' => 'PUB'],
                        ['TYPE' => 'PER', 'PERSONAL_BY_STORE' => $intStoreId]
                    ]
                ],
                'order' => [
                    'LIMIT_IMPORT_PRODUCTS' => 'ASC',
                    'IS_ACTION' => 'DESC',
                    'PRICE_PER_TRADING_PLATFORM' => 'ASC',
                ],
                'limit' => 1,
                'offset' => 0
            ])
                ->fetch();

            if (!empty($arTariff) && $arStore['TARIFF_CODE'] != $arTariff['CODE']) {
                $arReturn = $arTariff;
            }
        }

        return $arReturn;
    }

    /**
     * Проверяет был ли ранее у магазина успешный импорт
     *
     * @param $intStoreId
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function hasSuccessImport($intStoreId)
    {
        $boolRes = false;
        $ar = self::__getStoreRegister($intStoreId);
        if( $ar['DATE_LAST_SUCCESS_IMPORT'] instanceof \Bitrix\Main\Type\DateTime )
        {
            if( $ar['PRODUCT_SUCCESS_IMPORT'] > 0 )
                $boolRes = true;
        }
        return $boolRes;
    }

    /**
     * Возвращает версию робофида последнего импорта
     *
     * @param $intStoreId
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getLastImportVersion($intStoreId)
    {
        $ar = self::__getStoreRegister($intStoreId);
        $intVersionId = $ar['LAST_IMPORT_VERSION'];
        return $intVersionId;
    }

    /**
     * Возвращает версию робофида последнего УСПЕШНОГО импорта
     *
     * @param $intStoreId
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getLastSuccessImportVersion($intStoreId)
    {
        $ar = self::__getStoreRegister($intStoreId);
        $intVersionId = $ar['LAST_SUCCESS_IMPORT_VERSION'];
        return $intVersionId;
    }

    /**
     * Получить ID пользователя врадельца магазина
     *
     * @param $intStoreId
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getOwnUserId($intStoreId)
    {
        return self::__getStoreRegister($intStoreId)['COMPANY_USER_OWN_ID'];
    }

    /**
     * Получить ID компании магазина
     *
     * @param $intStoreId
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getCompanyId($intStoreId)
    {
        return self::__getStoreRegister($intStoreId)['COMPANY_ID'];
    }
}