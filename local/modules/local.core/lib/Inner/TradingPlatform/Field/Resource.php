<?php

namespace Local\Core\Inner\TradingPlatform\Field;


use Bitrix\Seo\LeadAds\Field;

class Resource extends AbstractField
{
    use Traits\StoreId;
    use Traits\Size;

    const TYPE_SOURCE = 'SOURCE';
    const TYPE_SIMPLE = 'SIMPLE';
    const TYPE_BUILDER = 'BUILDER';
    const TYPE_SELECT = 'SELECT';
    const TYPE_LOGIC = 'LOGIC';
    const TYPE_IGNORE = 'IGNORE';

    /**
     * Получить заголовоки типов
     *
     * @return array
     */
    private function getTypesTitles()
    {
        return [
            self::TYPE_SOURCE => 'Поле Robofeed XML',
            self::TYPE_SIMPLE => 'Простое значение',
            self::TYPE_BUILDER => 'Сложное значение',
            self::TYPE_SELECT => 'Выбрать из списка',
            self::TYPE_LOGIC => 'Сложное условие',
            self::TYPE_IGNORE => 'Игнорировать поле',
        ];
    }


    /** @var array $arAllowTypeList Список доступных типов данных */
    protected $arAllowTypeList = [
        self::TYPE_SOURCE
    ];

    /**
     * Задает список доступных типов данных.<br/>
     * Задавать массив из списка констант с префиксом <b>TYPE_</b>
     *
     * @param $ar
     *
     * @return $this
     */
    public function setAllowTypeList($ar)
    {
        $this->arAllowTypeList = $ar;
        return $this;
    }

    /**
     * Получить список доступных типов данных
     *
     * @return array
     */
    private function getAllowTypeList()
    {
        return $this->arAllowTypeList;
    }


    /** @inheritDoc */
    protected function execute()
    {
        $this->createBeginSelect();

        if (!empty($this->getValue()['TYPE'])) {
            switch ($this->getValue()['TYPE']) {
                case self::TYPE_SOURCE:
                    $this->initSourceBranch();
                    break;
                case self::TYPE_SIMPLE:
                    $this->initSimpleBranch();
                    break;
                case self::TYPE_BUILDER:
                    $this->initBuilderBranch();
                    break;
                case self::TYPE_SELECT:
                    $this->initSelectBranch();
                    break;
                case self::TYPE_LOGIC:
                    $this->initLogicBranch();
                    break;
                case self::TYPE_IGNORE:
                    break;
            }
        }
    }

    /**
     * Добавить в рендер список выбора типа данных
     */
    private function createBeginSelect()
    {
        $arOptions = [];
        foreach ($this->getAllowTypeList() as $v) {
            switch ($v) {
                case self::TYPE_SELECT:
                    if (!empty($this->getSelectField())) {
                        $arOptions[$v] = $this->getTypesTitles()[$v];
                    }
                    break;
                case self::TYPE_SIMPLE:
                    if (!empty($this->getSimpleField())) {
                        $arOptions[$v] = $this->getTypesTitles()[$v];
                    }
                    break;
                case self::TYPE_SOURCE:
                case self::TYPE_BUILDER:
                    if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId())) {
                        $arOptions[$v] = $this->getTypesTitles()[$v];
                    }
                    break;
                case self::TYPE_LOGIC:
                    if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId())) {
                        $arOptions[$v] = $this->getTypesTitles()[$v];
                    }
                    break;
                default:
                    $arOptions[$v] = $this->getTypesTitles()[$v];
                    break;
            }
        }

        if ($this->getIsRequired()) {
            unset($arOptions[self::TYPE_IGNORE]);
        }

        $obBeginSelect = (new Select())->setName($this->getName().'[TYPE]')
            ->setOptions($arOptions)
            ->setDefaultOption('-- Выберите тип данных --')
            ->setValue($this->getValue()['TYPE'] ?? null)
            ->setEvent([
                'onchange' => [
                    'PersonalTradingplatformFormComponent.refreshRow(\''.$this->getRowHash().'\')'
                ]
            ]);

        $this->addToRender($obBeginSelect->getRender());
    }


    /* ************* */
    /* SIMPLE BRANCH */
    /* ************* */

    /** @var AbstractField $_fieldSimpleField Поле, которое будет учавствовать в сценарии SIMPLE */
    protected $_fieldSimpleField;

    /**
     * Задает поле, которое будет учавствовать в SIMPLE сценарии.<br/>
     * Поле должно быть либо text, либо textarea.<br/>
     * Задавать название и значение у поля смысла не имеет, оно перебивается при добавлении в рендер.
     *
     * @param AbstractField $obSimpleField
     *
     * @return $this
     * @throws \Exception
     */
    public function setSimpleField(AbstractField $obSimpleField)
    {
        if (!($obSimpleField instanceof InputText) && !($obSimpleField instanceof Textarea)) {
            throw new \Exception('Поле должно быть text или textarea');
        }

        $this->_fieldSimpleField = $obSimpleField;
        return $this;
    }

    /**
     * Возвращает заданное поле для сценария SIMPLE
     *
     * @return InputText | Textarea | null
     */
    protected function getSimpleField()
    {
        return $this->_fieldSimpleField;
    }

    /**
     * Запускает сценарий ветки SIMPLE
     */
    private function initSimpleBranch()
    {
        if ($this->getSimpleField() instanceof AbstractField) {

            $obSimpleField = clone $this->getSimpleField()
                ->setName($this->getName().'['.self::TYPE_SIMPLE.'_VALUE]')
                ->setValue($this->getValue()[self::TYPE_SIMPLE.'_VALUE'])
                ->setRowHash($this->getRowHash());

            $this->addToRender($obSimpleField->getRender());
        }
    }

    /* ************* */
    /* SELECT BRANCH */
    /* ************* */

    /** @var AbstractField $_fieldSimpleField Поле, которое будет учавствовать в сценарии SELECT */
    protected $_fieldSelectField;

    /**
     * Задает поле, которое учавствует в сценарции SELECT.<br/>
     * Задавать название и значение у поля смысла не имеет, оно перебивается при добавлении в рендер.
     *
     * @param Select $obSelectField
     *
     * @return $this
     */
    public function setSelectField(Select $obSelectField)
    {
        $this->_fieldSelectField = $obSelectField;
        return $this;
    }

    /**
     * Возвращает заданное поле для сценария SELECT
     *
     * @return Select|null
     */
    public function getSelectField()
    {
        return $this->_fieldSelectField;
    }

    /**
     * Запускает сценарий ветки SELECT
     */
    private function initSelectBranch()
    {
        if ($this->getSelectField() instanceof Select) {
            $obSelectField = clone $this->getSelectField()
                ->setName($this->getName().'['.self::TYPE_SELECT.'_VALUE]')
                ->setValue($this->getValue()[self::TYPE_SELECT.'_VALUE'])
                ->setRowHash($this->getRowHash());

            $this->addToRender($obSelectField->getRender());
        }
    }


    /* ************* */
    /* SOURCE BRANCH */
    /* ************* */

    private function initSourceBranch()
    {
        if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId())) {
            $arOptions = $this->getSourceOptions();

            $obSelectField = (new Select())->setName($this->getName().'['.self::TYPE_SOURCE.'_VALUE]')
                ->setValue($this->getValue()[self::TYPE_SOURCE.'_VALUE'])
                ->setIsMultiple($this->getIsMultiple())
                ->setSize($this->getSize())
                ->setIsRequired($this->getIsRequired())
                ->setOptions($arOptions)
                ->setDefaultOption('-- Выберите данные --');

            $this->addToRender($obSelectField->getRender());
        }
    }

    private function getProductFieldsForSource()
    {
        $arOptions = [];
        switch (\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getStoreId())) {
            case 1:
                $arOptions = $this->getProductFieldsForSourceV1();
                break;
        }
        return $arOptions;
    }

    private function getProductFieldsForSourceV1()
    {
        $obRobofeedSchema = \Local\Core\Inner\Robofeed\Schema\Factory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getStoreId()))
            ->getSchemaMap();

        $arBaseOptions = [
            'BASE_FIELD#PRODUCT_ID' => 'Идентификатор товара',
            'BASE_FIELD#PRODUCT_GROUP_ID' => 'Идентификатор группы товара, которые повторяются',
        ];
        $arPriceOptions = [];
        $arSizeAndDimensionsOptions = [];
        $arWarrantyAndExpiryOptions = [];
        $arDeliveryOptions = [
            'BASE_FIELD#DELIVERY_AVAILABLE' => 'Признак наличия службы доставки',
            'DELIVERY_FIELD#PRICE_FROM#MIN' => 'Стоимость доставки "от" (минимальная)',
            'DELIVERY_FIELD#PRICE_FROM#MAX' => 'Стоимость доставки "от" (максимальная)',
            'DELIVERY_FIELD#PRICE_TO#MIN' => 'Стоимость доставки "до" (минимальная)',
            'DELIVERY_FIELD#PRICE_TO#MAX' => 'Стоимость доставки "до" (максимальная)',
            'DELIVERY_FIELD#CURRENCY_CODE#CODE' => 'Валюта стоимости (код)',
            'DELIVERY_FIELD#CURRENCY_CODE#NAME' => 'Валюта стоимости (название)',
            'DELIVERY_FIELD#DAYS_FROM#MIN' => 'Сроки доставки "от" в днях (минимальные)',
            'DELIVERY_FIELD#DAYS_FROM#MAX' => 'Сроки доставки "от" в днях (максимальные)',
            'DELIVERY_FIELD#DAYS_TO#MIN' => 'Сроки доставки "до" в днях (минимальные)',
            'DELIVERY_FIELD#DAYS_TO#MAX' => 'Сроки доставки "до" в днях (максимальные)',
        ];
        $arPickupOptions = [
            'BASE_FIELD#PICKUP_AVAILABLE' => 'Признак наличия самовывоза',
            'PICKUP_FIELD#PRICE#MIN' => 'Стоимость самовывоза (минимальная)',
            'PICKUP_FIELD#PRICE#MAX' => 'Стоимость самовывоза (максимальная)',
            'PICKUP_FIELD#CURRENCY_CODE#CODE' => 'Валюта стоимости (код)',
            'PICKUP_FIELD#CURRENCY_CODE#NAME' => 'Валюта стоимости (название)',
            'PICKUP_FIELD#SUPPLY_FROM#MIN' => 'Сроки поступления товара в магазин/на склад "от" в днях (минимальные)',
            'PICKUP_FIELD#SUPPLY_FROM#MAX' => 'Сроки поступления товара в магазин/на склад "от" в днях (максимальные)',
            'PICKUP_FIELD#SUPPLY_TO#MIN' => 'Сроки поступления товара в магазин/на склад "до" в днях (минимальные)',
            'PICKUP_FIELD#SUPPLY_TO#MAX' => 'Сроки поступления товара в магазин/на склад "до" в днях (максимальные)',
        ];

        foreach ($obRobofeedSchema['robofeed']['offers']['offer']['@value'] as $k => $v) {
            if ($v instanceof \Local\Core\Inner\Robofeed\SchemaFields\ScalarField) {
                preg_match_all('/([a-z]+|[A-Z][a-z]+)/', $k, $strColumnNameInTable);
                $strColumnNameInTable = implode('_', array_map('strtoupper', $strColumnNameInTable[1]));

                switch ($k) {
                    case 'article':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Артикул';
                        break;

                    case 'fullName':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Полное название товара (к примеру "Смартфон Apple iPhone 8S 64GB")';
                        break;

                    case 'simpleName':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Простое название товара (к примеру "iPhone 8S")';
                        break;

                    case 'manufacturer':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Название компании производителя';
                        break;

                    case 'model':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Модель';
                        break;

                    case 'url':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Ссылка на детальную страницу (без добавления utm)';
                        $arBaseOptions['CUSTOM_FIELD#DETAIL_URL_WITH_UTM'] = 'Ссылка на детальную страницу (добавить utm)';
                        break;

                    case 'manufacturerCode':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Код производителя для данного товара';
                        break;

                    case 'price':
                        $arPriceOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Текущая стоимость товара (без валюты)';
                        break;

                    case 'oldPrice':
                        $arPriceOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Старая стоимость товара (без валюты)';
                        break;

                    case 'currencyCode':
                        $arPriceOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Валюта (код)';
                        $arPriceOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#NAME'] = 'Валюта (название)';
                        break;

                    case 'quantity':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Количество товара без единицы измерения (к примеру 2)';
                        break;

                    case 'unitOfMeasure':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения кол-ва товара (код)';
                        $arBaseOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#NAME'] = 'Единица измерения кол-ва товара (название)';
                        break;

                    case 'minQuantity':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Минимальное кол-во товара в заказе (без ед. измер.)';
                        break;

                    case 'categoryId':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Категория товара (идентификатор)';
                        $arBaseOptions['CUSTOM_FIELD#CATEGORY_NAME'] = 'Категория товара (название)';
                        break;

                    case 'image':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Ссылки на изображения';
                        break;

                    case 'countryOfProductionCode':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Страна производства (код)';
                        $arBaseOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#NAME'] = 'Страна производства (название)';
                        break;

                    case 'description':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Описание товара';
                        break;

                    case 'manufacturerWarranty':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Признак наличия официальной гарантии производителя';
                        break;

                    case 'isSex':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Признак отношения товара к удовлетворению сексуальных потребностей';
                        break;

                    case 'isSoftware':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Признак отношения товара к программному обеспечению';
                        break;

                    case 'weight':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Вес товара (без ед. измер.)';
                        break;

                    case 'weightUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения веса товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#NAME'] = 'Единица измерения веса товара (название)';
                        break;

                    case 'width':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Ширина товара (без ед. измер.)';
                        break;

                    case 'widthUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения ширины товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#NAME'] = 'Единица измерения ширины товара (название)';
                        break;

                    case 'height':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Высота товара (без ед. измер.)';
                        break;

                    case 'heightUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения высоты товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#NAME'] = 'Единица измерения высоты товара (название)';
                        break;

                    case 'length':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Длина товара (без ед. измер.)';
                        break;

                    case 'lengthUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения длины товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#NAME'] = 'Единица измерения длины товара (название)';
                        break;

                    case 'volume':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Объем товара (без ед. измер.)';
                        break;

                    case 'volumeUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения объема товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#NAME'] = 'Единица измерения объема товара (название)';
                        break;

                    case 'warrantyPeriod':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Срок официальной гарантии товара (без ед. измер.)';
                        break;

                    case 'warrantyPeriodCode':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения срока официальной гарантии товара (код)';
                        $arWarrantyAndExpiryOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#NAME'] = 'Единица измерения срока официальной гарантии товара (название)';
                        break;

                    case 'expiryPeriod':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Срок годности / срок службы товара от даты производстава (без ед. измер.)';
                        break;

                    case 'expiryPeriodCode':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения срока годности / срока службы товара (код)';
                        $arWarrantyAndExpiryOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#NAME'] = 'Единица измерения срока годности / срока службы товара (название)';
                        break;

                    case 'expiryDate':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Дата истечения срока годности товара (формат YYYY-MM-DD HH:MM:SS)';
                        break;

                    case 'inStock':
                        $arPriceOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Признак наличия товара';
                        break;

                    case 'salesNotes':
                        $arPriceOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Условия продажи товара';
                        break;
                }
            }
        }

        return [
            'Основные поля' => $arBaseOptions,
            'Стоимость и наличие' => $arPriceOptions,
            'Габариты и объем' => $arSizeAndDimensionsOptions,
            'Гарантия и срок службы' => $arWarrantyAndExpiryOptions,
            'Доставка' => $arDeliveryOptions,
            'Самовывоз' => $arPickupOptions,
        ];
    }

    private function getParamsForSource()
    {

        $rsProductProps = \Local\Core\Model\Robofeed\StoreProductParamFactory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getStoreId()))
            ->setStoreId($this->getStoreId());
        $rsProductProps = $rsProductProps::getList([
            'select' => ['CODE', 'NAME'],
            'group' => ['CODE'],
            'order' => ['NAME' => 'ASC']
        ]);

        $arParamsOption = [];
        while ($ar = $rsProductProps->fetch()) {
            $arParamsOption['PARAM_FIELD#'.$ar['CODE']] = $ar['NAME'].' ['.$ar['CODE'].']';
        }

        return ['Параметры товаров' => $arParamsOption];
    }

    protected static $_arBuilderOptionsRegister;

    public function getSourceOptions()
    {
        if (is_null(self::$_arBuilderOptionsRegister)) {
            $obCache = \Bitrix\Main\Application::getInstance()
                ->getCache();

            if (
            $obCache->startDataCache(60 * 60 * 24 * 7, __METHOD__.__LINE__.'#STORE_ID='.$this->getStoreId(),
                \Local\Core\Inner\Cache::getCachePath(['Inner', 'TradingPlatform', 'Field', 'Resource'], ['SourceOptions', 'storeId='.$this->getStoreId()]))
            ) {
                self::$_arBuilderOptionsRegister = array_merge($this->getProductFieldsForSource(), $this->getParamsForSource());
                if (empty(self::$_arBuilderOptionsRegister)) {
                    $obCache->abortDataCache();
                } else {
                    $obCache->endDataCache(self::$_arBuilderOptionsRegister);
                }
            } else {
                self::$_arBuilderOptionsRegister = $obCache->getVars();
            }


        }
        return self::$_arBuilderOptionsRegister;
    }

    public function getSourceOptionsToJs()
    {
        $ar = $this->getSourceOptions();
        $arr = [];
        foreach ($ar as $k => $v) {
            foreach ($v as &$v1) {
                $v1 = addcslashes($v1, '"');
            }
            $arr = array_merge($arr, $v);
        }

        return \Bitrix\Main\Web\Json::encode($arr);
    }


    /* ************** */
    /* BUILDER BRANCH */
    /* ************** */

    private function initBuilderBranch()
    {
        if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId())) {

            $obBuilderField = (new Subfield\ResourceBuilder())->setName($this->getName().'['.self::TYPE_BUILDER.'_VALUE]')
                ->setValue($this->getValue()[self::TYPE_BUILDER.'_VALUE'])
                ->setRowHash($this->getRowHash())
                ->setOptions($this->getSourceOptions());

            $this->addToRender($obBuilderField->getRender());

        }
    }


    /* ************ */
    /* LOGIC BRANCH */
    /* ************ */

    private function initLogicBranch()
    {

        if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId())) {


            $this->addLogicIfRow(0);
            $this->addLogicElseRow();
        }

    }

    private function addLogicIfRow($key)
    {
        $this->addToRender('<h4>Если:</h4>');

        $this->addToRender((new Condition())->setStoreId($this->getStoreId())
            ->setName($this->getName().'['.self::TYPE_LOGIC.'_VALUE][IF]['.$key.'][RULE]')
            ->setValue($this->getValue()[self::TYPE_LOGIC.'_VALUE']['IF'][$key]['RULE'])
            ->getRender());

        $this->addToRender('<h4>То:</h4>');

        $this->addToRender($this->getLogicResourceField($this->getName().'['.self::TYPE_LOGIC.'_VALUE][IF]['.$key.'][VALUE]', $this->getValue()[self::TYPE_LOGIC.'_VALUE']['IF'][$key]['VALUE'])
            ->getRender());
    }

    private function addLogicElseRow()
    {
        $this->addToRender('<h4>Иначе:</h4>');
        $this->addToRender($this->getLogicResourceField($this->getName().'['.self::TYPE_LOGIC.'_VALUE][ELSE][VALUE]', $this->getValue()[self::TYPE_LOGIC.'_VALUE']['ELSE']['VALUE'])
            ->getRender());
    }

    /**
     * Формирует поле ресурса в ветке логички как результат
     *
     * @param $strName
     * @param $arValue
     *
     * @return Resource
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function getLogicResourceField($strName, $arValue)
    {
        $arAllowList = $this->getAllowTypeList();

        if (!in_array(self::TYPE_IGNORE, $arAllowList) && !$this->getIsRequired()) {
            $arAllowList[] = self::TYPE_IGNORE;
        }
        if ($this->getIsRequired() && in_array(self::TYPE_IGNORE, $arAllowList)) {
            unset($arAllowList[array_search(self::TYPE_IGNORE, $arAllowList)]);
        }

        $obResourceField = (new Resource())->setName($strName)
            ->setValue($arValue)
            ->setRowHash($this->getRowHash());

        foreach ($arAllowList as $k => $v) {
            switch ($v) {
                case self::TYPE_SIMPLE:
                    if ($this->getSimpleField() instanceof AbstractField) {
                        $obResourceField->setSimpleField(clone $this->getSimpleField())
                            ->setRowHash($this->getRowHash());
                    } else {
                        unset($arAllowList[$k]);
                    }
                    break;

                case self::TYPE_SELECT:
                    if ($this->getSelectField() instanceof AbstractField) {
                        $obResourceField->setSelectField(clone $this->getSelectField())
                            ->setRowHash($this->getRowHash());
                    } else {
                        unset($arAllowList[$k]);
                    }
                    break;

                case self::TYPE_LOGIC:
                    unset($arAllowList[$k]);
                    break;
                case self::TYPE_SOURCE:
                case self::TYPE_BUILDER:
                    if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId())) {
                        $obResourceField->setStoreId($this->getStoreId());
                    } else {
                        unset($arAllowList[$k]);
                    }
                    break;
            }
        }

        $obResourceField->setAllowTypeList($arAllowList);

        return $obResourceField;
    }

    /** @inheritDoc */
    public function isValueFilled($mixData)
    {
        $boolRes = false;
        if (array_key_exists('TYPE', $mixData) && in_array($mixData['TYPE'], $this->getAllowTypeList())) {
            switch ($mixData['TYPE']) {
                case self::TYPE_IGNORE:
                    $boolRes = false;
                    break;

                case self::TYPE_SIMPLE:
                    if ($this->getSimpleField() instanceof AbstractField) {
                        $boolRes = $this->getSimpleField()
                            ->isValueFilled($mixData[self::TYPE_SIMPLE.'_VALUE']);
                    }
                    break;

                case self::TYPE_SELECT:
                    if ($this->getSelectField() instanceof Select) {
                        $boolRes = $this->getSelectField()
                            ->isValueFilled($mixData[self::TYPE_SELECT.'_VALUE']);
                    }
                    break;

                case self::TYPE_BUILDER:
                    $boolRes = (new Subfield\ResourceBuilder())->isValueFilled($mixData[self::TYPE_BUILDER.'_VALUE']);
                    break;

                case self::TYPE_SOURCE:
                    if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId())) {
                        $boolRes = (new Select())->setOptions($this->getSourceOptions())
                            ->isValueFilled($mixData[self::TYPE_SOURCE.'_VALUE']);
                    }
                    break;

                case self::TYPE_LOGIC:
                    if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId())) {

                        $obResourceTmpField = (new Resource())->setIsMultiple($this->getIsMultiple())
                            ->setAllowTypeList($this->getAllowTypeList())
                            ->setStoreId($this->getStoreId());

                        if ($this->getSimpleField() instanceof AbstractField) {
                            $obResourceTmpField->setSimpleField($this->getSimpleField());
                        }

                        if ($this->getSelectField() instanceof AbstractField) {
                            $obResourceTmpField->setSelectField($this->getSelectField());
                        }

                        $boolRes = $obResourceTmpField->isValueFilled($mixData[self::TYPE_LOGIC.'_VALUE']['ELSE']['VALUE']);

                        if ($boolRes) {
                            foreach ($mixData[self::TYPE_LOGIC.'_VALUE']['IF'] as $arIf) {
                                if (!$obResourceTmpField->isValueFilled($arIf['VALUE'])) {
                                    $boolRes = false;
                                    break;
                                }
                            }
                        }

                        unset($obResourceTmpField);
                    }
                    break;
            }
        }

        return $boolRes;
    }

    /** @inheritDoc */
    public function extractValue($mixData, $mixAdditionalData = null)
    {
        $mixReturn = null;

        if (array_key_exists('TYPE', $mixData) && in_array($mixData['TYPE'], $this->getAllowTypeList())) {
            switch ($mixData['TYPE']) {
                case self::TYPE_IGNORE:
                    $mixReturn = null;
                    break;

                case self::TYPE_SIMPLE:
                    if ($this->getSimpleField() instanceof AbstractField) {
                        $mixReturn = $this->getSimpleField()
                            ->extractValue($mixData[self::TYPE_SIMPLE.'_VALUE']);
                    }
                    break;

                case self::TYPE_SELECT:
                    if ($this->getSelectField() instanceof Select) {
                        $mixReturn = $this->getSelectField()
                            ->extractValue($mixData[self::TYPE_SELECT.'_VALUE']);
                    }
                    break;

                case self::TYPE_BUILDER:
                    $builderVal = $mixData[self::TYPE_BUILDER.'_VALUE'];
                    preg_match_all('/{{([\#\-\_\|A-Za-z0-9]+)}}/', str_replace(["\r\n", "\n"], '', $builderVal), $matches);
                    if( !empty( $matches[1] ) )
                    {
                        foreach ($matches[1] as &$v)
                        {
                            $v = $this->extractSourceValue($v, $mixAdditionalData);
                        }
                        unset($v);
                        $mixReturn = str_replace($matches[0], $matches[1], str_replace(["\r\n", "\n"], '', $builderVal));
                    }
                    break;

                case self::TYPE_SOURCE:
                    if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId())) {

                        if (
                            $mixData[self::TYPE_SOURCE.'_VALUE'] == (new Select())->setOptions($this->getSourceOptions())
                                ->extractValue($mixData[self::TYPE_SOURCE.'_VALUE'])
                        ) {
                            $mixReturn = $this->extractSourceValue($mixData[self::TYPE_SOURCE.'_VALUE'], $mixAdditionalData);
                        }
                    }
                    break;

                case self::TYPE_LOGIC:
                    if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId())) {

                        $obResourceTmpField = (new Resource())->setIsMultiple($this->getIsMultiple())
                            ->setAllowTypeList($this->getAllowTypeList())
                            ->setStoreId($this->getStoreId());

                        if ($this->getSimpleField() instanceof AbstractField) {
                            $obResourceTmpField->setSimpleField($this->getSimpleField());
                        }

                        if ($this->getSelectField() instanceof AbstractField) {
                            $obResourceTmpField->setSelectField($this->getSelectField());
                        }

                        foreach ($mixData[self::TYPE_LOGIC.'_VALUE']['IF'] as $arIf) {
                            if (
                                (new Condition())->setStoreId($this->getStoreId())
                                    ->extractValue($arIf['RULE'], $mixAdditionalData)
                            ) {
                                $mixReturn = $obResourceTmpField->extractValue($arIf['VALUE'], $mixAdditionalData);
                                break;
                            }
                        }

                        if (is_null($mixReturn)) {
                            $mixReturn = $obResourceTmpField->extractValue($mixData[self::TYPE_LOGIC.'_VALUE']['ELSE']['VALUE'], $mixAdditionalData);
                        }

                        unset($obResourceTmpField);
                    }
                    break;
            }
        }

        return $mixReturn;
    }

    protected static $arReferenceFieldCache = [];
    protected static $arCustomFieldCache = [];


    protected function fillReferenceFieldCache($strReferenceType)
    {
        $obCache = \Bitrix\Main\Application::getInstance()->getCache();
        $className = null;
        switch ($strReferenceType)
        {
            case 'CURRENCY':
                $className = 'CurrencyTable';
                break;
            case 'COUNTRY':
                $className = 'CountryTable';
                break;
            case 'MEASURE':
                $className = 'MeasureTable';
                break;
        }

        if( !is_null($className) )
        {
            if( $obCache->startDataCache(
                60*60*24*7,
                __METHOD__.__LINE__,
                \Local\Core\Inner\Cache::getCachePath(['Model', 'Reference', $className], ['ResourceReferenceField'])
            ) )
            {
                $obReference = null;
                switch ($strReferenceType)
                {
                    case 'CURRENCY':
                        $obReference = \Local\Core\Model\Reference\CurrencyTable::getList(['select' => ['NAME', 'CODE', 'NUMBER_OF_CURRENCY']]);
                        break;
                    case 'COUNTRY':
                        $obReference = \Local\Core\Model\Reference\CountryTable::getList(['select' => ['NAME', 'CODE']]);
                        break;
                    case 'MEASURE':
                        $obReference = \Local\Core\Model\Reference\MeasureTable::getList(['select' => ['NAME', 'CODE']]);
                        break;
                }

                if( is_null($obReference) )
                {
                    $obCache->abortDataCache();
                }
                elseif( $obReference->getSelectedRowsCount() < 1)
                {
                    $obCache->abortDataCache();
                }
                else
                {
                    while ($ar = $obReference->fetch())
                    {
                        self::$arReferenceFieldCache[ $strReferenceType ][ $ar['CODE'] ] = $ar;
                    }

                    $obCache->endDataCache(self::$arReferenceFieldCache[ $strReferenceType ]);
                }
            }
            else
            {
                self::$arReferenceFieldCache[ $strReferenceType ] = $obCache->getVars();
            }
        }
    }

    protected function extractSourceValue($mixData, $mixAdditionalData = null)
    {
        $strReturn = null;

        $arData = array_map('trim', explode('#', $mixData));
        $arData = array_values(array_diff($arData, ['']));
        switch ($arData[0]) {
            case 'BASE_FIELD':
                $strReturn = $mixAdditionalData[$arData[1]];
                break;

            case 'PARAM_FIELD':
                $strReturn = $mixAdditionalData['PARAM_'.$arData[1]];
                break;

            case 'REFERENCE_FIELD':

                if( !is_null( $mixAdditionalData[$arData[1]] ) )
                {
                    $strReferenceType = null;

                    switch ($arData[1])
                    {
                        case 'CURRENCY_CODE':
                            $strReferenceType = 'CURRENCY';
                            break;
                        case 'COUNTRY_OF_PRODUCTION_CODE':
                            $strReferenceType = 'COUNTRY';
                            break;
                        case 'UNIT_OF_MEASURE':
                        case 'WEIGHT_UNIT_CODE':
                        case 'WIDTH_UNIT_CODE':
                        case 'HEIGHT_UNIT_CODE':
                        case 'LENGTH_UNIT_CODE':
                        case 'VOLUME_UNIT_CODE':
                        case 'WARRANTY_PERIOD_CODE':
                        case 'EXPIRY_PERIOD_CODE':
                            $strReferenceType = 'MEASURE';
                            break;
                    }

                    if( is_null( self::$arReferenceFieldCache[ $strReferenceType ] ) )
                    {
                        $this->fillReferenceFieldCache($strReferenceType);
                    }

                    switch ($arData[2])
                    {
                        case 'NAME':
                        case 'CODE':
                        case 'NUMBER_OF_CURRENCY':
                            $strReturn = self::$arReferenceFieldCache[ $strReferenceType ][ $mixAdditionalData[$arData[1]] ][ $arData[2] ];
                            break;
                    }
                }

                break;

            case 'CUSTOM_FIELD':

                switch ($arData[1])
                {
                    case 'DETAIL_URL_WITH_UTM':

                        $strReturn = $mixAdditionalData['URL'];
                        if( !empty( trim($mixAdditionalData['@HANDLER_SETTINGS']['UTM']) ) )
                        {
                            $strTail = $mixAdditionalData['@HANDLER_SETTINGS']['UTM'];
                            $strTail = ( substr(trim($strTail), 0, 1) == '?' ) ? substr(trim($strTail), 1) : trim($strTail);

                            $strReturn .= ( stripos($strReturn, '?') === false ) ? '?'.$strTail : '&'.$strTail;
                        }

                        break;
                    case 'CATEGORY_NAME':

                        if( is_null(self::$arCustomFieldCache['CATEGORY']) )
                        {
                            $obCache = \Bitrix\Main\Application::getInstance()->getCache();
                            if( $obCache->startDataCache(
                                60*60*24*7,
                                __METHOD__.__LINE__,
                                \Local\Core\Inner\Cache::getCachePath(['Inner', 'TradingPlatform', 'Field', 'Resource'], ['CustomFieldCategoryList', 'storeId='.$this->getStoreId()])
                            ) )
                            {
                                $rsCategories = \Local\Core\Model\Robofeed\StoreCategoryFactory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getStoreId()))->setStoreId( $this->getStoreId() )::getList([
                                    'select' => ['CATEGORY_ID', 'CATEGORY_PARENT_ID', 'CATEGORY_NAME']
                                ]);
                                if( $rsCategories->getSelectedRowsCount() < 1 )
                                {
                                    $obCache->abortDataCache();
                                }
                                else
                                {
                                    while ($ar = $rsCategories->fetch())
                                    {
                                        self::$arCustomFieldCache['CATEGORY'][ $ar['CATEGORY_ID'] ] = $ar;
                                    }

                                    $obCache->endDataCache(self::$arCustomFieldCache['CATEGORY']);
                                }
                            }
                            else
                            {
                                self::$arCustomFieldCache['CATEGORY'] = $obCache->getVars();
                            }
                        }

                        $strReturn = self::$arCustomFieldCache['CATEGORY'][ $mixAdditionalData['CATEGORY_ID'] ]['CATEGORY_NAME'];

                        break;
                }

                break;

            case 'DELIVERY_FIELD':

                if( !empty( $mixAdditionalData['DELIVERY_OPTIONS'] ) )
                {
                    switch ($arData[1])
                    {
                        case 'PRICE_FROM':
                            switch ($arData[2])
                            {
                                case 'MIN':
                                    $strReturn = min(array_column($mixAdditionalData['DELIVERY_OPTIONS'], 'PRICE_FROM'));
                                    break;

                                case 'MAX':
                                    $strReturn = max(array_column($mixAdditionalData['DELIVERY_OPTIONS'], 'PRICE_FROM'));
                                    break;
                            }
                            break;

                        case 'PRICE_TO':
                            switch ($arData[2])
                            {
                                case 'MIN':
                                    $strReturn = min(array_column($mixAdditionalData['DELIVERY_OPTIONS'], 'PRICE_TO'));
                                    break;

                                case 'MAX':
                                    $strReturn = max(
                                        array_merge(
                                            array_column($mixAdditionalData['DELIVERY_OPTIONS'], 'PRICE_TO'),
                                            array_column($mixAdditionalData['DELIVERY_OPTIONS'], 'PRICE_FROM')
                                        )
                                    );
                                    break;
                            }
                            break;

                        case 'CURRENCY_CODE':
                            if( is_null( self::$arReferenceFieldCache[ 'CURRENCY' ] ) )
                            {
                                $this->fillReferenceFieldCache('CURRENCY');
                            }

                            switch ($arData[2])
                            {
                                case 'CODE':
                                    if(
                                        is_array(self::$arReferenceFieldCache[ 'CURRENCY' ])
                                        && !empty( self::$arReferenceFieldCache[ 'CURRENCY' ][ $mixAdditionalData['DELIVERY_OPTIONS'][0]['CURRENCY_CODE'] ] )
                                    )
                                    {
                                        $strReturn = $mixAdditionalData['DELIVERY_OPTIONS'][0]['CURRENCY_CODE'];
                                    }
                                    break;

                                case 'NAME':
                                    if(
                                        is_array(self::$arReferenceFieldCache[ 'CURRENCY' ])
                                        && !empty( self::$arReferenceFieldCache[ 'CURRENCY' ][ $mixAdditionalData['DELIVERY_OPTIONS'][0]['CURRENCY_CODE'] ] )
                                    )
                                    {
                                        $strReturn = self::$arReferenceFieldCache[ 'CURRENCY' ][ $mixAdditionalData['DELIVERY_OPTIONS'][0]['CURRENCY_CODE'] ]['NAME'];
                                    }
                                    break;
                            }
                            break;

                        case 'DAYS_FROM':
                            switch ($arData[2])
                            {
                                case 'MIN':
                                    $strReturn = min(array_column($mixAdditionalData['DELIVERY_OPTIONS'], 'DAYS_FROM'));
                                    break;

                                case 'MAX':
                                    $strReturn = max(array_column($mixAdditionalData['DELIVERY_OPTIONS'], 'DAYS_FROM'));
                                    break;
                            }
                            break;

                        case 'DAYS_TO':
                            switch ($arData[2])
                            {
                                case 'MIN':
                                    $strReturn = min(array_column($mixAdditionalData['DELIVERY_OPTIONS'], 'DAYS_TO'));
                                    break;

                                case 'MAX':
                                    $strReturn = max(array_column($mixAdditionalData['DELIVERY_OPTIONS'], 'DAYS_TO'));
                                    break;
                            }
                            break;
                    }
                }
                break;

            case 'PICKUP_FIELD':


                if( !empty( $mixAdditionalData['PICKUP_OPTIONS'] ) )
                {
                    switch ($arData[1])
                    {
                        case 'PRICE':
                            switch ($arData[2])
                            {
                                case 'MIN':
                                    $strReturn = min(array_column($mixAdditionalData['PICKUP_OPTIONS'], 'PRICE'));
                                    break;

                                case 'MAX':
                                    $strReturn = max(array_column($mixAdditionalData['PICKUP_OPTIONS'], 'PRICE'));
                                    break;
                            }
                            break;

                        case 'CURRENCY_CODE':
                            if( is_null( self::$arReferenceFieldCache[ 'CURRENCY' ] ) )
                            {
                                $this->fillReferenceFieldCache('CURRENCY');
                            }

                            switch ($arData[2])
                            {
                                case 'CODE':
                                    if(
                                        is_array(self::$arReferenceFieldCache[ 'CURRENCY' ])
                                        && !empty( self::$arReferenceFieldCache[ 'CURRENCY' ][ $mixAdditionalData['PICKUP_OPTIONS'][0]['CURRENCY_CODE'] ] )
                                    )
                                    {
                                        $strReturn = $mixAdditionalData['PICKUP_OPTIONS'][0]['CURRENCY_CODE'];
                                    }
                                    break;

                                case 'NAME':
                                    if(
                                        is_array(self::$arReferenceFieldCache[ 'CURRENCY' ])
                                        && !empty( self::$arReferenceFieldCache[ 'CURRENCY' ][ $mixAdditionalData['PICKUP_OPTIONS'][0]['CURRENCY_CODE'] ] )
                                    )
                                    {
                                        $strReturn = self::$arReferenceFieldCache[ 'CURRENCY' ][ $mixAdditionalData['PICKUP_OPTIONS'][0]['CURRENCY_CODE'] ]['NAME'];
                                    }
                                    break;
                            }
                            break;

                        case 'SUPPLY_FROM':
                            switch ($arData[2])
                            {
                                case 'MIN':
                                    $strReturn = min(array_column($mixAdditionalData['PICKUP_OPTIONS'], 'SUPPLY_FROM'));
                                    break;

                                case 'MAX':
                                    $strReturn = max(array_column($mixAdditionalData['PICKUP_OPTIONS'], 'SUPPLY_FROM'));
                                    break;
                            }
                            break;

                        case 'SUPPLY_TO':
                            switch ($arData[2])
                            {
                                case 'MIN':
                                    $strReturn = min(array_column($mixAdditionalData['PICKUP_OPTIONS'], 'SUPPLY_TO'));
                                    break;

                                case 'MAX':
                                    $strReturn = max(array_column($mixAdditionalData['PICKUP_OPTIONS'], 'SUPPLY_TO'));
                                    break;
                            }
                            break;
                    }
                }
                break;
        }

        return $strReturn;
    }

    /**
     * Получается тип и значение, которое отработает в конечном итоге.<br/>
     * К примеру мы использовали этот метов в хэндлере ЯМаркета, что бы понять - будет использоваться
     *  простое значение или билдер с нашим маркером.<br/>
     * Следует вызывать, убедившись, что поле LOGIC, иначе хз че будет вообще.
     *
     * @param      $mixData
     * @param null $mixAdditionalData
     *
     * @return |null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function extractLogicValidValue($mixData, $mixAdditionalData = null)
    {
        $mixReturn = null;
        if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId())) {

            $obResourceTmpField = (new Resource())->setIsMultiple($this->getIsMultiple())
                ->setAllowTypeList($this->getAllowTypeList())
                ->setStoreId($this->getStoreId());

            if ($this->getSimpleField() instanceof AbstractField) {
                $obResourceTmpField->setSimpleField($this->getSimpleField());
            }

            if ($this->getSelectField() instanceof AbstractField) {
                $obResourceTmpField->setSelectField($this->getSelectField());
            }

            foreach ($mixData[self::TYPE_LOGIC.'_VALUE']['IF'] as $arIf) {
                if (
                (new Condition())->setStoreId($this->getStoreId())
                    ->extractValue($arIf['RULE'], $mixAdditionalData)
                ) {
                    $mixReturn = $arIf['VALUE'];
                    break;
                }
            }

            if (is_null($mixReturn)) {
                $mixReturn = $mixData[self::TYPE_LOGIC.'_VALUE']['ELSE']['VALUE'];
            }

            unset($obResourceTmpField);
        }

        return $mixReturn;
    }
}