<?php

namespace Local\Core\Inner\BxModified\Main\ORM\Data;


class DataManager extends \Bitrix\Main\ORM\Data\DataManager
{
    /**
     * Хранилище значений для полей Enum. Первый ключ - название поля.
     * Пример заполнения:<br/>
     * [<br/>
     * &emsp;'GROUP' => [<br/>
     * &emsp;&emsp;'ECONOM' => 'Экономические единицы',<br/>
     * &emsp;&emsp;'TIME' => 'Единицы времени',<br/>
     * &emsp;&emsp;'LENGTH' => 'Единицы длины',<br/>
     * &emsp;&emsp;'WEIGHT' => 'Единицы массы',<br/>
     * &emsp;&emsp;'VOLUME' => 'Единицы объема',<br/>
     * &emsp;&emsp;'AREA' => 'Единицы площади'<br/>
     * &emsp;]<br/>
     * ]<br/>
     * <br/>
     * Поле ACTIVE заполнено по умолчанию, дублировать не имеет смысла.<br/>
     *
     * @var array $arEnumFieldsValues
     */
    public static $arEnumFieldsValues = [];

    /**
     * Получает значения полей Enum в формате <b>VALUE => TEXT</b>.<br/>
     * Для этого необходимо определить <b>public static $arEnumFieldsValues</b> в ORM.<br/>
     * Пример заполнения $arEnumFieldsValues :<br/>
     * [<br/>
     * &emsp;'GROUP' => [<br/>
     * &emsp;&emsp;'ECONOM' => 'Экономические единицы',<br/>
     * &emsp;&emsp;'TIME' => 'Единицы времени',<br/>
     * &emsp;&emsp;'LENGTH' => 'Единицы длины',<br/>
     * &emsp;&emsp;'WEIGHT' => 'Единицы массы',<br/>
     * &emsp;&emsp;'VOLUME' => 'Единицы объема',<br/>
     * &emsp;&emsp;'AREA' => 'Единицы площади'<br/>
     * &emsp;]<br/>
     * ]<br/>
     * <br/>
     * Поле ACTIVE заполнено по умолчанию, дублировать не имеет смысла.<br/>
     *
     * @param string $strField Код поля
     *
     * @return array
     */
    public static function getEnumFieldHtmlValues($strField)
    {
        $arValues = static::$arEnumFieldsValues[$strField] ?? [];
        if( empty($arValues) && $strField == 'ACTIVE' )
        {
            $arValues = [
                'Y' => 'Да',
                'N' => 'Нет'
            ];
        }

        $arReturn = $arValues;
        return $arReturn;
    }

    /**
     * Получает только значения полей Enum.<br/>
     * Для получения значений и text обращаться к getEnumFieldHtmlValues()<br/>
     * <br/>
     * Поле ACTIVE заполнено по умолчанию, дублировать не имеет смысла.<br/>
     *
     * @param string $strField Код поля
     *
     * @return array
     */
    public static function getEnumFieldValues($strField)
    {
        $arReturn = [];
        if( is_array(static::getEnumFieldHtmlValues($strField)) )
        {
            $arReturn = array_keys(static::getEnumFieldHtmlValues($strField));
        }
        return $arReturn;
    }


    /**
     * Обновим поле DATE_MODIFIED
     *
     * @param \Bitrix\Main\ORM\Event       $event
     * @param \Bitrix\Main\ORM\EventResult $result
     * @param array                        $arModifiedFields
     *
     * @throws \Bitrix\Main\ObjectException
     */
    protected static function _OnBeforeUpdateBase(\Bitrix\Main\ORM\Event &$event, \Bitrix\Main\ORM\EventResult &$result, &$arModifiedFields)
    {

        /** @var \Bitrix\Main\ORM\Event $event */
        $arFields = $event->getParameter('fields');

        if( !empty($arFields) )
        {
            $arModifiedFields['DATE_MODIFIED'] = new \Bitrix\Main\Type\DateTime();
        }

        $arFields = array_merge($arFields, $arModifiedFields);
        $event->setParameter('fields', $arFields);

        $result->modifyFields($arModifiedFields);
    }

    /**
     * Скинем кэши компонентов
     *
     * @param \Bitrix\Main\ORM\Event $event
     * @param array                  $arAdditionalParams Дополнительные параметры, которые смержатся и передадутся в clearComponentsCache() в $arFields
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected static function _initClearComponentCache(\Bitrix\Main\ORM\Event &$event, $arAdditionalParams = [])
    {
        /** @var \Bitrix\Main\ORM\Event $event */
        $arEventParams = $event->getParameters();
        if( !empty($arEventParams['primary']['ID']) )
        {
            $ar = static::getById($arEventParams['primary']['ID'])
                ->fetchRaw();

            if( !empty($arAdditionalParams) )
            {
                $ar = array_merge($ar, $arAdditionalParams);
            }

            static::clearComponentsCache($ar);
        }
    }

    /**
     * Метод чистит кэши компонентов, в которых используется данный класс ORM.<br/>
     * Пример:<br/>
     * <code>
     * \Local\Core\Inner\Cache::deleteComponentCache(['personal.company.list'], [ 'user_id='.$arFields['USER_OWN_ID'] ]);
     * </code>
     *
     * @param array $arFields Массив полей getById() данного ORM
     */
    public static function clearComponentsCache($arFields)
    {
    }
}