<?php

namespace Local\Core\Inner\TradingPlatform\Handler\YandexMarket;

use \Local\Core\Inner\TradingPlatform\Field;

class Handler extends \Local\Core\Inner\TradingPlatform\Handler\AbstractHandler
{
    /** @inheritDoc */
    public static function getCode()
    {
        return 'yandex_market';
    }

    /** @inheritDoc */
    public static function getTitle()
    {
        return 'Яндекс маркет';
    }

    /** @inheritDoc */
    protected function getHandlerFields()
    {
        return array_merge($this->getShopBaseFields(), $this->getDefaultDeliveryFields(), $this->getOfferFields());
    }

    private function getShopBaseFields()
    {
        return [
            'header_y1' => (new Field\Header())->setValue('Настройки магазина'),

            'shop__name' => (new Field\InputText())->setTitle('Короткое название магазина')
                ->setDescription('Не более 20 символов.')
                ->setName('HANDLER_RULES[shop][name]')
                ->setIsRequired()
                ->setPlaceholder('Рога и копыта')
                ->setValue($this->getHandlerRules()['shop']['name']),

            'shop__company' => (new Field\InputText())->setTitle('Полное наименование компании, владеющей магазином')
                ->setDescription('Не публикуется, используется для внутренней идентификации.')
                ->setName('HANDLER_RULES[shop][company]')
                ->setIsRequired()
                ->setPlaceholder('ООО Рога и копыта')
                ->setValue($this->getHandlerRules()['shop']['company']),

            'shop__url' => (new Field\InputText())->setTitle('URL главной страницы магазина')
                ->setDescription('Максимум 50 символов. Допускаются кириллические ссылки.')
                ->setName('HANDLER_RULES[shop][url]')
                ->setIsRequired()
                ->setPlaceholder('https://example.com')
                ->setValue($this->getHandlerRules()['shop']['url']),

            'shop__platform' => (new Field\InputText())->setTitle('Система управления контентом, на основе которой работает магазин (CMS)')
                ->setName('HANDLER_RULES[shop][platform]')
                ->setPlaceholder('1C-Bitrix')
                ->setValue($this->getHandlerRules()['shop']['platform']),

            'shop__version' => (new Field\InputText())->setTitle('Версия CMS')
                ->setName('HANDLER_RULES[shop][version]')
                ->setPlaceholder('17')
                ->setValue($this->getHandlerRules()['shop']['version']),

            'shop__agency' => (new Field\InputText())->setTitle('Наименование агентства, которое оказывает техническую поддержку магазину и отвечает за работоспособность сайта.')
                ->setName('HANDLER_RULES[shop][agency]')
                ->setValue($this->getHandlerRules()['shop']['agency']),

            'shop__email' => (new Field\InputText())->setTitle('Контактный адрес разработчиков CMS или агентства, осуществляющего техподдержку')
                ->setName('HANDLER_RULES[shop][email]')
                ->setPlaceholder('info@example.com')
                ->setValue($this->getHandlerRules()['shop']['email'])
        ];
    }

    private function getDefaultDeliveryFields()
    {
        return [
            'header_y2' => (new Field\Header())->setValue('Базовые условия доставки'),
        ];
    }

    private function getOfferFields()
    {
        $arFields = [
            'header_y3' => (new Field\Header())->setValue('Список предложений'),

            'shop__offers__offer__@attr__type' => (new Field\Resource())->setTitle('Полное название предложения')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setDescription('Полное название предложения, в которое входит: тип товара, производитель, модель и название товара, важные характеристики.')
                ->setName('HANDLER_RULES[shop][offers][offer][name]')
                ->setIsRequired()
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['name'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_BUILDER,
                    Field\Resource::TYPE_LOGIC,
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['name'] ?? ['TYPE' => 'LOGIC'])
        ];

        return $arFields;
    }
}