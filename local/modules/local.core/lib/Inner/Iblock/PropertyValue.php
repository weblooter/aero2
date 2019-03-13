<?php

namespace Local\Core\Inner\Iblock;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main;
use Local\Core\Assistant\Arrays;
use Local\Core\Inner\CollectableEntity;

Loc::loadMessages(__FILE__);

class PropertyValue extends CollectableEntity
{
    /**
     * Создать массив коллекций свойств элементов
     *
     * @param ElementCollection $collection
     * @param bool              $select
     *
     * @return array
     */
    public static function getCollectionArrayByIblockElementCollection(ElementCollection $collection, $select = false)
    {
        if( !$select )
        {
            return [];
        }

        $arr_props = [];

        $iblock_id = 0;

        $collections = [];

        /** @var $item IblockElement */
        foreach( $collection as $item )
        {
            if( !$iblock_id )
            {
                $iblock_id = $item->getIblockId();
            }

            $arr_props[$item->getId()] = false;
        }

        $filter = array(
            'IBLOCK_ID' => $iblock_id,
            'ACTIVE' => 'Y',
            'ACTIVE_DATE' => 'Y',
            'CHECK_PERMISSIONS' => 'N'
        );

        $filter_props = [
            'ACTIVE' => 'Y'
        ];

        if( !in_array('*', $select) )
        {
            foreach( $select as $p )
            {
                if( is_scalar($p) )
                {
                    if( intval($p) > 0 )
                    {
                        $filter_props['ID'][] = intval($p);
                    }
                    else
                    {
                        $filter_props['CODE'][] = $p;
                    }
                }
            }
        }

        \CIBlockElement::GetPropertyValuesArray($arr_props, $iblock_id, $filter, $filter_props);

        foreach( $arr_props as $element_id => $properties )
        {
            foreach( $properties as $prop )
            {
                if( !isset($collections[$element_id]) )
                {
                    $collections[$element_id] = PropertyValueCollection::create();
                }

                $item = self::create($prop);

                $item->setCollection($collections[$element_id]);

                $collections[$element_id]->addItem($item);
            }
        }

        return $collections;
    }

    /**
     * Фабричный метод создания объекта элемента текущего класса
     *
     * @param array $fields
     *
     * @return PropertyValue
     */
    protected static function create(array $fields = array())
    {
        return new self($fields);
    }

    /**
     * PropertyValue constructor.
     *
     * @param array $fields
     */
    protected function __construct(array $fields = array())
    {
        $this->processProperty($fields);

        parent::__construct($fields);
    }

    /**
     * @inheritdoc
     */
    public static function getAvailableFields()
    {
        return array(
            'ID',
            'IBLOCK_ID',
            'NAME',
            'ACTIVE',
            'SORT',
            'CODE',
            'DEFAULT_VALUE',
            'PROPERTY_TYPE',
            'ROW_COUNT',
            'COL_COUNT',
            'LIST_TYPE',
            'MULTIPLE',
            'XML_ID',
            'FILE_TYPE',
            'MULTIPLE_CNT',
            'LINK_IBLOCK_ID',
            'WITH_DESCRIPTION',
            'SEARCHABLE',
            'FILTRABLE',
            'IS_REQUIRED',
            'VERSION',
            'USER_TYPE',
            'USER_TYPE_SETTINGS',
            'HINT',
            'VALUE_ENUM',
            'VALUE_XML_ID',
            'VALUE_SORT',
            'VALUE',
            'PROPERTY_VALUE_ID',
            'DESCRIPTION',
        );
    }

    /**
     * Обработать поля свойств перед созданием объекта класса
     *
     * @param array $fields
     */
    protected function processProperty(array &$fields)
    {
        Arrays::clearKeyTilda($fields);

        $fields['ID'] = (int)$fields['ID'];
        $fields['IBLOCK_ID'] = (int)$fields['IBLOCK_ID'];
        $fields['ROW_COUNT'] = (int)$fields['ROW_COUNT'];
        $fields['COL_COUNT'] = (int)$fields['COL_COUNT'];
        $fields['MULTIPLE_CNT'] = (int)$fields['MULTIPLE_CNT'];
        $fields['LINK_IBLOCK_ID'] = (int)$fields['LINK_IBLOCK_ID'];
        $fields['PROPERTY_VALUE_ID'] = (int)$fields['PROPERTY_VALUE_ID'];
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->getField('ID');
    }

    /**
     * @return null|string
     */
    public function getIblockId()
    {
        return $this->getField('IBLOCK_ID');
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->getField('NAME');
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getField('ACTIVE') == 'Y';
    }

    /**
     * @return null|string
     */
    public function getSort()
    {
        return $this->getField('SORT');
    }

    /**
     * @return null|string
     */
    public function getCode()
    {
        return $this->getField('CODE');
    }

    /**
     * @return null|string
     */
    public function getDefaultValue()
    {
        return $this->getField('DEFAULT_VALUE');
    }

    /**
     * @return null|string
     */
    public function getPropertyType()
    {
        return $this->getField('PROPERTY_TYPE');
    }

    /**
     * @return null|string
     */
    public function getRowCount()
    {
        return $this->getField('ROW_COUNT');
    }

    /**
     * @return null|string
     */
    public function getColCount()
    {
        return $this->getField('COL_COUNT');
    }

    /**
     * @return null|string
     */
    public function getListType()
    {
        return $this->getField('LIST_TYPE');
    }

    /**
     * @return bool
     */
    public function isMultiple()
    {
        return $this->getField('MULTIPLE') == 'Y';
    }

    /**
     * @return null|string
     */
    public function getXmlId()
    {
        return $this->getField('XML_ID');
    }

    /**
     * @return null|string
     */
    public function getFileType()
    {
        return $this->getField('FILE_TYPE');
    }

    /**
     * @return null|string
     */
    public function getMultipleCnt()
    {
        return $this->getField('MULTIPLE_CNT');
    }

    /**
     * @return null|string
     */
    public function getLinkIblockId()
    {
        return $this->getField('LINK_IBLOCK_ID');
    }

    /**
     * @return null|string
     */
    public function getWithDescription()
    {
        return $this->getField('WITH_DESCRIPTION');
    }

    /**
     * @return null|string
     */
    public function getSearchable()
    {
        return $this->getField('SEARCHABLE');
    }

    /**
     * @return null|string
     */
    public function getFiltrable()
    {
        return $this->getField('FILTRABLE');
    }

    /**
     * @return null|string
     */
    public function getIsRequired()
    {
        return $this->getField('IS_REQUIRED');
    }

    /**
     * @return null|string
     */
    public function getVersion()
    {
        return $this->getField('VERSION');
    }

    /**
     * @return null|string
     */
    public function getUserType()
    {
        return $this->getField('USER_TYPE');
    }

    /**
     * @return null|string
     */
    public function getUserTypeSettings()
    {
        return $this->getField('USER_TYPE_SETTINGS');
    }

    /**
     * @return null|string
     */
    public function getHint()
    {
        return $this->getField('HINT');
    }

    /**
     * @return null|string
     */
    public function getValueEnum()
    {
        return $this->getField('VALUE_ENUM');
    }

    /**
     * @return null|string
     */
    public function getValueXmlId()
    {
        return $this->getField('VALUE_XML_ID');
    }

    /**
     * @return null|string
     */
    public function getValueSort()
    {
        return $this->getField('VALUE_SORT');
    }

    /**
     * @return null|string
     */
    public function getValue()
    {
        return $this->getField('VALUE');
    }

    /**
     * @return null|string
     */
    public function getValueId()
    {
        return $this->getField('PROPERTY_VALUE_ID');
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->getField('DESCRIPTION');
    }

}