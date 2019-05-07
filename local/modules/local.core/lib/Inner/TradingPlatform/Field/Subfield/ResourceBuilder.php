<?php

namespace Local\Core\Inner\TradingPlatform\Field\Subfield;


class ResourceBuilder extends \Local\Core\Inner\TradingPlatform\Field\AbstractField
{
    use \Local\Core\Inner\TradingPlatform\Field\Traits\Options;

    /** @inheritDoc */
    protected function execute()
    {

        $smallTextHash = sha1($this->getName().time());

        $this->addToRender('
<div class="input-group mb-2">
    <textarea class="form-control textarea-autosize" name="'.$this->getName().'" onkeyup="PersonalTradingplatformFormComponent.replaceBuilderSmallString(this)" data-small-text-hash="'.$smallTextHash.'">'.$this->getValue().'</textarea>
    <div class="input-group-append">
        <button class="btn btn-secondary dropdown-toggle h-100" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Добавить</button>
        <div class="dropdown-menu dropdown-menu-right" style="max-height: 300px; overflow-y: scroll;">');
        foreach ($this->getOptions() as $h => $a) {
            $this->addToRender('<div class="font-weight-bold p-3 pt-2 pt-2 border border-secondary border-left-0 border-right-0 border-top-0 lead">'.$h.'</div>');

            foreach ($a as $k => $v) {
                $this->addToRender('<a class="dropdown-item" href="javascript:void(0)" onclick="PersonalTradingplatformFormComponent.addBuilderValueToInput(\'{{'.$k.'}}\', \''.$this->getName().'\');">'.$v.'</a>');
            }
        }


        $this->addToRender('</div>
    </div>
</div>
<small class="form-text text-muted mb-4" data-small-text-id="'.$smallTextHash.'">'.$this->getSmallString().'</small>');
    }

    private function getSmallString()
    {
        $strSmallTextValue = $this->getValue();
        preg_match_all('/{{([\#\-\_\|A-Za-z0-9]+)}}/', str_replace(["\r\n", "\n"], '', $strSmallTextValue), $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as &$v) {
                $nv = '';
                foreach ($this->getOptions() as $gn) {
                    if (!empty($gn[$v])) {
                        $nv = $gn[$v];
                    }
                }
                $v = '{{'.$nv.'}}';
            }
            unset($v);

            $strSmallTextValue = str_replace($matches[0], $matches[1], str_replace(["\r\n", "\n"], '', $strSmallTextValue));
        }

        return $strSmallTextValue;
    }

    /** @inheritDoc */
    public function printRender()
    {
        throw new \Exception('Поле является вспомогательным для Resource. Использовать отдельно не возможно.');
    }

    /** @inheritDoc */
    public function printRow()
    {
        throw new \Exception('Поле является вспомогательным для Resource. Использовать отдельно не возможно.');
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
        return null;
    }
}