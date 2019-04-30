<?php

namespace Local\Core\Inner\TradingPlatform;

/**
 * Базовый класс для работы с торговыми площадками
 *
 * @package Local\Core\Inner\TradingPlatform
 */
class Base
{
    /* ****** */
    /* ACCESS */
    /* ****** */

    /** @var array $__register Регистр ТП */
    private static $__register = [];

    /**
     * Заполняет регистр ТП
     *
     * @param integer $intTradingPlatformId ID ТП
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __fillTpRegister($intTradingPlatformId)
    {
        if (is_null(self::$__register[$intTradingPlatformId])) {
            $arTmp = \Local\Core\Model\Data\TradingPlatformTable::getList([
                'filter' => ['ID' => $intTradingPlatformId],
                'select' => [
                    'ID',
                    'ACTIVE',
                    'NAME',
                    'CODE',
                    'STORE_ID',
                    'HANDLER',
                    'PAYED_TO',
                    'STORE_DATA_' => 'STORE'
                ]
            ])
                ->fetch();

            $ar = [
                'ID' => $arTmp['ID'],
                'ACTIVE' => $arTmp['ACTIVE'],
                'NAME' => $arTmp['NAME'],
                'CODE' => $arTmp['CODE'],
                'STORE_ID' => $arTmp['STORE_ID'],
                'HANDLER' => $arTmp['HANDLER'],
                'PAYED_TO' => $arTmp['PAYED_TO'],
                'STORE_DATA_COMPANY_ID' => $arTmp['STORE_DATA_COMPANY_ID']
            ];

            self::$__register[$intTradingPlatformId] = $ar;
        }
    }

    /**
     * Возвращает регистр магазинов
     *
     * @param integer $intTradingPlatformId ID магазина
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __getTpRegister($intTradingPlatformId)
    {
        self::__fillTpRegister($intTradingPlatformId);
        return self::$__register[$intTradingPlatformId];
    }


    const ACCESS_TP_IS_MINE = 0x001;
    const ACCESS_TP_NOT_FOUND = 0x002;
    const ACCESS_TP_NOT_MINE = 0x003;

    /**
     * Проверяет права на ТП.<br/>
     * Результат необходимо сравнить с константами класса:<br/>
     * <ul>
     * <li>ACCESS_TP_IS_MINE</li>
     * <li>ACCESS_TP_NOT_FOUND</li>
     * <li>ACCESS_TP_NOT_MINE</li>
     * </ul>
     *
     * @param integer $intTradingPlatformId ID ТП
     * @param integer $intUserId            ID пользователя
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function checkUserAccess($intTradingPlatformId, $intUserId = 0)
    {
        if ($intUserId < 1) {
            $intUserId = $GLOBALS['USER']->GetID();
        }

        $ar = self::__getTpRegister($intTradingPlatformId);

        if (!empty($ar)) {

            switch (\Local\Core\Inner\Company\Base::checkUserAccess($ar['STORE_DATA_COMPANY_ID'], $intUserId)) {
                case \Local\Core\Inner\Company\Base::ACCESS_COMPANY_IS_MINE:
                    return self::ACCESS_TP_IS_MINE;
                    break;
                default:
                    return self::ACCESS_TP_NOT_MINE;
                    break;
            }
        } else {
            return self::ACCESS_TP_NOT_FOUND;
        }

    }

    /* ****** */
    /* GETTER */
    /* ****** */


    /**
     * Получить название ТП
     *
     * @param $intTradingPlatformId
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getName($intTradingPlatformId)
    {
        return self::__getTpRegister($intTradingPlatformId)['NAME'];
    }

    /**
     * Получить код обработчика
     *
     * @param $intTradingPlatformId
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getHandlerCode($intTradingPlatformId)
    {
        return self::__getTpRegister($intTradingPlatformId)['HANDLER'];
    }

    /**
     * Получить название обработчика
     *
     * @param $intTradingPlatformId
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getHandlerTitle($intTradingPlatformId)
    {
        return \Local\Core\Inner\TradingPlatform\Factory::getFactoryList()[self::__getTpRegister($intTradingPlatformId)['HANDLER']];
    }

    /**
     * Получение ссылки на файл экспорта
     *
     * @param $intTradingPlatformId
     *
     * @return string|null
     */
    public static function getExportFileLink($intTradingPlatformId)
    {
        $strReturn = null;

        try {
            $strCode = self::__getTpRegister($intTradingPlatformId)['CODE'];
            $strExportFileFormat = \Local\Core\Inner\TradingPlatform\Factory::factory(self::__getTpRegister($intTradingPlatformId)['HANDLER'])
                ->getExportFileFormat();
            $strReturn = (\Bitrix\Main\Config\Configuration::getInstance()->get('tradingplatform')['export']['export_dir'] ?? '/upload/tradingplatform/export').'/'.self::getStoreIdByTpId($intTradingPlatformId).'/'.$strCode.'.'.$strExportFileFormat;
        } catch (\Throwable $e) {

        }

        return $strReturn;
    }

    /**
     * Получить ID магазина
     *
     * @param $intTradingPlatformId
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getStoreIdByTpId($intTradingPlatformId)
    {
        return self::__getTpRegister($intTradingPlatformId)['STORE_ID'];
    }

    /**
     * Активация торговой площадки с проверкой возможности активации и списании баланса
     *
     * @param $intTradingPlatformId
     *
     * @return \Bitrix\Main\Result
     */
    public static function activate($intTradingPlatformId)
    {
        $obResult = new \Bitrix\Main\Result();

        try {
            $arTp = self::__getTpRegister($intTradingPlatformId);
            if ($arTp['ACTIVE'] == 'Y') {
                throw new \Exception('Торговая площадка уже активирована.');
            }

            if (!\Local\Core\Inner\Store\Base::hasSuccessImport($arTp['STORE_ID'])) {
                throw new \Exception('У магазина не было еще ни одного успешного импорта.');
            }

            $obTp = new \Local\Core\Inner\TradingPlatform\TradingPlatform();
            $obTp->load($intTradingPlatformId);
            $obHandler = $obTp->getHandler();
            $obCheckRes = $obHandler->isRulesTradingPlatformCorrectFilled();
            if (!$obCheckRes->isSuccess()) {
                throw new \Exception('В торговой площадке не заполнены все обязательные поля.');
            }

            if(
                !$arTp['PAYED_TO'] instanceof \Bitrix\Main\Type\DateTime
                || (
                    $arTp['PAYED_TO'] instanceof \Bitrix\Main\Type\DateTime
                    && $arTp['PAYED_TO']->getTimestamp() <= strtotime('now')
                )
            )
            {
                $obPayResult = \Local\Core\Inner\Balance\Base::payTradingPlatform($intTradingPlatformId);
                if( !$obPayResult->isSuccess() )
                {
                    $obResult->addErrors($obPayResult->getErrors());
                    throw new \Exception();
                }
                else
                {
                    $strAddTime = 'now + 1 month';
                    if( \Local\Core\Inner\Tariff\Base::getDefaultTariff()['CODE'] == \Local\Core\Inner\Store\Base::getTariffCode(\Local\Core\Inner\TradingPlatform\Base::getStoreIdByTpId($intTradingPlatformId)) )
                    {
                        $strAddTime = 'now + 7 day';
                    }

                    // Списание произошло, активация
                    \Local\Core\Model\Data\TradingPlatformTable::update(
                        $intTradingPlatformId,
                        [
                            'ACTIVE' => 'Y',
                            'PAYED_FROM' => new \Bitrix\Main\Type\DateTime(date('Y.m.d'), 'Y.m.d'),
                            'PAYED_TO' => new \Bitrix\Main\Type\DateTime(date('Y.m.d' , strtotime($strAddTime)), 'Y.m.d'),
                        ]
                    );

                }
            }
            elseif (
                $arTp['PAYED_TO'] instanceof \Bitrix\Main\Type\DateTime
                && $arTp['PAYED_TO']->getTimestamp() > strtotime('now')
            )
            {
                // Простая активация, тп уже была оплачена ранее и дата окончания еще не прошла
                \Local\Core\Model\Data\TradingPlatformTable::update(
                    $intTradingPlatformId,
                    ['ACTIVE' => 'Y']
                );
            }

        } catch (\Throwable $e) {
            if (!empty($e->getMessage())) {
                $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
            }
        }

        return $obResult;
    }
}