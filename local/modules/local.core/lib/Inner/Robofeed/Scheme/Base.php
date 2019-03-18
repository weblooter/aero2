<?php

namespace Local\Core\Inner\Robofeed\Scheme;

use \Local\Core\Inner\Robofeed\SchemeFields, \Local\Core\Inner\Exception;

/**
 * Базовый класс для работы со схемой Robofeed
 */
class Base
{

    /**
     * Получить версию карты робофида
     *
     * @param int $intVersion Версия Робофида
     *
     * @return array
     * @throws Exception\FatalException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getSchemaMap($intVersion)
    {

        $arSchema = self::getSchemeRoot();

        $arSchema['robofeed'] = array_merge($arSchema['robofeed'], self::getSchemaBodyByVersion($intVersion));

        $arMap = $arSchema;

        return $arMap;
    }

    /**
     * Получает ROOT схемы
     *
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    private static function getSchemeRoot()
    {
        return [
            'robofeed' => [
                '@attr' => [
                    'date' => new SchemeFields\DatetimeField(
                        'robofeed__@date', [
                            'title' => 'Дата создания фида',
                            'required' => true,
                            'xml_path' => 'robofeed->@date'
                        ]
                    ),
                    'version' => new SchemeFields\EnumField(
                        'robofeed__@version', [
                            'title' => 'Версия фида',
                            'required' => true,
                            'values' => [
                                '1'
                            ],
                            'xml_path' => 'robofeed->@version'
                        ]
                    )
                ]
            ]
        ];
    }

    /**
     * Получает BODY схемы по версии
     *
     * @param integer $intVersion Номер версии
     *
     * @return array
     * @throws Exception\FatalException
     */
    private static function getSchemaBodyByVersion($intVersion)
    {

        $className = '\Local\Core\Inner\Robofeed\Scheme\V'.$intVersion.'\SchemeBody';

        if( !class_exists($className) )
        {
            throw new Exception\FatalException('Класса "'.$className.'" не существует!');
        }

        if( !method_exists($className, 'getSchemeBody') )
        {
            throw new Exception\FatalException('Метода "'.$className.'::getSchemeBody()" не существует!');
        }


        return $className::getSchemeBody();
    }

    /**
     * Проверяет структуру на соответствие версии
     *
     * @oaram string $strFilePath Абсолютный путь до файла робофида
     *
     * @param integer $intVersion Номер версии
     *
     * @return \Bitrix\Main\Result
     * @throws Exception\FatalException
     * @throws \Bitrix\Main\SystemException
     */
    public static function checkStructureByVersion($strFilePath, $intVersion)
    {
        $obResult = new \Bitrix\Main\Result();

        $arScheme = self::getSchemaMap($intVersion);

        dump($arScheme);

        return $obResult;
    }
}