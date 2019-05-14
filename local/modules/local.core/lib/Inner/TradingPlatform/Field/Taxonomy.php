<?php

namespace Local\Core\Inner\TradingPlatform\Field;

/**
 * Таксонометрическое поле
 *
 * @package Local\Core\Inner\TradingPlatform\Field
 */
class Taxonomy extends AbstractField
{

    /** @inheritDoc */
    protected function execute()
    {
        if (empty($this->getLeftColumn())) {
            throw new \Exception('Необходимо задать левую колонку');
        }

        if (empty($this->getRightColumn())) {
            throw new \Exception('Необходимо задать правую колонку');
        }
        if (empty($this->getAction())) {
            throw new \Exception('Необходимо задать экшен таксономии');
        }

        $arConvertedRightColumn = \Local\Core\Inner\TaxonomyData\Base::convertData($this->getRightColumn());

        // INFO Тут были заметки для автокомплита но хуй там
        /*
        \Bitrix\Main\Page\Asset::getInstance()->addString('<script type="text/javascript">
var LocalCoreTaxonomy'.$this->getRowHash().' = {
    "leftColumn": JSON.parse(\''.addcslashes(json_encode($this->getLeftColumn(), JSON_UNESCAPED_UNICODE), '\'').'\'),
    "rightColumn": JSON.parse(\''.addcslashes(json_encode($arConvertedRightColumn, JSON_UNESCAPED_UNICODE), '\'').'\'),
};
</script>');
        */

        foreach ($this->getLeftColumn() as $valueLeftColumn => $text) {

            $boolHasValue = false;
            $strOption = '';
            if (!empty($this->getValue()[$valueLeftColumn])) {

                if( $this->getIsMultiple() )
                {
                    foreach ($this->getValue()[$valueLeftColumn] as $valueRightColumn)
                    {
                        if( !empty( $arConvertedRightColumn[$valueRightColumn] ) )
                        {
                            $boolHasValue = true;
                            $strOption .= '<option value="'.htmlspecialchars($valueRightColumn).'" selected>'.htmlspecialchars($arConvertedRightColumn[$valueRightColumn]).'</option>';
                        }
                    }
                }
                elseif( !empty( $arConvertedRightColumn[$this->getValue()[$valueLeftColumn]] ) )
                {
                    $boolHasValue = true;
                    $strOption .= '<option value="'.htmlspecialchars($this->getValue()[$valueLeftColumn]).'" selected>'.htmlspecialchars($arConvertedRightColumn[$this->getValue()[$valueLeftColumn]]).'</option>';
                }
            }

            $this->addToRender('<div class="row mb-4 '.($boolHasValue ? ' d-none' : '').'" data-taxonomyRowWrapper><div class="col-md-4"><label>'.$text.'</label></div>');
            $this->addToRender('<div class="col-md-8"><select ');
            $this->addToRender(' name="'.$this->getName().'['.$valueLeftColumn.']'.( $this->getIsMultiple() ? '[]" multiple data-close-On-Select="false"' : '"' ));
            $this->addToRender(' class="taxonomy-field-select" data-action="'.$this->getAction().'" data-placeholder="'.htmlspecialchars($this->getDefaultOption()).'">'.$strOption.'</select></div></div>');
        }

    }

    /** @inheritDoc */
    public function getRow($htmlInputRender)
    {
        $strTitle = '<label class="'.($this->getIsRequired() ? 'font-weight-bold' : '').'">'.$this->getTitle().($this->getIsRequired() ? ' * ' : '').':</label>';
        $strRowHash = $this->getRowHash();

        $strAdminPath = '';
        if ($GLOBALS['USER']->IsAdmin() && !empty($this->getName())) {
            $strAdminPath = '<br/><small><mark>'.$this->getName().'</mark></small>';
        }

        return <<<DOCHERE
<div class="form-group" id="$strRowHash">
    <div class="row">
        <div class="col-md-4">
            $strTitle
            $strAdminPath
        </div>
        <div class="col-md-8">
        
            <label class="custom-control custom-checkbox mb-2">
                <input type="checkbox" class="custom-control-input" checked onchange="PersonalTradingplatformFormComponent.toggleDisplayBlockTaxonomy('$strRowHash')" data-taxonomy-hide />
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Скрыть проставленные соответствия</span>
            </label>
            <br/>

            <a href="javascript:void(0)" class="btn btn-secondary taxonomy-collapse-btn" data-toggle="collapse" data-target="#collapseTaxonomy$strRowHash" aria-expanded="false"></a>
        </div>
    </div>
    <div class="card mt-4">
        <div class="collapse card-body p-3" id="collapseTaxonomy$strRowHash">
            $htmlInputRender
            <div class="clearfix"></div>
        </div>
    </div>
</div>
DOCHERE;
    }

    protected $arLeftColumn = [];

    /**
     * Задает левую колонку, к которой будут выбираться соответствия из правой колонки
     *
     * @param $ar
     *
     * @return $this
     */
    public function setLeftColumn(array $ar)
    {
        $this->arLeftColumn = $ar;
        return $this;
    }

    public function getLeftColumn()
    {
        return $this->arLeftColumn;
    }

    protected $arRightColumn = [];

    /**
     * Задает правую колонку.<br/>
     * В значений выстуает массив структуры таксономии.
     *
     * @param array $ar Массив вариантов
     *
     * @return $this
     */
    public function setRightColumn(array $ar)
    {
        $this->arRightColumn = $ar;
        return $this;
    }

    public function getRightColumn()
    {
        return $this->arRightColumn;
    }

    protected $_fieldAction = null;

    /**
     * Задает экшен для аякса таксономии
     *
     * @param $str
     *
     * @return $this
     */
    public function setAction($str)
    {
        $this->_fieldAction = $str;
        return $this;
    }

    public function getAction()
    {
        return $this->_fieldAction;
    }

    /** @inheritDoc */
    public function isValueFilled($mixData)
    {
        $boolRes = false;
        if (is_array($mixData)) {
            $mixData = array_diff($mixData, ['']);
            if (!empty($mixData) && sizeof($mixData) > 0) {
                $boolRes = true;
            }
        }

        return $boolRes;
    }

    /** @inheritDoc */
    public function extractValue($mixData, $mixAdditionalData = null)
    {
        $mixExtract = null;
        if( !empty( $mixData[ $mixAdditionalData ] ) )
        {
            $mixExtract = $mixData[ $mixAdditionalData ];
        }

        return $mixExtract;
    }


    /** @var string $_fieldDefaultOption Текст, выводимый в значении по умолчанию */
    protected $_fieldDefaultOption = '-- Выберите соответствие --';

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


    // INFO Тут были заметки для автокомплита но хуй там
    /*
    protected $_fieldIsAutocomplete = false;

    public function setIsAutocomplete($bool = true)
    {
        $this->_fieldIsAutocomplete = $bool;
        return $this;
    }

    public function getIsAutocomplete()
    {
        return $this->_fieldIsAutocomplete;
    }
    */
}