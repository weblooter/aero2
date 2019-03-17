<?php

namespace Local\Core\Inner\AdminHelper\EditField;

/**
 * Базовый класс для полей страницы редактирования элементов
 *
 * Class Base
 * @package Local\Core\Inner\AdminHelper\EditField
 */
abstract class Base
{
    protected $elementData;
    protected $fields;
    private $required;
    protected $isEditable = true;

    /**
     * @param         $title  - Заголовок поля
     * @param         $code   - Код поля
     * @param null    $value  - Значение. По умолчанию данные берутся:
     *                        1. из _POST (основываясь на $code),
     *                        2. из данных элемента (основываясь на $code),
     *                        3. из getDefaultValue
     * @param boolean $isEdit - Поле для редактирования или просмотра
     *
     * @return $this
     */
    public function __construct($title, $code, $value = null)
    {
        $this->fields["TITLE"] = $title;
        $this->fields["CODE"] = $code;
        $this->fields["VALUE"] = $value;

        return $this;
    }

    public function setValue($val)
    {
        $this->fields["VALUE"] = $val;
        return $this;
    }

    /**
     * Возвращает html с полем для редактирования
     *
     * @return string
     */
    abstract public function getEditFieldHtml();

    /**
     * Возвращает html с полем для просмотра
     *
     * @return string
     */
    abstract public function getViewFieldHtml();

    /**
     * Значение по умолчанию
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return "";
    }

    /**
     * Возвращает полный html строки поля
     *
     * @return string
     */
    public function getRowHtml()
    {
        $title = $this->isRequired() == true ? "<b>{$this->getTitle()}</b>" : $this->getTitle();

        return "<tr>
                    <td width='30%'>
                        {$title}:
                        ".( $this->getNote() ? "<br/><small>{$this->getNote()}</small>" : "" )."
                    </td>
                    <td>".( ( $this->isEditable === true ) ? $this->getEditFieldHtml() : $this->getViewFieldHtml() )."</td>
                </tr>";
    }

    /**
     * Возвращает код поля
     *
     * @return string
     */
    public function getCode()
    {
        return $this->fields["CODE"] ?? "";
    }

    /**
     * Возвращает значение поля
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = $this->fields["VALUE"];
        if( $value === null )
        {

            $request = \Bitrix\Main\Context::getCurrent()
                ->getRequest();

            if( $request->isPost() )
            {
                $value = $request->get($this->getCode());
            }
            else
            {
                if( !empty($this->elementData) && is_array($this->elementData) )
                {
                    $value = $this->elementData[$this->getCode()];
                }
                else
                {
                    $value = $this->getDefaultValue();
                }
            }
        }

        return $value;
    }

    /**
     * Возвращает название поля
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->fields["TITLE"] ?? "";
    }

    /**
     * Устанавливает редактируемость поля
     *
     * @param boolean $value
     *
     * @return $this
     */
    public function setEditable($value)
    {
        $this->isEditable = $value === true;

        return $this;
    }

    /**
     * Устанавливает обязательность поле
     *
     * @param $value
     *
     * @return $this
     */
    public function setRequired($value)
    {
        $this->required = ( $value === true || $value === "Y" ) ? true : false;

        return $this;
    }

    /**
     * Возвращает обязательность поле
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required === true;
    }

    /**
     * Устанавливает данные элемента
     *
     * @return $this
     */
    public function setElementData($data)
    {
        $this->elementData = $data;

        return $this;
    }

    /**
     * Устанавливает доп описание
     *
     * @param $value
     *
     * @return $this
     */
    public function setNote($value)
    {
        $this->fields["NOTE"] = $value;

        return $this;
    }

    /**
     * Возвращает доп описание
     *
     * @return string
     */
    public function getNote()
    {
        return $this->fields["NOTE"] ?? "";
    }

}
