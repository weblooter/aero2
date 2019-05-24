<?php


namespace Local\Core\Inner\TaxonomyData;

/**
 * Базовый класс для работы с данными Таксаномии
 *
 * @package Local\Core\Inner\TaxonomyData
 */
class Base
{
    /**
     * Извлекает данные по экшену.<br/>
     * В случае нахождения экшена вернет массив, в противном случае - null.
     *
     * @param string $action Экшен
     *
     * @return array|null
     */
    public static function getData($action)
    {
        switch ($action)
        {
            case 'AutoruCategory':
                return AutoruCategory::getData();
                break;

            case 'AutoruMarkModel':
                return AutoruMarkModel::getData();
                break;

            case 'GoogleMerchantCategory':
                return GoogleMerchantCategory::getData();
                break;

            default:
                return null;
                break;
        }
    }

    /**
     * Конвертирует массив структуры таксономии в массив формата 'значение' => 'текст значения'
     *
     * @param        $arEnter
     * @param string $strDelimiter
     *
     * @return array
     */
    public static function convertData($arEnter, $strDelimiter = ' / ')
    {
        $arReturn = [];
        static::runColumnLevel($arEnter, $arReturn, [], $strDelimiter);
        return $arReturn;
    }

    protected static function runColumnLevel($ar, &$arReturned, $arChainTitle = [], $strDelimiter = ' / ')
    {
        foreach ($ar as $arLvl)
        {
            if( $arLvl['DISABLED'] !== true )
            {
                $arReturned[ $arLvl['VALUE'] ?? $arLvl['TITLE'] ] = implode( $strDelimiter, array_merge($arChainTitle, [$arLvl['TITLE']] ) );
            }

            if( !empty( $arLvl['CHILD'] ) )
            {
                static::runColumnLevel($arLvl['CHILD'], $arReturned, array_merge($arChainTitle, [$arLvl['TITLE']]), $strDelimiter);
            }
        }
    }
}