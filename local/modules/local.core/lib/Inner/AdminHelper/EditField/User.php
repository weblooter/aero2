<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class User extends Base
{

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        return FindUserID(
            $this->getCode(),
            $this->getValue(),
            "",
            "post_form"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        $result = "";

        if( (int)$this->getValue() > 0 )
        {

            try
            {
                $user = \Bitrix\Main\UserTable::getList(
                    [
                        "select" => ["ID", "LOGIN", "NAME", "LAST_NAME"],
                        "filter" => ["ID" => (int)$this->getValue()],
                        "limit"  => 1
                    ]
                )->fetch();
            }
            catch( \Exception $e )
            {
                return $result;
            }

            if( !empty($user) )
            {
                $result = "[<a href=\"/bitrix/admin/user_edit.php?ID={$user["ID"]}&lang=ru\">{$user["ID"]}</a>] ({$user["LOGIN"]}) {$user["NAME"]} {$user["LAST_NAME"]}";
            }
        }

        return $result;
    }

}
