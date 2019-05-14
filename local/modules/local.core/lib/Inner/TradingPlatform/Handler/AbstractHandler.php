<?php

namespace Local\Core\Inner\TradingPlatform\Handler;

use \Local\Core\Inner\TradingPlatform\Field;

abstract class AbstractHandler
{
    /**
     * Возвращает код хэндлера
     * @return string
     */
    abstract public static function getCode();

    /**
     * Возвращает название хэндлера
     * @return string
     */
    abstract public static function getTitle();

    /**
     * Возвращает основную валюты торговой площадки
     *
     * @return string Код валюты из справочника валют
     */
    abstract public static function getMainCurrency();

    /**
     * Возвращает список поддерживаемых валют торговой площадки, включая основную
     *
     * @return array Коды валют из справочника валют
     */
    abstract public static function getSupportedCurrency();

    /**
     * Вывод полей для редактирования в форме
     */
    public function printFormFields()
    {
        foreach ($this->getFields() as $obField) {
            if ($obField instanceof Field\AbstractField) {
                $obField->printRow();
            }
        }
    }

    protected $arFieldsCache;

    /**
     * Получить набор полей обработчика, как общие, так и индивидуальные
     *
     * @return array
     */
    public function getFields()
    {
        if( is_null($this->arFieldsCache) )
        {
            $this->arFieldsCache = array_merge($this->getGeneralFields(), $this->getHandlerFields());
        }
        return $this->arFieldsCache;
    }

    /**
     * Получить общие поля
     *
     * @return array
     */
    protected function getGeneralFields()
    {

        $strMainCurrency = '';
        $arConvertToOptions = [
            'NOT_CONVERT' => 'Оставлять цены в переданных валютах'
        ];
        $rs = \Local\Core\Model\Reference\CurrencyTable::getList([
            'filter' => ['CODE' => $this::getSupportedCurrency()],
            'select' => ['CODE', 'NAME']
        ]);
        while ($ar = $rs->fetch())
        {
            $arConvertToOptions[ $ar['CODE'] ] = 'Конвертировать в "'.htmlspecialchars($ar['NAME']).'"';
            if( $ar['CODE'] == $this::getMainCurrency() )
            {
                $strMainCurrency =  '<br/><br/><b>Основная валюта текущией торговой площадки:</b> '.htmlspecialchars($ar['NAME']).'.';
            }
        }

        return [
            '#header_g1' => (new Field\Header())->setValue('Общие настройки обработки'),

            '@handler_settings__CONVERT_CURRENCY_TO' => (new Field\Select())->setTitle('Конвертация цен')
                ->setName('HANDLER_RULES[@handler_settings][CONVERT_CURRENCY_TO]')
                ->setIsRequired()
                ->setOptions($arConvertToOptions)
                ->setValue($this->getHandlerRules()['@handler_settings']['CONVERT_CURRENCY_TO'] ?? 'NOT_CONVERT')
                ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
Конвертация валюты будет происходить на основании курсов, предоставленных сервисом <a href="https://www.currencyconverterapi.com/" target="_blank">https://www.currencyconverterapi.com/</a> .<br/>
Если курсы данного сервиса Вам не устраивают, Вы можете самостоятельно сконвертировать Валюты, передать их в Robofeed XML и выбрать в данном поле <b>"Оставлять цены в переданных валютах"</b>.<br/>
<br/>
<b>Внимание!</b> Некоторые торговые площадки могут не поддерживать передаваемую Вами валюту. В таких случаях мы будем вынуждены принудительно переконвертировать валюту товара и стоимость в Российский рубль либо в валюту, которую торговая площадка считает основной.
$strMainCurrency
DOCHERE
                )),

            '@handler_settings__DOMAIN_LINK' => (new Field\InputText())->setTitle('Ссылка на сайт')
                ->setName('HANDLER_RULES[@handler_settings][DOMAIN_LINK]')
                ->setIsRequired()
                ->setValue($this->getHandlerRules()['@handler_settings']['DOMAIN_LINK'] ?? '')
                ->setPlaceholder('https://example.com'),

            '@handler_settings__UTM' => (new Field\InputText())->setTitle('UTM и другие метки')
                ->setName('HANDLER_RULES[@handler_settings][UTM]')
                ->setValue($this->getHandlerRules()['@handler_settings']['UTM'] ?? '')
                ->setPlaceholder('utm_source=google&utm_medium=cpc'),

            '@handler_settings__DEFAULT_WIDTH' => (new Field\InputText())->setTitle('Ширина товаров по умолчанию, мм')
                ->setDescription('Будет применятся для рассчетов ширины и габаритов, если у товара не проставлено значение.')
                ->setName('HANDLER_RULES[@handler_settings][DEFAULT_WIDTH]')
                ->setIsRequired()
                ->setValue($this->getHandlerRules()['@handler_settings']['DEFAULT_WIDTH'] ?? '100')
                ->setPlaceholder('100'),

            '@handler_settings__DEFAULT_HEIGHT' => (new Field\InputText())->setTitle('Высота товаров по умолчанию, мм')
                ->setDescription('Будет применятся для рассчетов высоты и габаритов, если у товара не проставлено значение.')
                ->setName('HANDLER_RULES[@handler_settings][DEFAULT_HEIGHT]')
                ->setIsRequired()
                ->setValue($this->getHandlerRules()['@handler_settings']['DEFAULT_HEIGHT'] ?? '100')
                ->setPlaceholder('100'),

            '@handler_settings__DEFAULT_LENGTH' => (new Field\InputText())->setTitle('Длина товаров по умолчанию, мм')
                ->setDescription('Будет применятся для рассчетов длины и габаритов, если у товара не проставлено значение.')
                ->setName('HANDLER_RULES[@handler_settings][DEFAULT_LENGTH]')
                ->setIsRequired()
                ->setValue($this->getHandlerRules()['@handler_settings']['DEFAULT_LENGTH'] ?? '100')
                ->setPlaceholder('100'),

            '@handler_settings__DEFAULT_WEIGHT' => (new Field\InputText())->setTitle('Вес товаров по умолчанию, грамм')
                ->setDescription('Будет использоваться, если у товара не проставлено значение')
                ->setName('HANDLER_RULES[@handler_settings][DEFAULT_WEIGHT]')
                ->setIsRequired()
                ->setValue($this->getHandlerRules()['@handler_settings']['DEFAULT_WEIGHT'] ?? '1000')
                ->setPlaceholder('1000'),
        ];
    }

    /**
     * Получить поля конкретного обработчика
     *
     * @return array
     */
    abstract protected function getHandlerFields();


    /** @var array $arTradingPlatformData Массив fetch от ORM ТП */
    protected $arTradingPlatformData = [];

    /**
     * Загрузить в обработчик данные о ТП
     *
     * @param array $ar Массив fetch от ORM ТП
     *
     * @return $this
     */
    public function fillTradingPlatformData($ar)
    {
        $this->arTradingPlatformData = $ar;
    }

    /**
     * Получить загруженные правила обработчика
     *
     * @return array
     */
    protected function getHandlerRules()
    {
        if (is_null($this->arTradingPlatformData['HANDLER_RULES']) || empty($this->arTradingPlatformData['HANDLER_RULES'])) {
            $this->arTradingPlatformData['HANDLER_RULES'] = [];
        }

        return $this->arTradingPlatformData['HANDLER_RULES'];
    }

    /**
     * Получить ID магазина из данных ТП
     *
     * @return mixed
     */
    protected function getTradingPlatformStoreId()
    {
        return $this->arTradingPlatformData['STORE_ID'];
    }

    /**
     * Метод проверяет корректность правил заполненого ТП.<br/>
     * Точнее заполнены ли его обязательные поля.
     *
     * @return \Bitrix\Main\Result
     */
    public function isRulesTradingPlatformCorrectFilled()
    {
        $obResult = new \Bitrix\Main\Result();

        try {
            if (empty($this->getHandlerRules())) {
                throw new \Exception('Для проверки необходимо загрузить данные по торговоей площадки.');
            }

            foreach ($this->getFields() as $strPath => $obField) {
                if (!($obField instanceof \Local\Core\Inner\TradingPlatform\Field\AbstractField)) {
                    continue;
                }

                if (!$obField->getIsRequired()) {
                    continue;
                }

                $arTpFieldData = $this->getTPFieldDataByFieldName($obField->getName());
                if (!$obField->isValueFilled($arTpFieldData)) {
                    $obResult->addError(new \Bitrix\Main\Error('Обязательное поле "'.$obField->getTitle().'" должно быть заполнено.'));
                }
            }
        } catch (\Exception $e) {
            $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
        }

        return $obResult;
    }

    /**
     * Извлекает данные поля (не конечнее значение, а данные, которые позже используются для получения конечного значения),
     * которое хранится у ТП, по пути Field\AbstractField ->getName().<br/><br/>
     * Возвращаемое значение либо null, что означает что данных по полю не найдено
     * совсем (поле не задано, вероятно ошибка при сохранении),
     * либо mixed, что означает, что значение есть, хоть пустое или bool, но есть.
     *
     * @param $strFieldName
     *
     * @return mixed|null
     */
    protected function getTPFieldDataByFieldName($strFieldName)
    {
        $mixReturn = null;
        if (preg_match_all('/\[([^\]]+)\]/', $strFieldName, $matches) > 0) {
            foreach ($matches[1] as $strPath) {
                if (is_null($mixReturn)) {
                    if (array_key_exists($strPath, $this->getHandlerRules())) {
                        $mixReturn = $this->getHandlerRules()[$strPath];
                    } else {
                        break;
                    }
                } else {
                    if (array_key_exists($strPath, $mixReturn)) {
                        $mixReturn = $mixReturn[$strPath];
                    } else {
                        $mixReturn = null;
                        break;
                    }
                }
            }
        }
        return $mixReturn;
    }

    /* ****** */
    /* EXPORT */
    /* ****** */

    /**
     * Создает файл на экспорт
     *
     * @return \Bitrix\Main\Result
     */
    public function makeExportFile()
    {
        $obResult = new \Bitrix\Main\Result();

        try {
            if ($this->arTradingPlatformData['ACTIVE'] == 'N') {
                throw new \Exception('Торговая площадка деактивирована.');
            }

            if (empty($this->getTradingPlatformStoreId())) {
                throw new \Local\Core\Inner\TradingPlatform\Exceptions\StoreIdNotDefined();
            }

            if (!\Local\Core\Inner\Store\Base::hasSuccessImport($this->getTradingPlatformStoreId())) {
                throw new \Exception('У магазина не было еще ни одного успешного импорта.');
            }

            if (is_null(\Local\Core\Inner\TradingPlatform\Base::getExportFileLink($this->arTradingPlatformData['ID']))) {
                throw new \Exception('Не удалось сформировать путь сохранения экспортного файла.');
            }

            if (file_exists($this->getExportFilePath(true))) {
                unlink($this->getExportFilePath(true));
            }

            $this->executeMakeExportFile($obResult);

            if ($obResult->isSuccess()) {
                copy($this->getExportFilePath(true), $this->getExportFilePath(false));
                unlink($this->getExportFilePath(true));
            }

        } catch (\Local\Core\Inner\TradingPlatform\Exceptions\StoreIdNotDefined $e) {
            $obResult->addError(new \Bitrix\Main\Error('Магазин не задан.'));
        } catch (\Exception $e) {
            $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
        } catch (\Throwable $e) {
            $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
        }
        finally {
            if (!$obResult->isSuccess()) {
                unlink($this->getExportFilePath(true));
            }
        }

        return $obResult;
    }

    /**
     * Входной метод формирования экспортного файла у хэндлера.<br/>
     * Перед его инициализацией происходит проверка наличия данных и успешной выгрузки
     *
     * @param \Bitrix\Main\Result $obResult
     */
    abstract protected function executeMakeExportFile(\Bitrix\Main\Result $obResult);

    /**
     * Метод, который вызывается внутри товаров, прошедних фильтрацию
     *
     * @param \Bitrix\Main\Result $obResult
     * @param array               $arExportProductData Массив полей продукта, его св-в и доставок
     */
    abstract protected function beginOfferForeachBody(\Bitrix\Main\Result $obResult, $arExportProductData);

    /**
     * Запускает цепочку фильтрации товаров и дальше инициализирует обработку тела.
     *
     * @param \Bitrix\Main\Result $obResult
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function beginFilterProduct(\Bitrix\Main\Result $obResult)
    {
        $strPhpProductCondition = \Local\Core\Inner\Condition\Base::generatePhp($this->arTradingPlatformData['PRODUCT_FILTER'], $this->getTradingPlatformStoreId(), '$arExportProductData');

        $arProductsIdList = $this->exportGetProductIdList();
        $arProductsIdList = array_chunk($arProductsIdList, \Bitrix\Main\Config\Configuration::getInstance()->get('tradingplatform')['export']['batch_size'] ?? 50);

        $arHandlerSettings = $this->getHandlerRules()['@handler_settings'];

        foreach ($arProductsIdList as $arBatch) {
            $rsProducts = \Local\Core\Model\Robofeed\StoreProductFactory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getTradingPlatformStoreId()))
                ->setStoreId($this->getTradingPlatformStoreId())::getList([
                    'filter' => ['ID' => $arBatch]
                ]);
            $arProducts = $rsProducts->fetchAll();

            $rsParams = \Local\Core\Model\Robofeed\StoreProductParamFactory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getTradingPlatformStoreId()))
                ->setStoreId($this->getTradingPlatformStoreId())::getList([
                    'filter' => [
                        'PRODUCT_ID' => $arBatch
                    ],
                    'select' => [
                        'CODE',
                        'NAME',
                        'VALUE',
                        'PRODUCT_ID'
                    ]
                ]);
            $arParamsByProdId = [];
            while ($ar = $rsParams->fetch()) {
                $intProdId = $ar['PRODUCT_ID'];
                unset($ar['PRODUCT_ID']);
                $arParamsByProdId[$intProdId]['PARAMS'][$ar['CODE']] = $ar;
                $arParamsByProdId[$intProdId]['PARAM_'.$ar['CODE']] = $ar['VALUE'];
            }

            $arDelivery = [];
            $rsDelivery = \Local\Core\Model\Robofeed\StoreProductDeliveryFactory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getTradingPlatformStoreId()))
                ->setStoreId($this->getTradingPlatformStoreId())::getList([
                    'filter' => [
                        'PRODUCT_ID' => $arBatch
                    ]
                ]);
            while ($ar = $rsDelivery->fetch()) {
                $intProdId = $ar['PRODUCT_ID'];
                unset($ar['PRODUCT_ID'], $ar['ROBOFEED_VERSION'], $ar['DATE_CREATE'], $ar['ID']);
                $arDelivery[$intProdId][] = $ar;
            }

            $arPickup = [];
            $rsPickup = \Local\Core\Model\Robofeed\StoreProductPickupFactory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getTradingPlatformStoreId()))
                ->setStoreId($this->getTradingPlatformStoreId())::getList([
                    'filter' => [
                        'PRODUCT_ID' => $arBatch
                    ]
                ]);
            while ($ar = $rsPickup->fetch()) {
                $intProdId = $ar['PRODUCT_ID'];
                unset($ar['PRODUCT_ID'], $ar['ROBOFEED_VERSION'], $ar['DATE_CREATE'], $ar['ID']);
                $arPickup[$intProdId][] = $ar;
            }

            global $arExportProductData;
            foreach ($arProducts as $arExportProductData) {
                $arExportProductData = array_merge(
                    $arExportProductData,
                    $arParamsByProdId[$arExportProductData['ID']],
                    ['DELIVERY_OPTIONS' => $arDelivery[$arExportProductData['ID']]],
                    ['PICKUP_OPTIONS' => $arPickup[$arExportProductData['ID']]],
                    ['@HANDLER_SETTINGS' => $arHandlerSettings]
                );
                if ($arExportProductData['EXPIRY_DATE'] instanceof \Bitrix\Main\Type\DateTime) {
                    $arExportProductData['EXPIRY_DATE'] = $arExportProductData['EXPIRY_DATE']->getTimestamp();
                }

                if (empty($arExportProductData['WEIGHT'])) {
                    $arExportProductData['WEIGHT'] = $arHandlerSettings['DEFAULT_WEIGHT'];
                    $arExportProductData['WEIGHT_UNIT_CODE'] = 'GRM';
                }

                if (empty($arExportProductData['WIDTH'])) {
                    $arExportProductData['WIDTH'] = $arHandlerSettings['DEFAULT_WIDTH'];
                    $arExportProductData['WIDTH_UNIT_CODE'] = 'MMT';
                }

                if (empty($arExportProductData['HEIGHT'])) {
                    $arExportProductData['HEIGHT'] = $arHandlerSettings['DEFAULT_HEIGHT'];
                    $arExportProductData['HEIGHT_UNIT_CODE'] = 'MMT';
                }

                if (empty($arExportProductData['LENGTH'])) {
                    $arExportProductData['LENGTH'] = $arHandlerSettings['DEFAULT_LENGTH'];
                    $arExportProductData['LENGTH_UNIT_CODE'] = 'MMT';
                }

                $arExportProductData['IMAGE'] = array_diff(explode("\r\n", $arExportProductData['IMAGE']), [''], [null]);

                if (eval('return '.$strPhpProductCondition.';')) {
                    $this->beginOfferForeachBody($obResult, $arExportProductData);
                }
            }
        }
    }

    /**
     * Получить полный список товаров магазина
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function exportGetProductIdList()
    {
        $arProductsIdList = [];
        $rsProducts = \Local\Core\Model\Robofeed\StoreProductFactory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getTradingPlatformStoreId()))
            ->setStoreId($this->getTradingPlatformStoreId())::getList(['select' => ['ID']]);
        while ($ar = $rsProducts->fetch()) {
            $arProductsIdList[] = $ar['ID'];
        }
        return $arProductsIdList;
    }

    protected $arExportFilePath = [];

    /**
     * Возвращает абсолютный путь до файла экспорта.<br/>
     * По умолчанию возвращает путь к временному файлу.
     *
     * @param bool $boolIsTmp true - временный, false - окончательный
     *
     * @return string
     */
    protected function getExportFilePath($boolIsTmp = true)
    {
        $strPath = null;
        if (!is_null(\Local\Core\Inner\TradingPlatform\Base::getExportFileLink($this->arTradingPlatformData['ID']))) {
            if ($boolIsTmp) {
                if (is_null($this->arExportFilePath['TMP'])) {
                    if (!file_exists(dirname(\Bitrix\Main\Application::getDocumentRoot().\Local\Core\Inner\TradingPlatform\Base::getExportFileLink($this->arTradingPlatformData['ID'])))) {
                        mkdir(dirname(\Bitrix\Main\Application::getDocumentRoot().\Local\Core\Inner\TradingPlatform\Base::getExportFileLink($this->arTradingPlatformData['ID'])), 0777, true);
                    }

                    $this->arExportFilePath['TMP'] = \Bitrix\Main\Application::getDocumentRoot().\Local\Core\Inner\TradingPlatform\Base::getExportFileLink($this->arTradingPlatformData['ID']).'_TMP';
                }
                $strPath = $this->arExportFilePath['TMP'];
            } else {
                if (is_null($this->arExportFilePath['FINAL'])) {
                    if (!file_exists(dirname(\Bitrix\Main\Application::getDocumentRoot().\Local\Core\Inner\TradingPlatform\Base::getExportFileLink($this->arTradingPlatformData['ID'])))) {
                        mkdir(dirname(\Bitrix\Main\Application::getDocumentRoot().\Local\Core\Inner\TradingPlatform\Base::getExportFileLink($this->arTradingPlatformData['ID'])), 0777, true);
                    }

                    $this->arExportFilePath['FINAL'] = \Bitrix\Main\Application::getDocumentRoot().\Local\Core\Inner\TradingPlatform\Base::getExportFileLink($this->arTradingPlatformData['ID']);
                }
                $strPath = $this->arExportFilePath['FINAL'];
            }
        }
        return $strPath;
    }

    /**
     * Получить формат экспортного файла
     *
     * @return string
     */
    abstract public function getExportFileFormat();

    /**
     * Добавляет запись во временный файл
     *
     * @param $str
     */
    protected function addToTmpExportFile($str)
    {
        $f = fopen($this->getExportFilePath(true), 'a');
        fwrite($f, $str);
        fclose($f);
    }

    /**
     * Извлекает значение из объекта Field\AbstractField.
     * Для того что бы извлечение прошло успешно необходимо иметь значение в $this->getFields()['#field__path#']
     *  и $this->getHandlerRules()['field']['path']
     * Пример использования:
     * <pre>
     * $strName = $this->getFields()['shop__name'];
     * </pre>
     *
     * @param Field\AbstractField $obField
     * @param array               $arAdditionalData
     *
     * @return mixed
     */
    protected function extractFilledValueFromRule(\Local\Core\Inner\TradingPlatform\Field\AbstractField $obField, $arAdditionalData = [])
    {
        return $obField->extractValue($this->getTPFieldDataByFieldName($obField->getName()), $arAdditionalData);
    }


    /**
     * Определить финальную валюту, к которой будет приведена стоимость
     *
     * @param string $strCurrentCurrency  Текущий код валюты
     *
     * @return |null
     */
    protected function getFinalCurrency($strCurrentCurrency)
    {
        $strReturn = null;

        switch ($this->extractFilledValueFromRule($this->getFields()['@handler_settings__CONVERT_CURRENCY_TO']))
        {
            case 'NOT_CONVERT':
                $strReturn = $strCurrentCurrency;
                break;

            default:
                $strReturn = $this->extractFilledValueFromRule($this->getFields()['@handler_settings__CONVERT_CURRENCY_TO']);
                break;
        }

        if( !in_array($strReturn, static::getSupportedCurrency()) )
        {
            $strReturn = static::getMainCurrency();
        }

        return $strReturn;
    }

    /**
     * Конвертирует массив структуры "\Spatie\ArrayToXml\ArrayToXml" в строку
     *
     * @param array  $arOfferXml массив структуры "\Spatie\ArrayToXml\ArrayToXml"
     * @param string $elemName   название дочернего элемента
     *
     * @return string
     */
    protected function convertArrayToString($arOfferXml, $elemName)
    {
        $q = \Spatie\ArrayToXml\ArrayToXml::convert($arOfferXml, $elemName, false, 'UTF-8', '1.0');
        return trim(str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $q));
    }
}