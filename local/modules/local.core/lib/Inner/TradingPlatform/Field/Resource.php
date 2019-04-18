<?php

namespace Local\Core\Inner\TradingPlatform\Field;


use Bitrix\Seo\LeadAds\Field;

class Resource extends AbstractField
{
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
            self::TYPE_SOURCE => 'Источник данных',
            self::TYPE_SIMPLE => 'Простое значение',
            self::TYPE_BUILDER => 'Сложное значение',
            self::TYPE_SELECT => 'Выбрать из списка',
            self::TYPE_LOGIC => 'Простое условие',
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

    /** @var integer $intStoreId ID магазина */
    protected $intStoreId;

    /**
     * Задать ID магазина
     *
     * @param $intStoreId
     *
     * @return $this
     */
    public function setStoreId($intStoreId)
    {
        $this->intStoreId = $intStoreId;
        return $this;
    }

    /**
     * Получить ID магазина
     *
     * @return int
     */
    protected function getStoreId()
    {
        return $this->intStoreId;
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
                    'LocalCoreTradingPlatform.refreshRow(\''.$this->getRowHash().'\')'
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
     * @return InputText | Textarea
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
            $obSimpleField = $this->getSimpleField()
                ->setName($this->getName().'['.self::TYPE_SIMPLE.'_VALUE]')
                ->setValue($this->getValue()[self::TYPE_SIMPLE.'_VALUE']);

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
     * @return Select
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
            $obSelectField = $this->getSelectField()
                ->setName($this->getName().'['.self::TYPE_SELECT.'_VALUE]')
                ->setValue($this->getValue()[self::TYPE_SELECT.'_VALUE']);

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
            'BASE_FIELD#PRODUCT_ID' => 'Идентификатор товара, уникален',
            'BASE_FIELD#PRODUCT_GROUP_ID' => 'Идентификатор группы товара, которые повторяются в рамках робофида',
        ];
        $arPriceOptions = [];
        $arSizeAndDimensionsOptions = [];
        $arWarrantyAndExpiryOptions = [];
        $arDeliveryOptions = [
            'BOOL_FIELD#DELIVERY_AVAILABLE' => 'Признак наличия службы доставки',
            'DELIVERY_FIELD#PRICE_FROM#MIN' => 'Стоимость доставки "от" (минимальная)',
            'DELIVERY_FIELD#PRICE_FROM#MAX' => 'Стоимость доставки "от" (максимальная)',
            'DELIVERY_FIELD#PRICE_TO#MIN' => 'Стоимость доставки "до" (минимальная)',
            'DELIVERY_FIELD#PRICE_TO#MAX' => 'Стоимость доставки "до" (максимальная)',
            'DELIVERY_FIELD#CURRENCY_CODE#CODE' => 'Валюта стоимости (код)',
            'DELIVERY_FIELD#CURRENCY_CODE#SHORT_NAME' => 'Валюта стоимости (короткое название)',
            'DELIVERY_FIELD#DAYS_FROM#MIN' => 'Сроки доставки "от" в днях (минимальные)',
            'DELIVERY_FIELD#DAYS_FROM#MAX' => 'Сроки доставки "от" в днях (максимальные)',
            'DELIVERY_FIELD#DAYS_TO#MIN' => 'Сроки доставки "до" в днях (минимальные)',
            'DELIVERY_FIELD#DAYS_TO#MAX' => 'Сроки доставки "до" в днях (максимальные)',
        ];
        $arPickupOptions = [
            'BOOL_FIELD#PICKUP_AVAILABLE' => 'Признак наличия самовывоза',
            'PICKUP_FIELD#PRICE#MIN' => 'Стоимость самовывоза (минимальная)',
            'PICKUP_FIELD#PRICE#MAX' => 'Стоимость самовывоза (максимальная)',
            'PICKUP_FIELD#CURRENCY_CODE#CODE' => 'Валюта стоимости (код)',
            'PICKUP_FIELD#CURRENCY_CODE#SHORT_NAME' => 'Валюта стоимости (короткое название)',
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
                        $arPriceOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Валюта (короткое название)';
                        break;

                    case 'quantity':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Количество товара без единицы измерения (к примеру 2)';
                        break;

                    case 'unitOfMeasure':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения кол-ва товара (код)';
                        $arBaseOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения кол-ва товара (короткое название)';
                        break;

                    case 'minQuantity':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Минимальное кол-во товара в заказе (без ед. измер.)';
                        $arBaseOptions['CUSTOM_FIELD#MIN_QUANTITY_WITH_SHORT_NAME'] = 'Минимальное кол-во товара в заказе с коротким названием ед. измер. (к примеру 2 шт.)';
                        break;

                    case 'categoryId':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Категория товара (идентификатор)';
                        $arBaseOptions['CUSTOM_FIELD#CATEGORY_NAME'] = 'Категория товара (название)';
                        break;

                    case 'image':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Ссылка на изображение';
                        break;

                    case 'countryOfProductionCode':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Страна производства (код)';
                        $arBaseOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#NAME'] = 'Страна производства (название)';
                        break;

                    case 'description':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Описание товара';
                        break;

                    case 'manufacturerWarranty':
                        $arWarrantyAndExpiryOptions['BOOL_FIELD#'.$strColumnNameInTable] = 'Признак наличия официальной гарантии производителя';
                        break;

                    case 'isSex':
                        $arBaseOptions['BOOL_FIELD#'.$strColumnNameInTable] = 'Признак отношения товара к удовлетворению сексуальных потребностей';
                        break;

                    case 'isSoftware':
                        $arBaseOptions['BOOL_FIELD#'.$strColumnNameInTable] = 'Признак отношения товара к программному обеспечению';
                        break;

                    case 'weight':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Вес товара (без ед. измер.)';
                        break;

                    case 'weightUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения веса товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения веса товара (короткое название)';
                        break;

                    case 'width':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Ширина товара (без ед. измер.)';
                        break;

                    case 'widthUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения ширины товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения ширины товара (короткое название)';
                        break;

                    case 'height':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Высота товара (без ед. измер.)';
                        break;

                    case 'heightUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения высоты товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения высоты товара (короткое название)';
                        break;

                    case 'length':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Длина товара (без ед. измер.)';
                        break;

                    case 'lengthUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения длины товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения длины товара (короткое название)';
                        break;

                    case 'volume':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Объем товара (без ед. измер.)';
                        break;

                    case 'volumeUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения объема товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения объема товара (короткое название)';
                        break;

                    case 'warrantyPeriod':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Срок официальной гарантии товара (без ед. измер.)';
                        break;

                    case 'warrantyPeriodCode':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения срока официальной гарантии товара (код)';
                        $arWarrantyAndExpiryOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения срока официальной гарантии товара (короткое название)';
                        break;

                    case 'expiryPeriod':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Срок годности / срок службы товара от даты производстава (без ед. измер.)';
                        break;

                    case 'expiryPeriodCode':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения срока годности / срока службы товара (код)';
                        $arWarrantyAndExpiryOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения срока годности / срока службы товара (короткое название)';
                        break;

                    case 'expiryDate':
                        $arWarrantyAndExpiryOptions['DATE_FIELD#'.$strColumnNameInTable] = 'Дата истечения срока годности товара (формат YYYY-MM-DD HH:MM:SS)';
                        break;

                    case 'inStock':
                        $arPriceOptions['BOOL_FIELD#'.$strColumnNameInTable] = 'Признак наличия товара';
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
        if( is_null(self::$_arBuilderOptionsRegister) )
        {
            self::$_arBuilderOptionsRegister = array_merge($this->getProductFieldsForSource(), $this->getParamsForSource());
        }
        return self::$_arBuilderOptionsRegister;
    }

    public function getSourceOptionsToJs()
    {
        $ar = $this->getSourceOptions();
        $arr = [];
        foreach ($ar as $k => $v)
        {
            foreach ($v as &$v1)
            {
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

            $obBuilderField = ( new Subfield\ResourceBuilder() )
                ->setName($this->getName().'['.self::TYPE_BUILDER.'_VALUE]')
                ->setValue($this->getValue()[self::TYPE_BUILDER.'_VALUE'])
                ->setRowHash($this->getRowHash())
                ->setOptions($this->getSourceOptions());

            $this->addToRender( $obBuilderField->getRender() );

        }
    }


    /* ************ */
    /* LOGIC BRANCH */
    /* ************ */

    private function initLogicBranch()
    {

        if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId())) {

            $this->addToRender('<div class="alert alert-info" role="alert">');

            $this->addToRender( $this->getLogicIfRow(0) );

            $this->addToRender('<h6>Иначе:</h6>');
            $this->addToRender( $this->getLogicResourceField(
                $this->getName().'['.self::TYPE_LOGIC.'_VALUE][ELSE][VALUE]',
                $this->getValue()[self::TYPE_LOGIC.'_VALUE']['ELSE']['VALUE']
            )->getRender() );

            $this->addToRender('</div>');
        }

    }

    private function getLogicIfRow($key)
    {
        $html = '<h6>Если:</h6>';

        $html .= ( new Subfield\ResourceLogic() )
            ->setName($this->getName().'['.self::TYPE_LOGIC.'_VALUE][IF]['.$key.'][V_1]')
            ->setValue($this->getValue()[self::TYPE_LOGIC.'_VALUE']['IF'][$key]['V_1'])
            ->setOptions($this->getSourceOptions())
            ->setDefaultOption('-- Выберите поле --')
            ->getRender().' ';

        $html .= ( new Subfield\ResourceLogic() )
            ->setName($this->getName().'['.self::TYPE_LOGIC.'_VALUE][IF]['.$key.'][OP]')
            ->setValue($this->getValue()[self::TYPE_LOGIC.'_VALUE']['IF'][$key]['OP'] ?? '==')
            ->setOptions([
                '==' => 'равно',
                '!=' => 'не равно',
                '>"' => 'больше',
                '>=' => 'больше или равно',
                '<' => 'меньше',
                '<=' => 'меньше или равно',
                '<>' => 'больше или меньше',
            ])->getRender().' ';

        $html .= '<div class="local-core-dropdown-input-line">'.( new InputText())
                ->setName($this->getName().'['.self::TYPE_LOGIC.'_VALUE][IF]['.$key.'][V_2]')
                ->setValue($this->getValue()[self::TYPE_LOGIC.'_VALUE']['IF'][$key]['V_2'])
                ->getRender().'</div>';

        $html .= '<h6>То:</h6>';

        $obResourceField = $this->getLogicResourceField(
            $this->getName().'['.self::TYPE_LOGIC.'_VALUE][IF]['.$key.'][VALUE]',
            $this->getValue()[self::TYPE_LOGIC.'_VALUE']['IF'][$key]['VALUE']
        );


        $html .= $obResourceField->getRender();

        return $html;
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

        if( !in_array(self::TYPE_IGNORE, $arAllowList) )
        {
            $arAllowList[] = self::TYPE_IGNORE;
        }

        $obResourceField = (new Resource())
            ->setName($strName)
            ->setValue($arValue)
            ->setRowHash($this->getRowHash());

        foreach ($arAllowList as $k => $v)
        {
            switch ($v)
            {
                case self::TYPE_SIMPLE:
                    if( $this->getSimpleField() instanceof AbstractField )
                    {
                        $obResourceField->setSimpleField($this->getSimpleField());
                    }
                    else
                    {
                        unset($arAllowList[$k]);
                    }
                    break;

                case self::TYPE_SELECT:
                    if( $this->getSelectField() instanceof AbstractField )
                    {
                        $obResourceField->setSelectField($this->getSelectField());
                    }
                    else
                    {
                        unset($arAllowList[$k]);
                    }
                    break;

                case self::TYPE_LOGIC:
                    unset($arAllowList[$k]);
                    break;

                case self::TYPE_SOURCE:
                case self::TYPE_BUILDER:
                    if( \Local\Core\Inner\Store\Base::hasSuccessImport($this->getStoreId()) )
                    {
                        $obResourceField->setStoreId($this->getStoreId());
                    }
                    else
                    {
                        unset($arAllowList[$k]);
                    }
                    break;
            }
        }

        $obResourceField->setAllowTypeList($arAllowList);

        return $obResourceField;
    }
}