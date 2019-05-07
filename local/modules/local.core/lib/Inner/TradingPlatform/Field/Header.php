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
        return '<div class="form-group"><div class="card"><h5 class="card-header text-center">'.$this->getValue().'</h5></div></div>';
    }

    /** @inheritDoc */
    public function isValueFilled($mixData){
        return true;
    }

    /** @inheritDoc */
    public function extractValue($mixData, $mixAdditionalData = null)
    {
        return null;
    }
}