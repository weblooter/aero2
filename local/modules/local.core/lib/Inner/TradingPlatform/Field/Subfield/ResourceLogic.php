<?php

namespace Local\Core\Inner\TradingPlatform\Field\Subfield;


class ResourceLogic extends \Local\Core\Inner\TradingPlatform\Field\AbstractField
{
    use \Local\Core\Inner\TradingPlatform\Field\Traits\Options;

    /** @inheritDoc */
    protected function execute()
    {
        $currentText = $this->getDefaultOption();
        foreach ($this->getOptions() as $h => $a)
        {
            if( is_array($a) )
            {
                if( !is_null( $a[ $this->getValue() ] ) )
                {
                    $currentText = $a[ $this->getValue() ];
                }
            }
            else
            {
                if( $this->getValue() == $h )
                {
                    $currentText = $a;
                }
            }
        }

        $strDropdownHash = sha1($this->getName());

        $this->addToRender('<div class="dropdown local-core-dropdown-line" data-dropdown-hash-id="'.$strDropdownHash.'">
    <a class="local-core-dropdown-title" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$currentText.'</a>
    <div class="dropdown-menu" style="max-height: 300px; overflow-y: scroll;">');

        $funMakeOption = function ($k, $v) use ($strDropdownHash)
            {
                return '<a class="dropdown-item" href="javascript:void(0)" onclick="PersonalTradingplatformFormComponent.changeLogicFieldValue(\''.$k.'\', \''.htmlspecialchars($v).'\', \''.$strDropdownHash.'\');">'.$v.'</a>';
            };

        foreach ($this->getOptions() as $h => $a) {
            if( is_array($a) )
            {
                $this->addToRender('<h4 class="dropdown-header">'.$h.'</h4>');

                foreach ($a as $k => $v) {
                    $this->addToRender($funMakeOption($k, $v));
                }
            }
            else
            {
                $this->addToRender($funMakeOption($h, $a));
            }
        }


        $this->addToRender('</div>'.( (new \Local\Core\Inner\TradingPlatform\Field\InputHidden())->setValue($this->getValue())
                ->setName($this->getName())
                ->getRender() ).'</div>');
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