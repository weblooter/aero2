<?php

namespace Local\Core\Inner\AdminHelper\EditField;


class Condition extends Base
{
    private $fieldName = null;
    private $formName = null;

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return "";
    }

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        \Bitrix\Main\Loader::includeModule('catalog');

        global $APPLICATION;

        $result = "";

        if (!is_null($this->fieldName) && !is_null($this->formName)) {
            $obCond = new \CCatalogCondTree();
            $boolCond = $obCond->Init(BT_COND_MODE_DEFAULT, BT_COND_BUILD_CATALOG, [
                    "FORM_NAME" => $this->formName, // ID формы в которую будет выводится
                    "CONT_ID" => $this->fieldName,
                    "JS_NAME" => "JSCatCond",
                    "PREFIX" => $this->fieldName
                ]);
            if (!$boolCond) {
                if ($ex = $APPLICATION->GetException()) {
                    $result .= $ex->GetString()."<br>";
                }
            } else {
                $result .= $obCond->Show($this->getValue());
            }

            # Блок с правилами
            $result .= "<div id='".$this->fieldName."' style='position: relative; z-index: 1;'></div>";
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return $this->getValue();
    }

    public function setFieldName($name)
    {
        $this->fieldName = $name;
        return $this;
    }

    public function setFormName($name)
    {
        $this->formName = $name;
        return $this;
    }
}