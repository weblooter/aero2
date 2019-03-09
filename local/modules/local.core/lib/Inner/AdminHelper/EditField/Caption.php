<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class Caption extends Base
{

    /**
     * {@inheritdoc}
     */
    public function getRowHtml()
    {
        return "<tr class='heading'>
                    <td colspan='2'>{$this->getTitle()}</td>
                </tr>";
    }

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        return "";
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return "";
    }
}
