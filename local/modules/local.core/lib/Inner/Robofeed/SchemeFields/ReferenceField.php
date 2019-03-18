<?php

namespace Local\Core\Inner\Robofeed\SchemeFields;

use Local\Core\Inner\Cache;

/**
 * Field для связи со справочниками
 *
 * @package Local\Core\Inner\Robofeed\SchemeFields
 */
class ReferenceField extends ScalarField
{
    private $strReferenceClass;
    private $strReferenceColumnName = 'CODE';
    protected $xml_expected_type = 'значение из справочника';

    public function __construct($name, $parameters = array())
    {
        parent::__construct($name, $parameters);

        if( isset($parameters['class']) )
        {
            $this->strReferenceClass = $parameters['class'];
        }

        if( isset($parameters['column_name']) )
        {
            $this->strReferenceColumnName = $parameters['column_name'];
        }
    }

    public function getValidators()
    {
        $validators[] = function($value, $primary, $row, $obField)
            {
                if( $value === '' )
                {
                    if( $this->isRequired() )
                    {
                        return new \Bitrix\Main\ORM\Fields\FieldError($this, '', 'LOCAL_CORE_FIELD_IS_REQUIRED');
                    }
                }
                else
                {

                    if( !class_exists($this->strReferenceClass) )
                    {
                        return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE_REF_CLASS_NOT_EXIST');
                    }

                    $arValues = self::__getOrmValues($this->strReferenceClass, $this->strReferenceColumnName);
                    if( !in_array($value, $arValues) )
                    {
                        return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_REF_INVALID_VALUE');
                    }

                }

                return true;
            };

        return $validators;
    }

    public function getValidValue($mixEnterValue)
    {

        if( $mixEnterValue === '' )
        {
            $mixEnterValue = null;
        }

        return $mixEnterValue;
    }

    /**
     * Возвращает значения
     *
     * @param string $strClass      Класс Model\Reference
     * @param string $strColumnName Код извлекаемой колонки
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __getOrmValues(string $strClass, string $strColumnName)
    {

        $arResult = [];

        if( !class_exists($strClass) )
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
}