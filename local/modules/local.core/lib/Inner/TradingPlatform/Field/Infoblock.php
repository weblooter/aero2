<?php
namespace Local\Core\Inner\TradingPlatform\Field;

/**
 * Поле информационного блока. В основном используется в setEpilog()
 *
 * @package Local\Core\Inner\TradingPlatform\Field
 */
class Infoblock extends AbstractField
{
    const TYPE_SUCCESS = 'alert-success';
    const TYPE_ERROR = 'alert-danger';
    const TYPE_INFO = 'alert-warning';

    /** @inheritDoc */
    protected function execute()
    {
        $this->addToRender('<div class="alert '.$this->getType().' mt-3">'.$this->getValue().'</div>');
    }

    /** @inheritDoc */
    protected function getRow($htmlInputRender)
    {
        return '<div class="row"><div class="col-12">'.$htmlInputRender.'</div></div>';
    }

    protected $_fieldType = self::TYPE_INFO;

    /**
     * Задает тип информационного блока.<br/>
     * В качестве значения необходимо передать одну из констант, объявленную в данном классе.<br/>
     * <ul>
     * <li>TYPE_SUCCESS</li>
     * <li>TYPE_WARNING</li>
     * <li>TYPE_DANGER</li>
     * <li>TYPE_INFO</li>
     * </ul>
     *
     * @param $constType
     *
     * @return $this
     */
    public function setType($constType)
    {
        $this->_fieldType = $constType;
        return $this;
    }

    protected function getType()
    {
        return $this->_fieldType;
    }
}