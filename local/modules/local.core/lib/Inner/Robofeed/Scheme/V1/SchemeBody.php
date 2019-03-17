<?php

namespace Local\Core\Inner\Robofeed\Scheme\V1;

use Local\Core\Inner\Robofeed\SchemeFields;

/**
 * Класс описывающий схему Fobofeed версии 1
 *
 * @package Local\Core\Inner\Robofeed\Base\V1
 */
class SchemeBody
{
    /**
     * Формирует и возвращает тело схемы
     *
     * @return array
     */
    public static function getSchemeBody()
    {
        return [
            'categories' => self::getSchemaCategories()
        ];
    }

    private static function getSchemaCategories()
    {
        return [
            'category' => [
                '@attr' => [
                    'id' => new SchemeFields\StringField(
                        'categories__category__@id', [
                            'required' => true,
                            'title' => 'ID категории',
                            'format' => '/[0-9]{1,9}/'
                        ]
                    ),
                    'parentId' => new SchemeFields\StringField(
                        'categories__category__@parentId', [
                            'required' => false,
                            'title' => 'ID родительской категории',
                            'format' => '/[0-9]{1,9}/'
                        ]
                    )
                ]
            ]
        ];
    }

    private static function getSchemaDefaultValues()
    {

    }

    private static function getSchemaOffers()
    {

    }

    public static function getXmlExample()
    {
        return file_get_contents(__DIR__.'/example.xml');
    }
}