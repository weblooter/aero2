<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class Custom extends Base
{

    /**
     * {@inheritdoc}
     */
    public function getRowHtml()
    {
        return "<tr>
                     <td>" . (($this->isEditable === true) ? $this->getEditFieldHtml() : $this->getViewFieldHtml()) . "</td>
                </tr>";
    }

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        return $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return $this->getValue();
    }
}
