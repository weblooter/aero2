<?

namespace Local\Core\Inner\TradingPlatform\Field;

/**
 * Заголовок
 *
 * @package Local\Core\Inner\TradingPlatform\Field
 */
class Header extends AbstractField
{
    /** @inheritDoc */
    protected function execute()
    {
    }

    /** @inheritDoc */
    public function getRow($htmlInputRender)
    {
        return '<div class="form-group">
    <h3 class="bold"><span>'.$this->getValue().'</span></h3>
</div>';
    }

    /** @inheritDoc */
    public function isValueFilled($mixData){
        return true;
    }
}