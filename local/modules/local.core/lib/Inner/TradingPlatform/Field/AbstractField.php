<?php

namespace Local\Core\Inner\TradingPlatform\Field;

/**
 * Абстрактное поле
 *
 * @package Local\Core\Inner\TradingPlatform\Field
 */
abstract class AbstractField
{
    use Traits\IsReadOnly;

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
    public function getTitle()
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
    public function getDescription()
    {
        return $this->_filedDescription;
    }


    /** @var string $_fieldName Аттрибут NAME поля */
    protected $_fieldName;

    /** @var static $_fieldRowHash Хэш блока. Служит для аяксового обновления блока */
    protected $_fieldRowHash;

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
        $this->_fieldRowHash = sha1($name);
        return $this;
    }

    /**
     * Получить название
     *
     * @return string
     */
    public function getName()
    {
        return $this->_fieldName;
    }

    /**
     * Задает хэш строки (ID) для обновления.<br/>
     * Генерируется автоматически после "setName()", поэтому задавать не нужно.<br/>
     * Но если есть необходимость, то делать только после вызова "setName()".
     *
     * @param $str
     *
     * @return $this
     */
    public function setRowHash($str)
    {
        $this->_fieldRowHash = $str;
        return $this;
    }

    /**
     * Возвращает хэш блока
     *
     * @return mixed
     */
    public function getRowHash()
    {
        return $this->_fieldRowHash;
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
    public function getValue()
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
    public function setIsRequired(bool $bool = true)
    {
        $this->_fieldIsRequired = $bool;
        return $this;
    }

    /**
     * Получить обязательность поля
     *
     * @return bool
     */
    public function getIsRequired()
    {
        return $this->_fieldIsRequired;
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
    public function setIsMultiple(bool $bool = true)
    {
        $this->_fieldIsMultiple = $bool;
        return $this;
    }

    /**
     * Получить признак множественности поля
     *
     * @return bool
     */
    public function getIsMultiple()
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
    public function getEpilog()
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
    public function getEvent()
    {
        return $this->_fieldEvent;
    }

    /**
     * Получить собранную строку эвентов поля с экранированными значениями.
     *
     * @return string
     */
    public function getEventCollected()
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
        if (!is_null($this->getEpilog()) && $this->getEpilog() instanceof AbstractField) {
            $this->addToRender($this->getEpilog()
                ->getRender());
        }
        $this->makeReadOnlyValue();
        return $this->_renderHtml;
    }

    /**
     * Дополняет рендер значениями, если поле помечено как readonly и значения не пустые.<br/>
     * Вызывается в \Local\Core\Inner\TradingPlatform\Field\AbstractField::getRender();<br/>
     * Может быть переиницилизаровано в случае необходимости.
     *
     * @see \Local\Core\Inner\TradingPlatform\Field\AbstractField::getRender();
     */
    protected function makeReadOnlyValue()
    {
        if (
            $this->getIsReadOnly()
            && (!is_null($this->getValue()) && !empty($this->getValue()))
            && !(static::class instanceof InputHidden)
        ) {
            $this->addToRender((new InputHidden())->setValue($this->getValue())
                ->setName($this->getName())
                ->setIsMultiple($this->getIsMultiple())
                ->getRender());
        }
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
    public function getRow($htmlInputRender)
    {
        $strInfotext = '<label class="'.( $this->getIsRequired() ? 'required' : '' ).'">'.$this->getTitle().':';
        $strInfotext .= (!is_null($this->getDescription())) ? '<button class="icon-info robotip__starter" type="button"><div class="robotip__content">'.$this->getDescription().'</div></button>' : '';
        $strInfotext .= '</label>';

        if ($GLOBALS['USER']->IsAdmin() && !empty($this->getName())) {
            $strInfotext .= '<br/><small><mark>'.$this->getName().'</mark></small>';
        }
        $strRowHash = $this->getRowHash();

        return <<<DOCHERE
<div class="form-group" id="$strRowHash">
    <div class="row">
        <div class="col-xs-4">
            $strInfotext
        </div>
        <div class="col-xs-8">
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

    /**
     * Проверяет заполненость поля.<br/>
     * Используется в хендлерах для проверки заполнености значения у обязательных полей.<br/>
     * Суть проверки сводится к тому, пустое ли значение или нет, если список - есть ли оно среди вариантов,
     * если ресурс - все ли логические цепочки имею значение на выходе.<br/>
     * Вызывается уже у заполненого данными поля.<br/>
     * true - если поле заполнено/логическая цепочка заполнена/значение заполнено и оно есть в вариантах.<br/>
     * false - для всего остального и ошибок.<br/>
     * <b>Метод не предназначен для извлечения конечного значения, а лишь для проверки - есть ли что извлекать!</b>
     *
     * @return bool
     */
    abstract public function isValueFilled($mixData);
}