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
        return '<div class="row">
    <div class="col-12">
        <table class="table table-dark">
            <thead>
                <tr>
                    <th class="text-center">'.$this->getValue().'</th>
                </tr>
            </thead>
        </table>
    </div>
</div>';
    }
}