<?php

namespace Local\Core\Inner\TradingPlatform\Field\Subfield;


class ResourceBuilder extends \Local\Core\Inner\TradingPlatform\Field\AbstractField
{
    use \Local\Core\Inner\TradingPlatform\Field\Traits\Options;

    /** @inheritDoc */
    protected function execute()
    {

        $smallTextHash = sha1($this->getName().time());

        $this->addToRender('<div class="input-group">
    <textarea class="form-control" name="'.$this->getName().'" onkeyup="PersonalTradingplatformFormComponent.replaceBuilderSmallString(this)" data-small-text-hash="'.$smallTextHash.'">'.$this->getValue().'</textarea>
    <div class="input-group-append">
        <button class="btn btn-warning dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Добавить</button>
        <div class="dropdown-menu" style="max-height: 300px; overflow-y: scroll;">');
        foreach ($this->getOptions() as $h => $a) {
            $this->addToRender('<h4 class="dropdown-header">'.$h.'</h4>');

            foreach ($a as $k => $v) {
                $this->addToRender('<a class="dropdown-item" href="javascript:void(0)" onclick="PersonalTradingplatformFormComponent.addBuilderValueToInput(\'{{'.$k.'}}\', \''.$this->getName().'\');">'.$v
                                   .'</a>');
            }
        }


        $this->addToRender('</div>
    </div>
</div>
<small class="form-text text-muted" data-small-text-id="'.$smallTextHash.'">'.$this->getSmallString().'</small>');
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
}