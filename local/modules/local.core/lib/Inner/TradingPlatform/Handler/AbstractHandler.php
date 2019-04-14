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

    /**
     * Получить набор полей обработчика, как общие, так и индивидуальные
     *
     * @return array
     */
    public function getFields()
    {
        return array_merge($this->getGeneralFields(), $this->getHandlerFields());
    }

    /**
     * Получить общие поля
     *
     * @return array
     */
    protected function getGeneralFields()
    {
        return [
            'header_g1' => (new Field\Header())->setValue('Настройки обработки'),

            '@handler_settings__CONVERT_CURRENCY_TO' => (new Field\Select())->setTitle('Конвертация цен')
                ->setName('HANDLER_RULES[@handler_settings][CONVERT_CURRENCY_TO]')
                ->setIsRequired()
                ->setOptions([
                    'NOT_CONVERT' => 'Оставлять цены в переданных валютах',
                    'BYN' => 'Конвертировать в "Белорусский рубль"',
                    'UAH' => 'Конвертировать в "Гривна"',
                    'KZT' => 'Конвертировать в "Тенге"',
                    'EUR' => 'Конвертировать в "Евро"',
                    'USD' => 'Конвертировать в "Доллар США"',
                ])
                ->setValue($this->getHandlerRules()['@handler_settings']['CONVERT_CURRENCY_TO'] ?? 'NOT_CONVERT')
                ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
Конвертация валюты будет происходить на основании курсов, предоставленным сервисом https://www.currencyconverterapi.com/ .<br/>
Если курсы данного сервиса Вам не устравивают, Вы можете самостоятельно сконвертировать Валюты, передать их в Robofeed XML и выбрать в данном поле <b>"Оставлять цены в переданных валютах"</b>.
DOCHERE
                    )),

            '@handler_settings__DOMAIN_LINK' => (new Field\InputText())->setTitle('Ссылка на сайт')
                ->setName('HANDLER_RULES[@handler_settings][DOMAIN_LINK]')
                ->setIsRequired()
                ->setValue( $this->getHandlerRules()['@handler_settings']['DOMAIN_LINK'] ?? '' )
                ->setPlaceholder('https://example.com'),

            '@handler_settings__UTM' => (new Field\InputText())->setTitle('UTM и другие метки')
                ->setName('HANDLER_RULES[@handler_settings][UTM]')
                ->setValue( $this->getHandlerRules()['@handler_settings']['UTM'] ?? '' )
                ->setPlaceholder('utm_source=google&utm_medium=cpc'),

            '@handler_settings__DEFAULT_WIDTH' => (new Field\InputText())->setTitle('Ширина товаров по умолчанию, мм')
                ->setDescription('Будет применятся для рассчетов ширины и габаритов, если у товара не проставлено значение.')
                ->setName('HANDLER_RULES[@handler_settings][DEFAULT_WIDTH]')
                ->setIsRequired()
                ->setValue( $this->getHandlerRules()['@handler_settings']['DEFAULT_WIDTH'] ?? '100' )
                ->setPlaceholder('100'),

            '@handler_settings__DEFAULT_HEIGHT' => (new Field\InputText())->setTitle('Высота товаров по умолчанию, мм')
                ->setDescription('Будет применятся для рассчетов высоты и габаритов, если у товара не проставлено значение.')
                ->setName('HANDLER_RULES[@handler_settings][DEFAULT_HEIGHT]')
                ->setIsRequired()
                ->setValue( $this->getHandlerRules()['@handler_settings']['DEFAULT_HEIGHT'] ?? '100' )
                ->setPlaceholder('100'),

            '@handler_settings__DEFAULT_LENGTH' => (new Field\InputText())->setTitle('Длина товаров по умолчанию, мм')
                ->setDescription('Будет применятся для рассчетов длины и габаритов, если у товара не проставлено значение.')
                ->setName('HANDLER_RULES[@handler_settings][DEFAULT_LENGTH]')
                ->setIsRequired()
                ->setValue( $this->getHandlerRules()['@handler_settings']['DEFAULT_LENGTH'] ?? '100' )
                ->setPlaceholder('100'),

            '@handler_settings__DEFAULT_WEIGHT' => (new Field\InputText())->setTitle('Вес товаров по умолчанию, грамм')
                ->setDescription('Будет использоваться, если у товара не проставлено значение')
                ->setName('HANDLER_RULES[@handler_settings][DEFAULT_WEIGHT]')
                ->setIsRequired()
                ->setValue( $this->getHandlerRules()['@handler_settings']['DEFAULT_WEIGHT'] ?? '1000' )
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
}