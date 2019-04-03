<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class Editor extends Base
{

    /**
     * {@inheritdoc}
     */
    public function getRowHtml()
    {
        return "<tr class=\"heading\">
                    <td colspan=\"2\">{$this->getTitle()}</td>
                </tr>
                <tr>
                    <td colspan=\"2\">".($this->isEditable === true) ? $this->getEditFieldHtml() : $this->getViewFieldHtml()."</td>
                </tr>";
    }

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        ob_start();

        \CFileMan::AddHTMLEditorFrame($this->getCode(), $this->getValue(), "", "html", [
                "height" => 450,
                "width" => "100%"
            ], "N", 0, "", "", "s1", true, false, [
                "toolbarConfig" => \CFileMan::GetEditorToolbarConfig("admin"),
                "saveEditorKey" => 1,
                "hideTypeSelector" => "N",
            ]);

        $as = ob_get_contents();
        ob_end_clean();

        return $as;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return (new \CBXSanitizer())->SanitizeHtml($this->getValue());
    }

}
