<?php

namespace Local\Core\Inner\TradingPlatform\Handler\AutoruParts;

use \Local\Core\Inner\Route;
use \Local\Core\Inner\TradingPlatform\Field;

class Handler extends \Local\Core\Inner\TradingPlatform\Handler\YandexMarket\Handler
{
    /** @inheritDoc */
    public static function getCode()
    {
        return 'autoru_parts';
    }

    /** @inheritDoc */
    public static function getTitle()
    {
        return 'Auto.ru/Запчасти';
    }

    protected function getShopBaseFields()
    {
        $ar = parent::getShopBaseFields();
        unset($ar['shop__platform'], $ar['shop__version'], $ar['shop__agency'], $ar['shop__email']);
        return $ar;
    }

    protected function fillShopHeader(\Bitrix\Main\Result $obResult)
    {
        if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__name']))) {
            $this->addToTmpExportFile('<name>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__name'])).'</name>');
        }
        if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__company']))) {
            $this->addToTmpExportFile('<company>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__company'])).'</company>');
        }
        if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__url']))) {
            $this->addToTmpExportFile('<url>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__url'])).'</url>');
        }
    }
}