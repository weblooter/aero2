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
        if( empty( $this->getLeftColumn() ) )
        {
            throw new \Exception('Необходимо задать левую колонку');
        }

        if( empty( $this->getRightColumn() ) )
        {
            throw new \Exception('Необходимо задать правую колонку');
        }
        if( empty( $this->getAction() ) )
        {
            throw new \Exception('Необходимо задать экшен таксономии');
        }

        $arConvertedRightColumn = \Local\Core\Inner\TaxonomyData\Base::convertData($this->getRightColumn());

        foreach ($this->getLeftColumn() as $value => $text)
        {
            $this->addToRender('<div class="row mb-4"><div class="col-md-4"><label>'.$text.' [ID '.$value.']</label></div>');
            $this->addToRender('<div class="col-md-8"><select name="'.$this->getName().'['.$value.']" class="taxonomy-field-select" data-action="'.$this->getAction().'" data-placeholder="'.$this->getDefaultOption().'">');

            if( !empty( $this->getValue()[ $value ] ) && !empty( $arConvertedRightColumn[ $this->getValue()[ $value ] ] ) )
            {
                $this->addToRender('<option value="'.htmlspecialchars( $this->getValue()[ $value ] ).'">'.htmlspecialchars($arConvertedRightColumn[ $this->getValue()[ $value ] ]).'</option>');
            }

            $this->addToRender('</select></div></div>');
        }

    }

    /** @inheritDoc */
    public function getRow($htmlInputRender)
    {
        $strTitle = $this->getTitle().( $this->getIsRequired() ? ' * ' : '' );
        $strRowHash = $this->getRowHash();

        $strAdminPath = '';
        if ($GLOBALS['USER']->IsAdmin() && !empty($this->getName())) {
            $strAdminPath = '<small class="pl-4"><mark>'.$this->getName().'</mark></small>';
        }

        return <<<DOCHERE
<div class="form-group" id="$strRowHash">
    <div class="card"><h5 class="card-header text-center">$strTitle <a href="javascript:void(0)" class="btn btn-secondary btn-sm taxonomy-collapse-btn" data-toggle="collapse" data-target="#collapseTaxonomy$strRowHash" aria-expanded="false"></a>$strAdminPath</h5></div>
    <div class="collapse border border-secondary p-3" id="collapseTaxonomy$strRowHash">
        $htmlInputRender
        <div class="clearfix"></div>
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
        if( is_array($mixData) )
        {
            $mixData = array_diff($mixData, ['']);
            if( !empty( $mixData ) && sizeof($mixData) > 0 )
            {
                $boolRes = true;
                foreach ($mixData as $strVal)
                {
                    if( !(bool)strlen( trim( $strVal ) ) ){
                        $boolRes = false;
                        break;
                    }
                }
            }
        }
        else
        {
            $boolRes = (bool)strlen( trim( $mixData ) );
        }

        return $boolRes;
    }

    /** @inheritDoc */
    public function extractValue($mixData, $mixAdditionalData = null)
    {
        $mixExtract = null;
        if( is_array($mixData) )
        {
            foreach ($mixData as &$str)
            {
                $str = (string)(trim($str));
            }
            unset($str);
            $mixExtract = array_diff($mixData, ['']);
        }
        elseif( is_scalar($mixData) )
        {
            $mixExtract = (string)(trim($mixData));
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
}