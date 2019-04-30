<?php

namespace Local\Core\Inner;


/**
 * Класс для работы с валютами
 *
 *
 * @package Local\Core\Inner
 */
class Currency
{

    /**
     * Получить сконвертироанную цену.<br/>
     * На выходе получаем либо сконвертированную стоимость, либо null, что означает ошибку в запросе к курсу.
     *
     * @param string $intPrice Цена
     * @param string $fromCode Код текущей валюты из справочника
     * @param string $toCode Код необходимой валюты из справочника
     * @param int $intRound Округление, по умолчанию 2
     *
     * @return float|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function convert($intPrice, $fromCode, $toCode, $intRound = 2)
    {
        $intNewPrice = null;

        if( $fromCode == $toCode )
        {
            $intNewPrice = round($intPrice, $intRound);
        }
        else
        {
            if( !is_null( $intPrice ) )
            {
                $intRate = \Local\Core\Inner\Currency::getRate($fromCode, $toCode);
                if( !is_null($intRate) )
                {
                    $intNewPrice = round($intPrice*$intRate, $intRound);
                }
            }
        }

        return $intNewPrice;
    }

    /**
     * Получить курс валют
     *
     * @param $fromCode
     * @param $toCode
     *
     * @return |null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getRate($fromCode, $toCode)
    {
        $intRate = null;

        $obCache = \Bitrix\Main\Application::getInstance()->getCache();

        if( $obCache->startDataCache(
            60*10,
                __METHOD__.__LINE__,
            \Local\Core\Inner\Cache::getCachePath(['Inner', 'Currency', 'getRate'], [$fromCode.'_'.$toCode])
        ) )
        {
            $obHttp = new \Bitrix\Main\Web\HttpClient();
            $obHttp->get('https://free.currconv.com/api/v7/convert?q='.$fromCode.'_'.$toCode.'&compact=ultra&apiKey='.\Bitrix\Main\Config\Configuration::getInstance()->get('currencyconverterapi')['apikey']);
            if( $obHttp->getStatus() == 200 )
            {
                $intRate = json_decode($obHttp->getResult(), true)[$fromCode.'_'.$toCode];
            }

            if( !empty( $intRate ) )
            {
                $ar = \Local\Core\Model\Data\CurrencyRateTable::getList([
                    'filter' => [
                        'CURRENCY_FROM' => $fromCode,
                        'CURRENCY_TO' => $toCode,
                    ],
                    'select' => ['ID']
                ])->fetch();
                if( !empty( $ar['ID'] ) )
                {
                    \Local\Core\Model\Data\CurrencyRateTable::update($ar['ID'], ['RATE' => $intRate]);
                }
                else
                {
                    \Local\Core\Model\Data\CurrencyRateTable::add([
                        'CURRENCY_FROM' => $fromCode,
                        'CURRENCY_TO' => $toCode,
                        'RATE' => $intRate
                    ]);
                }
            }
            else
            {
                $ar = \Local\Core\Model\Data\CurrencyRateTable::getList([
                    'filter' => [
                        'CURRENCY_FROM' => $fromCode,
                        'CURRENCY_TO' => $toCode,
                    ],
                    'select' => ['ID', 'RATE']
                ])->fetch();
                if( !empty( $ar['RATE'] ) )
                {
                    $intRate = $ar['RATE'];
                }
            }

            if( empty( $intRate ) )
            {
                $obCache->abortDataCache();
            }
            else
            {
                $obCache->endDataCache($intRate);
            }
        }
        else
        {
            $intRate = $obCache->getVars();
        }

        return $intRate;
    }
}