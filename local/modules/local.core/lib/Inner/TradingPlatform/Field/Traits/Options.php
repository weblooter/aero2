<?php
namespace Local\Core\Inner\TradingPlatform\Field\Traits;


trait Options
{

    /** @var array $_fieldOptions Варианты значений */
    protected $_fieldOptions = [];

    /**
     * Задать варианты значений.<br/>
     * Пример заполнения:<br/>
     * <pre>
     * [
     *  'value1' => 'text value 1',
     *  'group_name_1' => [
     *     'value in group 1' => 'text value',
     *     'value in group 2' => 'text value',
     *   ]
     * ]
     * </pre>
     *
     * @param array $ar
     *
     * @return $this
     */
    public function setOptions(array $ar)
    {
        $this->_fieldOptions = $ar;
        return $this;
    }

    /**
     * Получить варианты значений
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_fieldOptions;
    }

    /** @var string $_fieldDefaultOption Текст, выводимый в значении по умолчанию */
    protected $_fieldDefaultOption;

    /**
     * Задать текст значения по умолчанию
     *
     * @param $str
     *
     * @return $this
     */
    public function setDefaultOption($str)
    {
        $this->_fieldDefaultOption = $str;
        return $this;
    }

    /**
     * Получить текст значения по умолчанию
     *
     * @return string
     */
    public function getDefaultOption()
    {
        return $this->_fieldDefaultOption;
    }
}