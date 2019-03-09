<?php

namespace Local\Core\Inner\Iblock;

use Bitrix\Iblock\Component\Tools;
use Bitrix\Iblock\InheritedProperty\ElementValues;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main;
use Local\Core\Assistant\Arrays;
use Local\Core\Inner\CollectableEntity;
use Local\Core\Inner\File;
use Local\Core\Inner\FileCollection;

Loc::loadMessages(__FILE__);

/**
 * Class Element
 * @package Local\Core\Inner\Iblock
 */
class Element extends CollectableEntity
{
    /**
     * @var bool Если true - загружаются подчиненные объекты лениво, по факту обращения к ним, иначе загружается сразу все
     */
    protected $lazy = false;

    protected $iblockId;

    protected $propertyCollection;

    /**
     * Получить обработанный массив параметров сортировки
     * @param array $sort Массив параметров сортировки
     * @return array
     */
    protected static function getSort(array $sort = [])
    {
        $default = false;

        if (!empty($sort)) {
            return $sort;
        }

        return $default;
    }

    /**
     * Получить дефолтный массив параметров выборки дополенный массивом $filter
     * @param array $filter Пользовательски условия фильтрации
     * @return array
     */
    protected static function getFilter(array $filter = [])
    {
        if (!((int)$filter['IBLOCK_ID'])) {
            throw new Main\ArgumentNullException('В фильтре не указан обязательный параметр IBLOCK_ID');
        }

        $default = array(
            'IBLOCK_LID' => SITE_ID,
            'ACTIVE_DATE' => 'Y',
            'CHECK_PERMISSIONS' => 'Y',
            'MIN_PERMISSION' => 'R',
        );

        if (!empty($filter)) {
            if (isset($filter['SECTION_ID'])) {
                $filter['INCLUDE_SUBSECTIONS'] = $filter['INCLUDE_SUBSECTIONS'] === 'N' ? 'N' : 'Y';

                $filter['SECTION_GLOBAL_ACTIVE'] = $filter['SECTION_GLOBAL_ACTIVE'] === 'N' ? 'N' : 'Y';
            }

            $default = array_merge($default, $filter);
        }
        return $default;
    }

    /**
     * Получить обработаннй массив параметров группировки
     * @param bool $arr_group
     * @return bool
     */
    protected static function getGroup($arr_group = false)
    {
        $default = false;

        if (!empty($arr_group)) {
            return $arr_group;
        }

        return $default;
    }

    /**
     * Получить обработанный массив параметров постраничной навигации
     * @param bool $arr_nav
     * @return array|bool
     */
    protected static function getNav($arr_nav = false)
    {
        $default = array();

        if (!empty($arr_nav)) {
            return $arr_nav;
        }

        return $default;
    }

    /**
     * Поулчить дефолтный массив выбираемых полей дополненый массивом $select
     * @param array $select
     * @return array
     */
    protected static function getSelect(array $select = [])
    {
        $default = array(
            'ID',
            'IBLOCK_ID',
            'CODE',
            'XML_ID',
            'NAME',
            'ACTIVE',
            'DATE_ACTIVE_FROM',
            'DATE_ACTIVE_TO',
            'SORT',
            'PREVIEW_TEXT',
            'PREVIEW_TEXT_TYPE',
            //'DETAIL_TEXT',
            //'DETAIL_TEXT_TYPE',
            //'DATE_CREATE',
            //'CREATED_BY',
            //'TAGS',
            //'TIMESTAMP_X',
            //'MODIFIED_BY',
            'IBLOCK_SECTION_ID',
            'DETAIL_PAGE_URL',
            //'DETAIL_PICTURE',
            'PREVIEW_PICTURE',
        );

        if (!empty($select)) {
            $default = array_merge($default, $select);
        }

        return $default;
    }

    /**
     * Получить массив кодов свойств из массива $select и очистить $select от элементов PROPERTY_
     * @param array $select
     * @return array|bool
     */
    protected static function getProperty(&$select = [])
    {
        if (!empty($select)) {
            $props = array_filter($select, function ($v) {
                return strpos($v, 'PROPERTY_') !== false;
            });

            $select = array_diff($select, $props);

            array_walk($props, function (&$v, $k) {
                $v = str_replace('PROPERTY_', '', $v);
            });

            return $props;
        }

        return false;
    }

    /**
     * Возвращает значение параметра, истинное значение которого говорит о том, что по умолчанию будут проинициализированны минимальный набор данных.
     * Ложное значение говорит о том, что все данные для инициализации сущностей будут загружены сразу при построении списка.
     * На данный момент работает только полная предварительная загрузка данных ($lazy = false)
     * @param bool $lazy
     * @return bool
     */
    protected static function getLazy($lazy = false)
    {
        return $lazy === 'N' ? false : (bool)$lazy;
    }

    /**
     * Получить элемент по ID.
     * @param int $id
     * @return ProductCollection
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentTypeException
     */
    public static function getById(int $id)
    {
        $id = (int)$id;
        if ($id <= 0) {
            throw new Main\ArgumentNullException('Не указан ID элмента');
        }

        return self::getList(['filter' => ['ID' => $id]]);
    }

    /**
     * Получить коллецию элементов по фильтру
     * @param array $params
     * @return ElementCollection
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentTypeException
     */
    public static function getList(array $params = [])
    {
        $property_fields        = self::getProperty($params['select']);
        $order_fields           = self::getSort((array)$params['order']);
        $filter_fields          = self::getFilter((array)$params['filter']);
        $group_fields           = self::getGroup((array)$params['group']);
        $nav_fields             = self::getNav($params['nav']);
        $select_fields          = self::getSelect((array)$params['select']);
        $lazy_init              = self::getLazy($params['lazy']);

        /** @var $elementIterator \CDBResult */
        $elementIterator = \CIBlockElement::GetList(
            $order_fields,
            $filter_fields,
            $group_fields,
            $nav_fields,
            $select_fields
        );

        /** @var $collection ElementCollection */
        $collection = ElementCollection::create();
        $collection->setPageNavParams($elementIterator);

        $arr_elements = [];

        while ($row = $elementIterator->GetNext()) {

            $arr_elements[$row['ID']] = false;

            /** @var $item Element */
            $item = self::create($row);

            $item->setCollection($collection);

            $collection->addItem($item);
        }

        if (!$lazy_init) {
            self::initProps($collection, $property_fields);
            self::initFiles($collection);
        }

        return $collection;
    }

    /**
     * Фабричный метод создания объекта класса
     * @param array $fields
     * @return Product
     */
    protected static function create(array $fields = array())
    {
        return new static($fields);
    }

    /**
     * Фабричный метод создания объекта класса реализующего "ленивую" подгрузку свойств
     * @param array $fields
     * @return Element
     */
    protected static function createLazy(array $fields = array())
    {
        return new static($fields, true);
    }

    /**
     * Element constructor.
     * @param array $fields
     * @param bool $lazy
     */
    protected function __construct(array $fields = array(), $lazy = false)
    {
        $this->lazy = $lazy;

        $this->processElement($fields);

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
            'CODE',
            'XML_ID',
            'NAME',
            'ACTIVE',
            'DATE_ACTIVE_FROM',
            'DATE_ACTIVE_TO',
            'SORT',
            'PREVIEW_TEXT',
            'PREVIEW_TEXT_TYPE',
            'DETAIL_TEXT',
            'DETAIL_TEXT_TYPE',
            'DATE_CREATE',
            'CREATED_BY',
            'TAGS',
            'TIMESTAMP_X',
            'MODIFIED_BY',
            'IBLOCK_SECTION_ID',
            'DETAIL_PAGE_URL',
            'DETAIL_PICTURE',
            'PREVIEW_PICTURE',
            'LANG_DIR',
            'EXTERNAL_ID',
            'IBLOCK_TYPE_ID',
            'IBLOCK_CODE',
            'IBLOCK_EXTERNAL_ID',
            'LID',
        );
    }

    /**
     * Обработать поля элемента перед созданием объекта класс
     * @param array $element
     * @throws Main\ObjectException
     */
    protected function processElement(array &$element)
    {
        Arrays::clearKeyTilda($element);

        $element['ID'] = (int)$element['ID'];
        $element['IBLOCK_ID'] = (int)$element['IBLOCK_ID'];

        $element['ACTIVE_FROM'] = (isset($element['DATE_ACTIVE_FROM']) ? new Main\Type\DateTime($element['DATE_ACTIVE_FROM'], 'd.m.Y H:i:s') : null);
        $element['ACTIVE_TO'] = (isset($element['DATE_ACTIVE_TO']) ? new Main\Type\DateTime($element['DATE_ACTIVE_TO'], 'd.m.Y H:i:s') : null);
        $element['DATE_CREATE'] = (isset($element['DATE_CREATE']) ? new Main\Type\DateTime($element['DATE_CREATE'], 'd.m.Y H:i:s') : null);
        $element['TIMESTAMP_X'] = (isset($element['TIMESTAMP_X']) ? new Main\Type\DateTime($element['TIMESTAMP_X'], 'd.m.Y H:i:s') : null);

        $ipropValues = new ElementValues($element['IBLOCK_ID'], $element['ID']);
        $element['IPROPERTY_VALUES'] = $ipropValues->getValues();

    }

    /**
     * Инициализировать коллекции свойств текущей выборки элементов
     * @param ElementCollection $collection
     * @param bool $select
     * @throws Main\ArgumentTypeException
     */
    protected static function initProps(ElementCollection $collection, $select = false)
    {
        $prop_collection = PropertyValue::getCollectionArrayByIblockElementCollection($collection, $select);

        if (!count($prop_collection)) {
            return;
        }

        foreach ($collection as $item) {
            if (isset($prop_collection[$item->getId()])) {
                $item->setPropertyCollection($prop_collection[$item->getId()]);
            }
        }
    }

    /**
     * Инициализировать все поля и свойства типа файл - создает объекты типа File и Image
     * @param ElementCollection $collection
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentTypeException
     */
    protected function initFiles(ElementCollection $collection)
    {
        $file_collection = File::getCollectionByElementCollection($collection);

        if (!$file_collection->count()) {
            return;
        }

        /** @var $item IblockElement */
        foreach ($collection as $item) {
            if ($file_id = (int)$item->getField('DETAIL_PICTURE')) {
                if ($file = $file_collection->getItemById($file_id)) {
                    $item->setField('DETAIL_PICTURE', $file);
                }
            }

            if ($file_id = (int)$item->getField('PREVIEW_PICTURE')) {
                if ($file = $file_collection->getItemById($file_id)) {
                    $item->setField('PREVIEW_PICTURE', $file);
                }
            }

            if ($prop_collection = $item->getPropertyCollection()) {
                /** @var $property IblockPropertyValue */
                foreach ($prop_collection as $property) {
                    if ($property->getPropertyType() == 'F') {
                        if ($property->isMultiple()) {
                            $values = array_filter(
                                array_map(function ($v) {
                                    return (int)$v;
                                }, (array)$property->getField('VALUE'))
                            );

                            if ($values) {
                                $item_file_collection = FileCollection::create();

                                foreach ($values as $file_id) {
                                    if ($file = $file_collection->getItemById($file_id)) {
                                        $item_file_collection->addItem($file);
                                        $file->setCollection($item_file_collection);
                                        $property->setField('VALUE', $item_file_collection);
                                    }
                                }
                            }

                        } else {
                            if ($value = (int)$property->getField('VALUE')) {
                                if ($file = $file_collection->getItemById($value)) {
                                    $property->setField('VALUE', $file);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    ### ### ### ### ###

    /**
     * Установить коллекцию свойств элемента
     * @param PropertyValueCollection $collection
     * @return PropertyValueCollection
     */
    public function setPropertyCollection(PropertyValueCollection $collection)
    {
        return $this->propertyCollection = $collection;
    }

    /**
     * Получить коллекцию свойств элемента
     * @return mixed
     */
    public function getPropertyCollection()
    {
        return $this->propertyCollection;
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->getField('ID');
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        $active = $this->getField('ACTIVE');
        return $active === 'Y' || $active === true;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->getField('NAME');
    }

    /**
     * @return null|string
     */
    public function getPreviewText()
    {
        return $this->getField('PREVIEW_TEXT');
    }

    /**
     * @return null|string
     */
    public function getPreviewTextType()
    {
        return $this->getField('DETAIL_TEXT_TYPE');
    }

    /**
     * @return null|string
     */
    public function getDetailText()
    {
        return $this->getField('DETAIL_TEXT');
    }

    /**
     * @return null|string
     */
    public function getDetailPicture()
    {
        return $this->getField('DETAIL_PICTURE');
    }

    /**
     * @return null|string
     */
    public function getPreviewPicture()
    {
        return $this->getField('PREVIEW_PICTURE');
    }

    /**
     * @return null|string
     */
    public function getDetailTextType()
    {
        return $this->getField('DETAIL_TEXT_TYPE');
    }

    /**
     * @return null|string
     */
    public function getDetailPageUrl()
    {
        return $this->getField('DETAIL_PAGE_URL');
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
    public function getIblockId()
    {
        return $this->getField('IBLOCK_ID');
    }

    /**
     * @return null|string
     */
    public function getCode()
    {
        return $this->getField('CODE');
    }

    /**
     * Создать клон текущего объекта
     * @param \SplObjectStorage $cloneEntity
     * @return CollectableEntity|Element|object
     */
    public function createClone(\SplObjectStorage $cloneEntity)
    {
        if ($this->isClone() && $cloneEntity->contains($this)) {
            return $cloneEntity[$this];
        }

        $productClone = clone $this;
        $productClone->isClone = true;

        /** @var Internals\Fields $fields */
        if ($fields = $this->fields) {
            $productClone->fields = $fields->createClone($cloneEntity);
        }

        if (!$cloneEntity->contains($this)) {
            $cloneEntity[$this] = $productClone;
        }

        if ($collection = $this->getCollection()) {
            if (!$cloneEntity->contains($collection)) {
                $cloneEntity[$collection] = $collection->createClone($cloneEntity);
            }

            if ($cloneEntity->contains($collection)) {
                $productClone->collection = $cloneEntity[$collection];
            }
        }

        //todo: не забывать расширять для новых составляющих объектов

        return $productClone;
    }

    /**
     * Создать хеш текущего элемента
     * @return string
     */
    public function getHash()
    {
        return md5($this->getId());
    }

    /**
     * <h1>Строит сложные запрос для элементов инфоблока</h1>
     * Позволяет создавать запросы с объединеним результатов нескольких фильтраций,<br/>
     * а также запросы с результирующим декартовым произведением нескольких фильтраций.<br/>
     *
     * В параметр $arFilters могут передаваться два массива:
     * <pre>
     * [
     *  #Строит декартовое произведение результатов нескольких фильтраций
     *  "CROSS" => [(array) arFilter, (array) arFilter2,...],
     *
     *  #Соединяет несколько результатов разных фильтров в один результат
     *  "UNION" => [(array) arFilter, (array) arFilter2,...],
     * ]
     * </pre>
     *
     * Параметр $SelectPlaceholders необходим для корректного составления поля для декартового произведения.<br/>
     * По умолчанию, все поля собираются через CONCAT с разделителем $delimiter.
     * Если, например, необходимо посчитать сумму по колонкам в разных результатах для цены, то
     * <pre>
     * $SelectPlaceholders = [
     *  "CATALOG_PRICE_136" => function ($arFields, $fieldName){
     *      return implode("+", $arFields) . " as {$fieldName}";
     *  },
     * ]
     * </pre>
     * где, CATALOG_PRICE_136 - название колоток с алиасом таблицы, $arFields - список полей из таблицы, например
     * <pre>
     * [
     *  "a0.CATALOG_PRICE_136",
     *  "a1.CATALOG_PRICE_136",
     *  "a2.CATALOG_PRICE_136",
     * ]
     * </pre>
     * $fieldName - название колонки (CATALOG_PRICE_136)
     *
     * <hr>
     * <h2>Область применения: Виртуальные колеса в сборе.</h2>
     * Необходимо отфильтровать диски со своим фильтром<br/>
     * Необходимо отфильтровать шины со своим фильтром<br/>
     * Получить декартово произведение шин и дисков<br/>
     * Добавить к результату реальные колеса в сборе в своим фильтром<br/>
     * Возможность сортировать весь результат
     *
     * @param array $arOrder
     * @param array $arFilters
     * @param bool $arNavStartParams
     * @param array $arSelectFields
     * @param array $SelectPlaceholders
     *
     * @return \CIBlockResult
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function GetListCross($arOrder = ["SORT" => "ASC"], $arFilters = [], $arNavStartParams = false, $arSelectFields = [], $SelectPlaceholders = [], $delimiter = "##")
    {
        if (empty($arFilters)) {
            return new \CIBlockResult();
        }
        if (empty($arSelectFields)) {
            $arSelectFields = ["*"];
        }

        $sqlSelect = "";
        $sqlOrder = "";
        $sqlList = [];
        $arFilterIBlocks = [];

        # Преобразовываем каждый фильтр в sql запрос
        foreach ($arFilters as $type => $typeList) {

            if (!in_array($type, ["CROSS", "UNION"])) {
                continue;
            }

            foreach ($typeList as $arFilter) {
                $ob = new \CIBlockElement();
                $ob->prepareSql($arSelectFields, $arFilter, false, $arOrder);

                $arFilterIBlocks = $arFilterIBlocks + $ob->arFilterIBlocks;

                if (empty($sqlSelect)) {
                    $sqlSelect = $ob->sSelect;
                }

                if (empty($sqlOrder)) {
                    # Добавляем в SELECT поля для сортировки, чтобы потом по ним можно было отсортировать весь результат
                    $sqlSelectAddOrderFields = [];
                    # Бьем поля на 2 группы ({алиас таблицы}.{название поля}) as ({название поля})
                    if (preg_match_all("/([^,\.\s]+\.([^,\.\s]+))/", $ob->sOrderBy, $sqlOrderMatches)) {

                        foreach ($sqlOrderMatches[1] as $key => $field) {
                            # Если значение {название поля) существует, и если этого значения ещё нет в SELECT
                            if (!isset($sqlOrderMatches[2][$key]) || preg_match("/[\s\,]as\s+{$sqlOrderMatches[2][$key]}[\s\,]/", $sqlSelect)) {
                                continue;
                            }
                            $sqlSelectAddOrderFields[] = "{$field} as {$sqlOrderMatches[2][$key]}";
                        }
                    }
                    if (!empty($sqlSelectAddOrderFields)) {
                        $sqlSelect .= ", " . implode(", ", $sqlSelectAddOrderFields);
                    }

                    # Используется для сортировки всего результата. Убираем алиасы таблиц для полей, так как они не нужны.
                    $sqlOrder = preg_replace("/([^.\s,]+\.)/", "", $ob->sOrderBy);
                }

                $sqlList[$type][] = "
                SELECT " . $sqlSelect . "
                FROM " . $ob->sFrom . "
                WHERE 1=1 " . $ob->sWhere . "
                " . $ob->sGroupBy . "
                " . $ob->sOrderBy . "
            ";

                unset($ob);
            }
        }

        if (empty($sqlList)) {
            throw new \Exception("Не получилось собрать запросы");
        }

        $strSql = "";

        # Добавляем CROSS JOIN запросы
        if (!empty($sqlList["CROSS"])) {

            # Подготавливаем общее поле SELECT для CROSS
            $arSelectCross = [];

            #Получим все названия колонок (...as {Название колонки}...)
            if (preg_match_all("/as\s([^,]+)/", $sqlSelect, $matches)) {
                $countCrossQueries = count($sqlList["CROSS"]) - 1;
                foreach ($matches[1] as $fieldName) {

                    # Для каждого CROSS запроса соберем отдельный префикс для названия колонки
                    $arSelectFieldQueries = [];
                    for ($i = 0; $i <= $countCrossQueries; $i++) {
                        $arSelectFieldQueries[] = "a{$i}.{$fieldName}";
                    }

                    # Если есть особая обработка слияния колонок
                    if (isset($SelectPlaceholders[$fieldName]) && is_callable($SelectPlaceholders[$fieldName])) {
                        $arSelectCross[] = call_user_func($SelectPlaceholders[$fieldName], $arSelectFieldQueries, $fieldName);
                    }
                    # Иначе собираем значения для колонки из всех CROSS запросов через разделитель
                    else {
                        $arSelectCross[] = "CONCAT( " . implode(", '{$delimiter}', ", $arSelectFieldQueries) . " ) as {$fieldName}";
                    }
                }
            }

            # Формируем запросы через CROSS JOIN
            $sqlCross = "( SELECT " . implode(", ", $arSelectCross) . " FROM ";
            foreach ($sqlList["CROSS"] as $key => $sqlItem) {
                if ($key != 0) {
                    $sqlCross .= " CROSS JOIN ";
                }
                $sqlCross .= " ({$sqlItem}) a{$key} ";
            }
            $sqlCross .= ") ";
            $strSql .= $sqlCross;
        }

        # Добавляем UNION ALL запросы
        if (!empty($sqlList["UNION"])) {
            foreach ($sqlList["UNION"] as $key => $sqlItem) {
                if (($key == 0 && !empty($sqlList["CROSS"])) || $key > 0) {
                    $strSql .= " UNION ALL ";
                }
                $strSql .= " ({$sqlItem})";
            }
        }

        if (!empty($sqlOrder)) {
            $strSql .= $sqlOrder;
        }

        if (!empty($arNavStartParams) && is_array($arNavStartParams)) {
            $nTopCount = (isset($arNavStartParams["nTopCount"]) ? (int)$arNavStartParams["nTopCount"] : 0);

            if ($nTopCount > 0) {
                $strSql .= " LIMIT {$nTopCount}";
                $res = $GLOBALS["DB"]->Query($strSql, false, "FILE: " . __FILE__ . "<br> LINE: " . __LINE__);
            }
            else {
                $res_cnt = $GLOBALS["DB"]->Query($strSql);
                $cntRows = $res_cnt->SelectedRowsCount();
                $res = new \CDBResult();
                $res->NavQuery($strSql, $cntRows, $arNavStartParams);
            }
        }
        else {
            $res = $GLOBALS["DB"]->Query($strSql, false, "FILE: " . __FILE__ . "<br> LINE: " . __LINE__);
        }

        $result = new \CIBlockResult($res);
        $result->SetIBlockTag($arFilterIBlocks);

        return $result;
    }

}