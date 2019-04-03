<?php

namespace Local\Core\Inner\Robofeed\Validator;

use Local\Core\Inner\Exception\FatalException;
use Local\Core\Inner\Route;

abstract class AbstractValidator
{
    use \Local\Core\Inner\Robofeed\Traites\AbstractClass;

    /**
     * Производит валидацию значения и в случае ошибки сообщает в $obResult
     *
     * @param string                                              $strValue
     * @param \Local\Core\Inner\Robofeed\SchemaFields\ScalarField $obField
     * @param \Bitrix\Main\Result                                 $obResult
     *
     * @return bool
     * @throws FatalException
     */
    public static function validateValue(string $strValue, $obField, \Bitrix\Main\Result &$obResult)
    {
        if (!in_array(\Local\Core\Inner\Robofeed\Interfaces\ScalarField::class, class_implements(get_class($obField)))) {
            throw new FatalException('Поля, описывающие схему валидации, должны быть потомками '.\Local\Core\Inner\Robofeed\SchemaFields\ScalarField::class.'. Поле - '.$obField->getName());
        }

        $obValidResult = new \Bitrix\Main\ORM\Data\AddResult();

        $obField->validateValue($strValue, null, [], $obValidResult);

        if (!$obValidResult->isSuccess()) {
            $obErrorsFields = $obValidResult->getErrors();

            /** @var \Bitrix\Main\ORM\Fields\FieldError $obErrorsField */
            /*
            // В этом фрагменте ранее перебирались все ошибки одного поля, но это не логично
            foreach( $obErrorsFields as $obErrorsField )
            {
                dump($obErrorsField->getCode());

                $this->fillValidateErrorByCode($obErrorsField->getCode(), $obField, $obResult);
            }
            */
            self::sendErrorByCodeToResult($obErrorsFields[0]->getCode(), $obField, $obResult);
        }

        return $obValidResult->isSuccess();
    }


    /**
     * Отправляет описание ошибки в $obResult по ее коду
     *
     * @param string                                              $strErrorCode Код ошибки
     * @param \Local\Core\Inner\Robofeed\SchemaFields\ScalarField $obField      Объект скалярного поля
     * @param \Bitrix\Main\Result                                 $obResult     Объект результата ORM битрикса
     */
    public static function sendErrorByCodeToResult($strErrorCode, $obField, &$obResult)
    {
        $strErrorText = '';

        switch ($strErrorCode) {
            case 'LOCAL_CORE_FIELD_IS_REQUIRED':
                $strErrorText = 'Обязательное поле "'.$obField->getTitle().'" не заполнено.';
                break;

            case 'LOCAL_CORE_INVALID_VALUE':
                $strErrorText = 'Поле "'.$obField->getTitle().'" имеет недопустимое значение.';
                break;

            case 'LOCAL_CORE_INVALID_VALUE_FORMAT_DATETIME':
                $strErrorText = 'Поле "'.$obField->getTitle().'" имеет не правильный формат даты и времени.';
                break;

            case 'LOCAL_CORE_INVALID_VALUE_FORMAT_DATE':
                $strErrorText = 'Поле "'.$obField->getTitle().'" имеет не правильный формат даты.';
                break;

            case 'LOCAL_CORE_INVALID_VALUE_TABOO_ASCII_CHARS':
                $strErrorText = 'Поле "'.$obField->getTitle().'" не должно иметь непечатаемые символы с ASCII-кодами от 0 до 31 (за исключением символов с кодами 9, 10, 13).';
                break;

            case 'LOCAL_CORE_INVALID_VALUE_CANT_HTML':
                $strErrorText = 'Поле "'.$obField->getTitle().'" html не должно иметь тэгов.';
                break;

            case 'LOCAL_CORE_INVALID_VALUE_CDATA_LIMIT':
                $strErrorText = 'В поле "'.$obField->getTitle().'" из всех тегов разрешены только &lt;h3&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;p&gt;, &lt;br/&gt;.';
                break;

            case 'LOCAL_CORE_INVALID_VALUE_REF_CLASS_NOT_EXIST':
                $strErrorText = 'Поле "'.$obField->getTitle()
                                .'" не может быть проверено, т.к. справочник не доступен. Пожалуйста, напишите на info@robofeed.ru что бы мы исправили ошибку, вероятно мы еще не курсе!';
                // TODO сделал записть в логер о критической ошибке
                break;

            case 'LOCAL_CORE_REF_INVALID_VALUE':
                $strErrorText = 'Поле "'.$obField->getTitle().'" должно содержать значение из справочника. Пожалуйста, изучите справочники https://robofeed.ru/'.Route::getRouteTo('development',
                        'references');
                break;

            case 'LOCAL_CORE_DELIVERY_EMPTY':
                $strErrorText = 'Вы отметили доставку как возможную, но не перечислили ни одного условия доставки.';
                break;

            case 'LOCAL_CORE_DELIVERY_NO_ONE_OPTION_NOT_VALID':
                $strErrorText = 'Вы отметили доставку как возможную, перечислили условия доставки, но ни одно из них не валидно.';
                break;

            case 'LOCAL_CORE_PICKUP_EMPTY':
                $strErrorText = 'Вы отметили самовывоз как возможный, но не перечислили ни одного условия самовывоза.';
                break;

            case 'LOCAL_CORE_PICKUP_NO_ONE_OPTION_NOT_VALID':
                $strErrorText = 'Вы отметили самовывоз как возможную, перечислили условия самовывоза, но ни одно из них не валидно.';
                break;

            case 'LOCAL_CORE_UNDEFINED_CATEGORY':
                $strErrorText = 'Поле "'.$obField->getTitle().'" описано в товаре, но этого ID нет среди списка категорий.';
                break;

            case 'LOCAL_CORE_NOT_UNIT_WEIGHT':
                $strErrorText = 'Указан вес товара, но не указана единица измерения.';
                break;

            case 'LOCAL_CORE_NOT_UNIT_WIDTH':
                $strErrorText = 'Указана ширина товара, но не указана единица измерения.';
                break;

            case 'LOCAL_CORE_NOT_UNIT_HEIGHT':
                $strErrorText = 'Указана высота товара, но не указана единица измерения.';
                break;

            case 'LOCAL_CORE_NOT_UNIT_LENGTH':
                $strErrorText = 'Указана длина товара, но не указана единица измерения.';
                break;

            case 'LOCAL_CORE_NOT_UNIT_VOLUME':
                $strErrorText = 'Указан объем товара, но не указана единица измерения.';
                break;

            case 'LOCAL_CORE_NOT_UNIT_WARRANTY':
                $strErrorText = 'Указан срок гарантии товара, но не указана единица измерения.';
                break;

            case 'LOCAL_CORE_NOT_UNIT_EXPIRY':
                $strErrorText = 'Указан срок годности товара, но не указана единица измерения.';
                break;

            default:
                $strErrorText = 'Поле "'.$obField->getTitle().'" - произошла иная ошибка.';
                break;
        }

        if (!is_null($obField->getXmlPath())) {
            $strErrorText .= '<br/>Путь: '.$obField->getXmlPath();
        }

        if (!is_null($obField->getXmlExpectedType())) {
            $strErrorText .= '<br/>Ожидаемые данные: '.$obField->getXmlExpectedType();
        }

        $obResult->addError(new \Bitrix\Main\Error($strErrorText));
    }

    /**
     * Извлекает аттрибуты элемента.
     *
     * @param \SimpleXMLElement $obElem
     *
     * @return array
     */
    protected function getAttrs(\SimpleXMLElement $obElem)
    {
        $arAttrs = [];
        if ($obElem instanceof \SimpleXMLElement) {
            foreach ($obElem->attributes() as $k => $v) {
                $arAttrs[$k] = (string)$v;
            }
        }
        return $arAttrs;
    }
}