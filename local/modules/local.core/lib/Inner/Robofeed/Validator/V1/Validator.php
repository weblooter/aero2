<?php

namespace Local\Core\Inner\Robofeed\Validator\V1;


use Local\Core\Inner\Robofeed\Validator\AbstractValidator;

class Validator extends \Local\Core\Inner\Robofeed\Validator\AbstractValidator
{
    /** @inheritdoc */
    public static function getVersion()
    {
        return 1;
    }

    /**
     * @return \Bitrix\Main\Result|void
     * @deprecated
     *
     */
    public function run()
    {
    }

    public function validateCategories($obXml, $obFields)
    {
        $obResult = new \Bitrix\Main\Result();

        $arCategories = [];

        foreach ($obXml->category as $obXmlCategory) {
            $obCategoryResult = new \Bitrix\Main\Result();
            $arCategory = [];
            $arAttrs = $this->getAttrs($obXmlCategory);
            foreach ($obFields['@attr'] as $attrName => $obField) {
                if (self::validateValue($arAttrs[$attrName] ?? '', $obField, $obCategoryResult)) {
                    $arCategory['@attr'][$attrName] = $obField->getValidValue($arAttrs[$attrName] ?? '');
                }
            }

            if (self::validateValue((string)$obXmlCategory ?? '', $obFields['@value'], $obCategoryResult)) {
                $arCategory['@value'] = $obFields['@value']->getValidValue((string)$obXmlCategory ?? '');
            }

            if ($obCategoryResult->isSuccess()) {
                $arCategories[$arCategory['@attr']['id']] = $arCategory;
            } else {
                $obResult->addErrors($obCategoryResult->getErrors());
            }
        }

        $obResult->setData($arCategories);

        return $obResult;
    }

    public function validateDefaultValuesOffer($obXml, $obFields)
    {
        $obResult = new \Bitrix\Main\Result();
        $arDefaultValue = [];

        foreach ($obFields as $elemName => $obField) {
            if (isset($obXml->$elemName)) {

                switch ($elemName) {
                    case 'article':
                    case 'param':
                    case 'image':
                        continue;
                        break;

                    case 'delivery':

                        $obDeliveryValidResult = $this->validDelivery($obXml->$elemName, $obField, 'robofeed->defaultValues->offer');
                        if (!empty($obDeliveryValidResult->getData())) {
                            $arDefaultValue['delivery'] = $obDeliveryValidResult->getData();
                        }
                        if (!$obDeliveryValidResult->isSuccess()) {
                            $obResult->addErrors($obDeliveryValidResult->getErrors());
                        }

                        break;

                    case 'pickup':

                        $obPickupValidResult = $this->validPickup($obXml->$elemName, $obField, 'robofeed->defaultValues->offer');
                        if (!empty($obPickupValidResult->getData())) {
                            $arDefaultValue['pickup'] = $obPickupValidResult->getData();
                        }
                        if (!$obPickupValidResult->isSuccess()) {
                            $obResult->addErrors($obPickupValidResult->getErrors());
                        }

                        break;

                    default:
                        if (self::validateValue((string)$obXml->$elemName, $obField, $obResult)) {
                            if (!is_null($obField->getValidValue((string)$obXml->$elemName))) {
                                $arDefaultValue[$elemName] = $obField->getValidValue((string)$obXml->$elemName);
                            }
                        }
                        break;
                }
            }
        }

        $obResult->setData($arDefaultValue);

        return $obResult;
    }

    public function validateOffer($obXml, $obFields, $arDefaultValues, $arCategories)
    {
        $obResult = new \Bitrix\Main\Result();


        $obOfferResult = new \Bitrix\Main\Result();
        $arOfferFields = [];

        $arOfferAttr = $this->getAttrs($obXml);
        // Проверка аттрибутов и запись, если импорт
        foreach ($obFields['@attr'] as $attrName => $obField) {
            if (self::validateValue($arOfferAttr[$attrName] ?? '', $obField, $obOfferResult)) {
                $arOfferFields['@attr'][$attrName] = $obField->getValidValue($arOfferAttr[$attrName] ?? '');
            }
        }

        // Проверка всех полей и запись, если импорт
        foreach ($obFields['@value'] as $elemName => $obField) {
            $obOfferValRes = new \Bitrix\Main\Result();
            $mixDefaultVal = $arDefaultValues[$elemName];


            switch ($elemName) {

                case 'article':
                    if (self::validateValue((string)$obXml->$elemName, $obField, $obOfferValRes)) {
                        $arOfferFields['@value'][$elemName] = (string)$obXml->$elemName;
                    }
                    break;

                case 'image':
                    if (sizeof($obXml->$elemName) > 0) {
                        $arImages = [];

                        foreach ($obXml->$elemName as $obXmlImage) {
                            if (self::validateValue((string)$obXmlImage, $obField, $obOfferValRes)) {
                                $arImages[] = $obField->getValidValue((string)$obXmlImage);
                            }
                        }
                        $arOfferFields['@value'][$elemName] = $arImages;
                        unset($arImages);
                    }
                    break;

                case 'param':
                    if (sizeof($obXml->$elemName) > 0) {
                        foreach ($obXml->$elemName as $obXmlParam) {
                            $arParamFields = [];
                            $obParamResult = new \Bitrix\Main\Result();

                            $arAttrParam = $this->getAttrs($obXmlParam);
                            foreach ($obField['@attr'] as $k => $v) {
                                if (self::validateValue($arAttrParam[$k] ?? '', $v, $obParamResult)) {
                                    $arParamFields['@attr'][$k] = $v->getValidValue($arAttrParam[$k] ?? '');
                                }
                            }

                            if (self::validateValue((string)$obXmlParam, $obField['@value'], $obParamResult)) {
                                $arParamFields['@value'] = $obField['@value']->getValidValue((string)$obXmlParam);
                            }

                            if ($obParamResult->isSuccess()) {
                                $arOfferFields['@value'][$elemName][] = $arParamFields;
                            } else {
                                $obOfferValRes->addErrors($obParamResult->getErrors());
                            }
                        }
                    }
                    break;

                case 'delivery':
                    if (isset($obXml->$elemName)) {
                        $obDeliveryValidResult = $this->validDelivery($obXml->$elemName, $obField, 'robofeed->offers->offer');
                        if (!$obDeliveryValidResult->isSuccess()) {
                            $obOfferValRes->addErrors($obDeliveryValidResult->getErrors());
                        }
                        if (!empty($obDeliveryValidResult->getData())) {
                            $arOfferFields['@value'][$elemName] = $obDeliveryValidResult->getData();
                        }
                    } else {
                        if (!is_null($mixDefaultVal)) {
                            $arOfferFields['@value'][$elemName] = $mixDefaultVal;
                        } else {
                            // Нет информации по доставке
                            self::sendErrorByCodeToResult('LOCAL_CORE_FIELD_IS_REQUIRED', new \Local\Core\Inner\Robofeed\SchemaFields\StringField('delivery__option', [
                                'title' => 'Группа доставки',
                                'reqired' => true,
                                'xml_path' => 'robofeed->offers->offer->delivery',
                                'xml_expected_type' => 'валидные условия самовывоза'
                            ]), $obOfferValRes);
                        }
                    }
                    break;

                case 'pickup':
                    if (isset($obXml->$elemName)) {
                        $obPickupValidResult = $this->validPickup($obXml->$elemName, $obField, 'robofeed->offers->offer');
                        if (!$obPickupValidResult->isSuccess()) {
                            $obOfferValRes->addErrors($obPickupValidResult->getErrors());
                        }
                        $arOfferFields['@value'][$elemName] = $obPickupValidResult->getData();
                    } else {
                        if (!is_null($mixDefaultVal)) {
                            $arOfferFields['@value'][$elemName] = $mixDefaultVal;
                        } else {
                            // Нет информации по самовывозу
                            self::sendErrorByCodeToResult('LOCAL_CORE_FIELD_IS_REQUIRED', new \Local\Core\Inner\Robofeed\SchemaFields\StringField('pickup__option', [
                                'title' => 'Группа самовывоза',
                                'reqired' => true,
                                'xml_path' => 'robofeed->offers->offer->pickup',
                                'xml_expected_type' => 'валидные условия самовывоза'
                            ]), $obOfferValRes);
                        }
                    }
                    break;


                default:

                    if (isset($obXml->$elemName)) {
                        if (self::validateValue((string)$obXml->$elemName, $obField, $obOfferValRes)) {
                            $arOfferFields['@value'][$elemName] = $obField->getValidValue((string)$obXml->$elemName);
                        }
                    } else {
                        if (!is_null($mixDefaultVal)) {
                            $arOfferFields['@value'][$elemName] = $mixDefaultVal;
                        } else {
                            self::validateValue('', $obField, $obOfferValRes);
                        }
                    }

                    break;
            }


            if (!$obOfferValRes->isSuccess()) {
                $obOfferResult->addErrors($obOfferValRes->getErrors());
            }
        }

        // Финальная проверка
        $this->finalOfferCheck($arOfferFields['@value'], $obOfferResult, $arCategories);

        $obResult->setData($arOfferFields);

        if (!$obOfferResult->isSuccess()) {
            $obResult->addErrors($obOfferResult->getErrors());
        }

        return $obResult;
    }

    /**
     * Валидация доставки
     *
     * @param \SimpleXMLElement $obXmlDelivery XML объект delivery
     * @param array             $obField       филд delivery из маппинга
     * @param string            $xmlPath       Примиска для xml_path. Используется для вывода ошибок из филда, грененрированного на лету
     *
     * @return \Bitrix\Main\Result
     * @throws \Local\Core\Inner\Exception\FatalException
     * @throws \Bitrix\Main\SystemException
     */
    private function validDelivery($obXmlDelivery, $obField, $xmlPath)
    {
        $obReturnResult = new \Bitrix\Main\Result();
        $arReturn = [];

        $arAttrs = $this->getAttrs($obXmlDelivery);
        if (self::validateValue($arAttrs['available'] ?? '', $obField['@attr']['available'], $obReturnResult)) {
            $arReturn['@attr']['available'] = $obField['@attr']['available']->getValidValue($arAttrs['available']);
        }

        if ($arReturn['@attr']['available'] == 'Y') {
            // Доставка заявлена как предоставляемая услуга.
            if (sizeof($obXmlDelivery->option) > 0) {
                // Есть условия доставок
                foreach ($obXmlDelivery->option as $obXmlOption) {
                    // Перебираем условия, валидируя поля

                    $arAttrs = $this->getAttrs($obXmlOption);

                    $obOptionResult = new \Bitrix\Main\Result();

                    foreach ($obField['option']['@attr'] as $obOptionField) {
                        $attrName = substr(strstr($obOptionField->getName(), '@'), 1);
                        self::validateValue($arAttrs[$attrName] ?? '', $obOptionField, $obOptionResult);
                    }

                    if (!$obOptionResult->isSuccess()) {
                        // Среди полей условий были ошибки, добавим в общие ошибки и не будем извелкать и сохранять условие.
                        $obReturnResult->addErrors($obOptionResult->getErrors());
                    } else {
                        $arValues = [];

                        foreach ($obField['option']['@attr'] as $obOptionField) {
                            $attrName = substr(strstr($obOptionField->getName(), '@'), 1);
                            $arValues['@attr'][$attrName] = $obOptionField->getValidValue($arAttrs[$attrName] ?? '');
                        }

                        $arReturn['option'][] = $arValues;
                    }
                }

                // Перебрали все условия, проверим есть ли валидные
                if (empty($arReturn['option'])) {
                    self::sendErrorByCodeToResult('LOCAL_CORE_DELIVERY_NO_ONE_OPTION_NOT_VALID', new \Local\Core\Inner\Robofeed\SchemaFields\StringField('delivery__option', [
                        'title' => '',
                        'reqired' => true,
                        'xml_path' => $xmlPath.'->delivery->option',
                        'xml_expected_type' => 'валидные условия доставки'
                    ]), $obReturnResult);
                }
            } else {
                // Нет условий доставок
                self::sendErrorByCodeToResult('LOCAL_CORE_DELIVERY_EMPTY', new \Local\Core\Inner\Robofeed\SchemaFields\StringField('delivery__option', [
                    'title' => '',
                    'reqired' => true,
                    'xml_path' => $xmlPath.'->delivery->option',
                    'xml_expected_type' => 'валидные условия доставки'
                ]), $obReturnResult);
            }
        }

        $obReturnResult->setData($arReturn);
        return $obReturnResult;
    }

    /**
     * Валидация самовывоза
     *
     * @param \SimpleXMLElement $obXmlPickup XML объект pickup
     * @param array             $obField     филд pickup из маппинга
     * @param string            $xmlPath     Примиска для xml_path. Используется для вывода ошибок из филда, грененрированного на лету
     *
     * @return \Bitrix\Main\Result
     * @throws \Local\Core\Inner\Exception\FatalException
     * @throws \Bitrix\Main\SystemException
     */
    private function validPickup($obXmlPickup, $obField, $xmlPath)
    {
        $obReturnResult = new \Bitrix\Main\Result();
        $arReturn = [];

        $arAttrs = $this->getAttrs($obXmlPickup);
        if (self::validateValue($arAttrs['available'] ?? '', $obField['@attr']['available'], $obReturnResult)) {
            $arReturn['@attr']['available'] = $obField['@attr']['available']->getValidValue($arAttrs['available']);
        }

        if ($arReturn['@attr']['available'] == 'Y') {
            // Самовывоз заявлен как предоставляемая услуга.
            if (sizeof($obXmlPickup->option) > 0) {
                // Есть условия самовывоза
                foreach ($obXmlPickup->option as $obXmlOption) {
                    // Перебираем условия, валидируя поля

                    $arAttrs = $this->getAttrs($obXmlOption);

                    $obOptionResult = new \Bitrix\Main\Result();

                    foreach ($obField['option']['@attr'] as $obOptionField) {
                        $attrName = substr(strstr($obOptionField->getName(), '@'), 1);
                        self::validateValue($arAttrs[$attrName] ?? '', $obOptionField, $obOptionResult);
                    }

                    if (!$obOptionResult->isSuccess()) {
                        // Среди полей условий были ошибки, добавим в общие ошибки и не будем извелкать и сохранять условие.
                        $obReturnResult->addErrors($obOptionResult->getErrors());
                    } else {
                        $arValues = [];

                        foreach ($obField['option']['@attr'] as $obOptionField) {
                            $attrName = substr(strstr($obOptionField->getName(), '@'), 1);
                            $arValues['@attr'][$attrName] = $obOptionField->getValidValue($arAttrs[$attrName] ?? '');
                        }

                        $arReturn['option'][] = $arValues;
                    }
                }

                // Перебрали все условия, проверим есть ли валидные
                if (empty($arReturn['option'])) {
                    self::sendErrorByCodeToResult('LOCAL_CORE_PICKUP_NO_ONE_OPTION_NOT_VALID', new \Local\Core\Inner\Robofeed\SchemaFields\StringField('delivery__option', [
                        'title' => '',
                        'reqired' => true,
                        'xml_path' => $xmlPath.'->pickup->option',
                        'xml_expected_type' => 'валидные условия самовывоза'
                    ]), $obReturnResult);
                }
            } else {
                // Нет условий самовывоза
                self::sendErrorByCodeToResult('LOCAL_CORE_PICKUP_EMPTY', new \Local\Core\Inner\Robofeed\SchemaFields\StringField('delivery__option', [
                    'title' => '',
                    'reqired' => true,
                    'xml_path' => $xmlPath.'->pickup->option',
                    'xml_expected_type' => 'валидные условия самовывоза'
                ]), $obReturnResult);
            }
        }

        $obReturnResult->setData($arReturn);
        return $obReturnResult;
    }

    /**
     * Метод финальной проверки валидности значений в товаре
     *
     * @param                     $arOfferValuesToFinalCheck
     * @param \Bitrix\Main\Result $obOfferCheckResult
     */
    private function finalOfferCheck($arFields, \Bitrix\Main\Result $obOfferCheckResult, $arCategories)
    {
        if (!empty($arFields['categoryId'])) {
            if (empty($arCategories[$arFields['categoryId']])) {
                // Данной категории нет в списке
                AbstractValidator::sendErrorByCodeToResult('LOCAL_CORE_UNDEFINED_CATEGORY', new \Local\Core\Inner\Robofeed\SchemaFields\StringField('delivery__option', [
                    'title' => 'ID категории товара',
                    'xml_path' => 'robofeed->offers->offer->categoryId',
                    'xml_expected_type' => 'валидные условия самовывоза'
                ]), $obOfferCheckResult);
            }
        }


        $funCheckUnit = function ($val, $valUnitCode, $ERROR_CODE, $xmlPath) use ($obOfferCheckResult)
            {
                if (!empty($val) && empty($valUnitCode)) {
                    self::sendErrorByCodeToResult($ERROR_CODE, new \Local\Core\Inner\Robofeed\SchemaFields\StringField('q', [
                        'xml_path' => $xmlPath,
                        'xml_expected_type' => 'значение из справочника'
                    ]), $obOfferCheckResult);
                }
            };

        $funCheckUnit($arFields['weight'], $arFields['weightUnitCode'], 'LOCAL_CORE_NOT_UNIT_WEIGHT', 'robofeed->offers->offer->weightUnitCode');
        $funCheckUnit($arFields['width'], $arFields['widthUnitCode'], 'LOCAL_CORE_NOT_UNIT_WIDTH', 'robofeed->offers->offer->widthUnitCode');
        $funCheckUnit($arFields['height'], $arFields['heightUnitCode'], 'LOCAL_CORE_NOT_UNIT_HEIGHT', 'robofeed->offers->offer->heightUnitCode');
        $funCheckUnit($arFields['length'], $arFields['lengthUnitCode'], 'LOCAL_CORE_NOT_UNIT_LENGTH', 'robofeed->offers->offer->lengthUnitCode');
        $funCheckUnit($arFields['volume'], $arFields['volumeUnitCode'], 'LOCAL_CORE_NOT_UNIT_VOLUME', 'robofeed->offers->offer->volumeUnitCode');
        $funCheckUnit($arFields['warrantyPeriod'], $arFields['warrantyPeriodCode'], 'LOCAL_CORE_NOT_UNIT_WARRANTY', 'robofeed->offers->offer->warrantyPeriodCode');
        $funCheckUnit($arFields['expiryPeriod'], $arFields['expiryPeriodCode'], 'LOCAL_CORE_NOT_UNIT_EXPIRY', 'robofeed->offers->offer->expiryPeriodCode');
    }
}