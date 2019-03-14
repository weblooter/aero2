<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class SelectPlusTexts extends Select
{
    protected $variants = [];
    private $inputs = [];
    private $inputSeparator = 'x';
    private $valueModificator = null;

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        $variants = [
            "reference"    => array_values($this->variants),
            "reference_id" => array_keys($this->variants),
        ];

        $value = $this->getValue();

        if( !is_array($value) )
        {
            if( $this->valueModificator )
            {
                $value = call_user_func(
                    $this->valueModificator,
                    $value
                );
            }
        }

        $return = [];
        $return[] = SelectBoxFromArray(
            $this->getCode().'[]',
            $variants,
            $value[0],
            "Не выбрано",
            ""
        );
        $arInputs = [];
        foreach( $this->inputs as $inputKey => $arInput )
        {
            $i = ['<input'];

            if( !$arInput['type'] )
            {
                $arInput['type'] = 'text';
            }

            if( !$arInput['name'] )
            {
                $arInput['name'] = $this->getCode().'[]';
            }

            if( $value[$inputKey + 1] )
            {
                $arInput['value'] = $value[$inputKey + 1];
            }

            foreach( $arInput as $k => $v )
            {
                $i[] = "$k=\"$v\"";
            }

            $i[] = '/>';
            $arInputs[] = join(
                ' ',
                $i
            );

        }

        $return[] = join(
            $this->inputSeparator,
            $arInputs
        );

        return join(
            '&nbsp;',
            $return
        );
    }

    /**
     * {@inheritdoc}
     */

    // todo
    public function getViewFieldHtml()
    {
        $result = "";

        if( !empty($this->getValue()) )
        {

            if( isset($this->variants[$this->getValue()]) )
            {

                $result = "{$this->variants[$this->getValue()]}[{$this->getValue()}]";
            }

        }

        return $result;
    }

    /**
     * Добавляет текстовое поле ввода
     *
     * @param array $input - массив атрибутов вида ['type' => 'text', 'required' => 'true']
     *
     * @return $this
     */


    public function addTextField(array $input = [])
    {
        $this->inputs[] = $input;

        return $this;
    }

    /**
     * Устанавливает визуальный разделитель текстовых полей
     *
     * @param string $inputSeparator
     *
     * @return $this
     */
    public function setTextFieldSeparator(string $inputSeparator)
    {
        $this->inputSeparator = $inputSeparator;
        return $this;
    }


    /**
     * Устанавливает обработчик для разделения $this->value на части
     *
     * @param string $valueModificator
     *
     * @return $this
     */
    public function setValueModificator(string $valueModificator)
    {
        $this->valueModificator = $valueModificator;
        return $this;
    }

}
