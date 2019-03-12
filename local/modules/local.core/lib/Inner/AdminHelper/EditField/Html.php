<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class Html extends Base
{

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        return ( new \CBXSanitizer )->SanitizeHtml( $this->getValue() );
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return ( new \CBXSanitizer )->SanitizeHtml( $this->getValue() );
    }

}
