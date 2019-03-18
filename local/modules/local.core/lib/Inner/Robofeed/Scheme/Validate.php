<?php

namespace Local\Core\Inner\Robofeed\Scheme;


/**
 * Класс для валидации схемы
 *
 * @package Local\Core\Inner\Robofeed\Scheme
 */
class Validate
{
    /**
     * Производит валидацию значения и в случае ошибки сообщает в $obResult
     *
     * @param string                                              $mixValue
     * @param \Local\Core\Inner\Robofeed\SchemeFields\ScalarField $obField
     * @param \Bitrix\Main\Result                                 $obResult
     *
     * @return bool
     * @throws \Bitrix\Main\SystemException
     */
    public static function validateValue(string $mixValue, \Local\Core\Inner\Robofeed\SchemeFields\ScalarField $obField, \Bitrix\Main\Result $obResult)
    {

        $obValidResult = new \Bitrix\Main\ORM\Data\AddResult();

        $obField->validateValue(
            $mixValue,
            null,
            [],
            $obValidResult
        );

        if( !$obValidResult->isSuccess() )
        {
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
     * @param \Local\Core\Inner\Robofeed\SchemeFields\ScalarField $obField      Объект скалярного поля
     * @param \Bitrix\Main\Result                                 $obResult     Объект результата ORM битрикса
     */
    private static function sendErrorByCodeToResult($strErrorCode, $obField, $obResult)
    {
        $strErrorText = '';

        switch( $strErrorCode )
        {
            case 'LOCAL_CORE_FIELD_IS_REQUIRED':
            case \Bitrix\Main\ORM\Fields\FieldError::EMPTY_REQUIRED:
                $strErrorText = 'Обязательное поле "'.$obField->getTitle().'" не заполнено.';
                break;

            case 'LOCAL_CORE_INVALID_VALUE':
            case \Bitrix\Main\ORM\Fields\FieldError::INVALID_VALUE:
                $strErrorText = 'Поле "'.$obField->getTitle().'" имеет недопустимое значение.';
                break;

            case 'LOCAL_CORE_INVALID_VALUE_FORMAT_DATETIME':
                $strErrorText = 'Поле "'.$obField->getTitle().'" имеет не правильный формат даты и времени.';
                break;

            case 'LOCAL_CORE_INVALID_VALUE_FORMAT_DATE':
                $strErrorText = 'Поле "'.$obField->getTitle().'" имеет не правильный формат даты.';
                break;

            case 'LOCAL_CORE_INVALID_VALUE_TABOO_ASCII_CHARS':
                $strErrorText = 'Поле "'.$obField->getTitle().'" не должно иметь непечатаемые символы с ASCII-кодами от 0 до 31 (за исключением символов с кодами 9, 10, 13)';
                break;

            case 'LOCAL_CORE_INVALID_VALUE_CANT_HTML':
                $strErrorText = 'Поле "'.$obField->getTitle().'" не должно иметь символов \', ", &, < и >. Замените их на &amp;apos; , &amp;quot; , &amp;amp; , &amp;lt; и &amp;gt; соответственно';
                break;

            case 'LOCAL_CORE_INVALID_VALUE_CDATA_LIMIT':
                $strErrorText = 'В поле "'.$obField->getTitle().'" из всех тегов разрешены только &lt;h3&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;p&gt;, &lt;br/&gt;';
                break;

            case 'LOCAL_CORE_INVALID_VALUE_REF_CLASS_NOT_EXIST':
                $strErrorText = 'Поле "'.$obField->getTitle().'" не может быть проверено, т.к. справочник не доступен. Пожалуйста, напишите на info@robofeed.ru что бы мы исправили ошибку, вероятно мы еще не курсе!';
                // TODO сделал записть в логер о критической ошибке
                break;

            case 'LOCAL_CORE_REF_INVALID_VALUE':
                $strErrorText = 'Поле "'.$obField->getTitle().'" должно содержать значение из справочника. Пожалуйста, изучите справочники https://robofeed.ru/development/references/';
                break;

            default:
                $strErrorText = 'Поле "'.$obField->getTitle().'" - произошла иная ошибка.';
                break;
        }

        if( !is_null($obField->getXmlPath()) )
        {
            $strErrorText .= '<br/>Путь: '.$obField->getXmlPath();
        }

        if( !is_null($obField->getXmlExpectedType()) )
        {
            $strErrorText .= '<br/>Ожидаемые данные: '.$obField->getXmlExpectedType();
        }

        $obResult->addError(new \Bitrix\Main\Error($strErrorText));
    }
}