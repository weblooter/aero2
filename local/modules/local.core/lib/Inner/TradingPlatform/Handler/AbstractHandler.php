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
        foreach (static::getFields() as $obField) {
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
    protected static function getFields()
    {
        return array_merge(static::getGeneralFields(), static::getHandlerFields());
    }

    /**
     * Получить общие поля
     *
     * @return array
     */
    protected static function getGeneralFields()
    {
        return [
            'general_siteLink' => (new Field\InputText())->setTitle('Ссылка на сайт')
                ->setIsRequired(true)
                ->setName('FIELD[GENERAL][SITE_LINK]')
                ->setIsMultiple(true)
                ->setIsCanAddNewInput(true)
                ->setAdditionalInputsCount(0)
                ->setValue(['red', 'blue'])
                ->setEvent([
                    'onkeyup' => ['console.log(this.value)', 'alert("hi")']
                ])
        ];
    }

    /**
     * Получить поля конкретного обработчика
     *
     * @return array
     */
    abstract protected static function getHandlerFields();
}