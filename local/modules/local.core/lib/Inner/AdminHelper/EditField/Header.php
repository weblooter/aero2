<?php
namespace Local\Core\Inner\AdminHelper\EditField;


class Header extends Base
{

    /**
     * {@inheritdoc}
     */
    public function getRowHtml()
    {
        return "<tr class=\"heading\">
                    <td colspan=\"2\">{$this->getValue()}</td>
                </tr>";
    }

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return false;
    }
}