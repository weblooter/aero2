<?php

namespace Local\Core\Inner\Robofeed\Schema;

use \Local\Core\Inner\Robofeed\SchemaFields, \Local\Core\Inner\Exception;

abstract class AbstractSchema
{
    use \Local\Core\Inner\Robofeed\Traites\AbstractClass;

    /**
     * Получить версию карты робофида
     *
     * @param int $intVersion Версия Робофида
     *
     * @return array
     * @throws Exception\FatalException
     * @throws \Bitrix\Main\SystemException
     */
    public function getSchemaMap()
    {

        $arSchema = $this->getSchemeRoot();

        $arSchema['robofeed'] = array_merge($arSchema['robofeed'], static::getSchemaBody());

        $arMap = $arSchema;

        return $arMap;
    }

    /**
     * Получает ROOT схемы
     *
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    private function getSchemeRoot()
    {
        return [
            'robofeed' => [
                'lastModified' => new SchemaFields\DatetimeField('robofeed__lastModified', [
                        'title' => 'Дата создания робофида',
                        'required' => true,
                        'xml_path' => 'robofeed->lastModified'
                    ]),
                'version' => new SchemaFields\EnumField('robofeed__version', [
                        'title' => 'Номер версии робофида',
                        'required' => true,
                        'values' => [
                            '1'
                        ],
                        'xml_path' => 'robofeed->version'
                    ])
            ]
        ];
    }

    /**
     * Формирует и возвращает тело схемы
     *
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    abstract protected static function getSchemaBody();
}