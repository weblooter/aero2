<?php

namespace Local\Core\Inner\TradingPlatform\Field;

/**
 * Абстрактное поле
 *
 * @package Local\Core\Inner\TradingPlatform\Field
 */
abstract class AbstractField
{
    /* ************* */
    /* FIELD METHODS */
    /* ************* */

    /** @var string $_fieldTitle Заголовок поля */
    protected $_fieldTitle;

    /**
     * Заголовок поля
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->_fieldTitle = $title;
        return $this;
    }

    /**
     * Получить заголовк поля
     *
     * @return string
     */
    protected function getTitle()
    {
        return $this->_fieldTitle;
    }


    /** @var string $_filedDescription Описание поля */
    protected $_filedDescription;

    /**
     * Описание поля
     *
     * @param string $desc
     *
     * @return $this
     */
    public function setDescription($desc)
    {
        $this->_filedDescription = $desc;
        return $this;
    }

    /**
     * Получить описание поля
     *
     * @return string
     */
    protected function getDescription()
    {
        return $this->_filedDescription;
    }


    /** @var string $_fieldName Аттрибут NAME поля */
    protected $_fieldName;

    /**
     * Задать название
     *
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->_fieldName = $name;
        return $this;
    }

    /**
     * Получить название
     *
     * @return string
     */
    protected function getName()
    {
        return $this->_fieldName;
    }


    /** @var string $_fieldValue Значение поля */
    protected $_fieldValue;

    /**
     * Задать значение.<br/>
     * Все зависит от поля, но в большестве случает если поле не multiple, то значение строка, в противном случае это array.
     *
     * @param string|array $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->_fieldValue = $value;
        return $this;
    }

    /**
     * Получить значение
     *
     * @return string|array|null
     */
    protected function getValue()
    {
        return $this->_fieldValue;
    }


    /** @var bool $_fieldIsRequired Признак обязательности поля */
    protected $_fieldIsRequired = false;

    /**
     * Задать обязательность
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setIsRequired(bool $bool)
    {
        $this->_fieldIsRequired = $bool;
        return $this;
    }

    /**
     * Получить обязательность поля
     *
     * @return bool
     */
    protected function getIsRequired()
    {
        return $this->_fieldIsRequired;
    }


    /** @var bool $_fieldIsReadOnly Признак доступа только на чтение поля */
    protected $_fieldIsReadOnly = false;

    /**
     * Задать признак "только для чтения"
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setIsReadOnly(bool $bool)
    {
        $this->_fieldIsReadOnly = $bool;
        return $this;
    }

    /**
     * Получить признак "только для чтения"
     *
     * @return bool
     */
    protected function getIsReadOnly()
    {
        return $this->_fieldIsReadOnly;
    }


    /** @var bool $_fieldIsMultiple Признак множественности поля */
    protected $_fieldIsMultiple = false;

    /**
     * Задать признак множественности поля
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setIsMultiple(bool $bool)
    {
        $this->_fieldIsMultiple = $bool;
        return $this;
    }

    /**
     * Получить признак множественности поля
     *
     * @return bool
     */
    protected function getIsMultiple()
    {
        return $this->_fieldIsMultiple;
    }


    /** @var AbstractField $_fieldEpilog Объект описанного поля для эпилога */
    protected $_fieldEpilog;

    /**
     * Задать объект поля для эпилога
     *
     * @param AbstractField $obField
     *
     * @return $this
     */
    public function setEpilog(AbstractField $obField)
    {
        $this->_fieldEpilog = $obField;
        return $this;
    }

    /**
     * Получить объект поля эпилога
     *
     * @return AbstractField
     */
    protected function getEpilog()
    {
        return $this->_fieldEpilog;
    }


    /** @var array $_fieldEvent Хранилище событий поля */
    protected $_fieldEvent = [];

    /**
     * Задать аттрибуты событий для поля.<br/>
     * <b>Экранировать " ' \ не нужно!</b> Скрипт далее это сдетает сам.<br/>
     * Если в событии задан массив - он склеится знаком ;<br/>
     * Пример заполнения:<br/>
     * <pre>
     * [
     *   'onclick' => [
     *       'console.log(this.value)',
     *       'this.form.submit()'
     *     ],
     *   'onkeyup' => 'console.log(this.value)'
     * ]
     * </pre>
     *
     * @param array $ar
     *
     * @return $this
     */
    public function setEvent(array $ar)
    {
        $this->_fieldEvent = $ar;
        return $this;
    }

    /**
     * Получить аттрибуты событий поля.
     *
     * @return array
     */
    protected function getEvent()
    {
        return $this->_fieldEvent;
    }

    /**
     * Получить собранную строку эвентов поля с экранированными значениями.
     *
     * @return string
     */
    protected function getEventCollected()
    {
        $ar = $this->getEvent();
        foreach ($ar as $k => &$v) {
            if (is_array($v)) {
                $v = implode('; ', $v);
            }

            $v = $k.'="'.htmlspecialchars($v).'"';
        }
        unset($v);
        return implode(' ', $ar);
    }


    /** @var string $_fieldPlaceholder Placeholder поля, если таковой есть у поля */
    protected $_fieldPlaceholder;

    /**
     * Задать плейсхолдер поля
     *
     * @param string $str
     *
     * @return $this
     */
    public function setPlaceholder($str)
    {
        $this->_fieldPlaceholder = $str;
        return $this;
    }

    /**
     * Получить плейсхолдер поля
     *
     * @return string
     */
    protected function getPlaceholder()
    {
        return $this->_fieldPlaceholder;
    }


    /** @var bool $_fieldIsCanAddNewInput Признак возможности добавить еще одно поле */
    protected $_fieldIsCanAddNewInput = false;

    /**
     * Задает признак возможности добавить еще одно поле.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setIsCanAddNewInput(bool $bool)
    {
        $this->_fieldIsCanAddNewInput = $bool;
        return $this;
    }

    /**
     * Получить признак возможности добавить еще одно поле.
     *
     * @return bool
     */
    protected function getIsCanAddNewInput()
    {
        return $this->_fieldIsCanAddNewInput;
    }

    /** @var int $_intAdditionalInputsCount Кол-во дополнительных полей, если поле помечено как множественное */
    protected $_intAdditionalInputsCount = 2;

    /**
     * Задать кол-во дополнительных полей. Применяется если поле множественное
     *
     * @param $int
     *
     * @return $this
     */
    public function setAdditionalInputsCount($int)
    {
        $this->_intAdditionalInputsCount = $int;
        return $this;
    }

    /**
     * Получить кол-во дополнительных полей
     *
     * @return int
     */
    protected function getAdditionalInputsCount()
    {
        return $this->_intAdditionalInputsCount;
    }

    /* ****** */
    /* RENDER */
    /* ****** */

    /** @var string $_renderHtml Хранилище для рендеринга */
    protected $_renderHtml = '';

    /**
     * Добавляет html для дальнейшего рендеринга
     *
     * @param string $str
     */
    protected function addToRender($str)
    {
        $this->_renderHtml .= $str;
    }

    /**
     * Сбросить хранилище рендера
     */
    protected function resetRender()
    {
        $this->_renderHtml = '';
    }

    /**
     * Получить html рендеринга поля
     *
     * @return string
     */
    public function getRender()
    {
        $this->execute();
        return $this->_renderHtml;
    }

    /**
     * Вывод только рендеринга поля
     */
    public function printRender()
    {
        echo $this->getRender();
    }

    /**
     * Получить html рендера вместе с название и описанием
     *
     * @param $htmlInputRender
     *
     * @return string
     */
    protected function getRow($htmlInputRender)
    {
        $strTitle = (($this->getIsRequired()) ? '<b>'.$this->getTitle().'</b>' : $this->getTitle()).':';
        $strDesc = (!is_null($this->getDescription())) ? '<br/><small>'.$this->getDescription().'</small>' : null;
        return <<<DOCHERE
<div class="form-group">
    <div class="row">
        <div class="col-4 text-right">
            <label>$strTitle</label>
            $strDesc
        </div>
        <div class="col-8 text-left">
            $htmlInputRender
        </div>
    </div>
</div>
DOCHERE;

    }

    /**
     * Выводит рендер вместе с название и описанием
     */
    public function printRow()
    {
        echo $this->getRow($this->getRender());
    }

    /* ******** */
    /* ABSTRACT */
    /* ******** */

    /**
     * Собирает рендер по заявленным данным
     */
    abstract protected function execute();
}