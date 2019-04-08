<?php

namespace Local\Core\Inner\AdminHelper\Data\AttemptsTopUpBalanceLog;


use Local\Core\Model\Data\AttemptsTopUpBalanceLogTable;
use Local\Core\Model\Data\TariffTable;

/**
 * Class AdminList
 * @package Local\Core\Inner\AdminHelper\Data\Company
 */
class AdminList extends \Local\Core\Inner\AdminHelper\ListBase
{

    const ADMIN_ENTITY_VALUE = "model_data_attempts_top_up_balance_log";
    const ADMIN_ACTION_VALUE = "list";

    static $fields = [];
    static $currencies = [];

    /**
     * Ссылка в админку с учётом прав
     * <code>
     * ->getData["uri"]
     * </code>
     *
     * @param string $operation
     *
     * @return \Bitrix\Main\Result
     */
    public function getAdminUri(string $operation = "menuVisible"): \Bitrix\Main\Result
    {
        $result = new \Bitrix\Main\Result();

        $check = $this->checkRights($operation);
        if ($check->isSuccess()) {
            $result->setData([
                "uri" => \Local\Core\Inner\AdminHelper\AdminRoute::getUri([
                    \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
                    \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => self::ADMIN_ACTION_VALUE,
                ]),
            ]);
        } else {
            $result->addErrors($check->getErrors());
        }

        return $result;
    }

    /**
     * #TODO корректировка прав
     * {@inheritdoc}
     */
    protected function checkRights($operation = ""): \Bitrix\Main\Result
    {
        $result = new \Bitrix\Main\Result();

        if ($this->user instanceof \CUser && !$this->user->isAdmin()) {
            $result->addError(new \Bitrix\Main\Error("Необходим доступ администратора"));
        }

        switch ($operation) {

            case "can_add":
                break;

            case "can_edit":
                break;

            case "can_delete":
                break;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTableListId()
    {
        return \str_replace(["\\"], ["_"], __CLASS__);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUpperButtons()
    {
        $buttons = [];

        if (
        $this->checkRights("can_add")
            ->isSuccess()
        ) {
            $buttons = [
                [
                    "TEXT" => "Добавить",
                    "LINK" => $this->getEditLink(),
                    "TITLE" => "Добавить",
                    "ICON" => "btn_new",
                ],
            ];
        }

        return $buttons;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilterSearchFields()
    {
        return [
            "ID" => [
                "NAME" => self::$fields["ID"],
                "TYPE" => "TEXT",
            ],
            "DATE_CREATE" => [
                "NAME" => self::$fields["DATE_CREATE"],
                "TYPE" => "DATE_PERIOD",
            ],
            "DATE_MODIFIED" => [
                "NAME" => self::$fields["DATE_MODIFIED"],
                "TYPE" => "DATE_PERIOD",
            ],

            "USER_ID" => [
                "NAME" => self::$fields["USER_ID"],
                "TYPE" => "TEXT",
            ],
            "HANDLER" => [
                "NAME" => self::$fields["HANDLER"],
                "TYPE" => "SELECT",
                'VARIANTS' => \Local\Core\Inner\Payment\Factory::getHandlersList()
            ],
            "QUERY_CHECK_RESULT" => [
                "NAME" => self::$fields["QUERY_CHECK_RESULT"],
                "TYPE" => "SELECT",
                "VARIANTS" => AttemptsTopUpBalanceLogTable::getEnumFieldHtmlValues('QUERY_CHECK_RESULT')
            ],
            "TRY_TOP_UP_BALANCE_RESULT" => [
                "NAME" => self::$fields["TRY_TOP_UP_BALANCE_RESULT"],
                "TYPE" => "SELECT",
                "VARIANTS" => AttemptsTopUpBalanceLogTable::getEnumFieldHtmlValues('TRY_TOP_UP_BALANCE_RESULT')
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareFilterList($filterSearch)
    {
        $arFilter = [];

        foreach ($filterSearch ?? [] as $code => $value) {

            if (!empty(trim($value))) {
                switch ($code) {

                    case "ID":
                    case "USER_ID":
                    case "HANDLER":
                        $arFilter["=".$code] = trim($value);
                        break;
                    case "QUERY_CHECK_RESULT":
                        if (in_array($value, AttemptsTopUpBalanceLogTable::getEnumFieldValues('QUERY_CHECK_RESULT'))) {
                            $arFilter["=".$code] = trim($value);
                        }
                        break;
                    case "TRY_TOP_UP_BALANCE_RESULT":
                        if (in_array($value, AttemptsTopUpBalanceLogTable::getEnumFieldValues('TRY_TOP_UP_BALANCE_RESULT'))) {
                            $arFilter["=".$code] = trim($value);
                        }
                        break;

                    case "DATE_MODIFIED":
                        $arFilter[">=".$code] = $value;
                        break;
                    case "DATE_MODIFIED_2":
                        $arFilter["<=".$code] = $value;
                        break;

                    case "DATE_CREATE":
                        $arFilter[">=".$code] = $value;
                        break;
                    case "DATE_CREATE_2":
                        $arFilter["<=".$code] = $value;
                        break;
                }
            }
        }

        return $arFilter;
    }

    /**
     * {@inheritdoc}
     */
    protected function getList()
    {
        return \Local\Core\Model\Data\AttemptsTopUpBalanceLogTable::getlist([
            "select" => [
                "*",
                'USER_DATA_' => 'USER_INFO'
            ],
            "filter" => $this->filterList,
            "order" => [$this->CAdminList->sort->getField() => $this->CAdminList->sort->getOrder()],
            'runtime' => [
                new \Bitrix\Main\ORM\Fields\Relations\Reference('USER_INFO', \Bitrix\Main\UserTable::class, \Bitrix\Main\ORM\Query\Join::on('this.USER_ID', 'ref.ID'))
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getHeaders()
    {
        $columns = [];

        foreach (self::$fields as $columnCode => $columnName) {
            $columns[] = [
                "id" => $columnCode,
                "content" => $columnName,
                "sort" => $columnCode,
                "default" => $columnCode == "ID",
            ];
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareRowField(\CAdminListRow $row, $fields)
    {

        $row->AddViewField("ID", '<a href="'.$this->getEditLink($fields).'">'.$fields["ID"].'</a>');
        $row->AddViewField("USER_ID",
            '<a href="/bitrix/admin/user_edit.php?lang=ru&ID='.$fields["USER_ID"].'" target="_blank">['.$fields["USER_ID"].'] '.$fields['USER_DATA_LAST_NAME'].' '.$fields['USER_DATA_NAME'].'</a>');

        $row->AddViewField("HANDLER", \Local\Core\Inner\Payment\Factory::getHandlersList()[$fields["HANDLER"]]);
        $row->AddViewField("QUERY_CHECK_RESULT", AttemptsTopUpBalanceLogTable::getEnumFieldHtmlValues('QUERY_CHECK_RESULT')[$fields["QUERY_CHECK_RESULT"]]);
        $row->AddViewField("TRY_TOP_UP_BALANCE_RESULT", AttemptsTopUpBalanceLogTable::getEnumFieldHtmlValues('TRY_TOP_UP_BALANCE_RESULT')[$fields["TRY_TOP_UP_BALANCE_RESULT"]]);

        $row->AddViewField("QUERY_DATA", 'Нельзя отобразить в списке');
        $row->AddViewField("ADDITIONAL_DATA", 'Нельзя отобразить в списке');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRowAction($fields)
    {
        $actions = [];

        if (
        $this->checkRights("can_edit")
            ->isSuccess()
        ) {
            $actions[] = [
                "ICON" => "edit",
                "TEXT" => "Редактировать",
                "ACTION" => $this->CAdminList->ActionRedirect($this->getEditLink($fields)),
            ];
        }

        /*
        if (
        $this->checkRights("can_delete")
            ->isSuccess()
        ) {
            $addParams = \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY."=".self::ADMIN_ENTITY_VALUE."&".\Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION."=".self::ADMIN_ACTION_VALUE;
            $actions[] = [
                "ICON" => "delete",
                "TEXT" => "Удалить",
                "ACTION" => "if(confirm('Действительно удалить?')) ".$this->CAdminList->ActionDoGroup($fields["ID"], "delete", $addParams)
            ];
        }
        */

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGroupAction()
    {
        $actions = [];

        if (
        $this->checkRights("can_delete")
            ->isSuccess()
        ) {
            //            $actions["delete"] = "Удалить";
        }

        if (
        $this->checkRights("can_edit")
            ->isSuccess()
        ) {
            $actions["edit"] = "Редактировать";
        }

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    protected function editAction($id, $fields)
    {
        $result = new \Bitrix\Main\Result();
        $checkRights = $this->checkRights("can_edit");

        if ($checkRights->isSuccess()) {
            try {
                $arFields = [];

                $res = \Local\Core\Model\Data\AttemptsTopUpBalanceLogTable::update($id, $arFields);
                if (!$res->isSuccess()) {
                    $result->addErrors($res->getErrors());
                }

            } catch (\Exception $e) {
                $result->addError(new \Bitrix\Main\Error($e->getMessage()));
            }
        } else {
            $result->addErrors($checkRights->getErrors());
        }

        return $result;
    }

    /**
     * Удаление элемента
     *
     * @param $id
     *
     * @return \Bitrix\Main\Result
     */
    protected function deleteAction($id)
    {
        $result = new \Bitrix\Main\Result();
        $checkRights = $this->checkRights("can_delete");

        if ($checkRights->isSuccess()) {
            try {

                $res = \Local\Core\Model\Data\AttemptsTopUpBalanceLogTable::delete((int)$id);
                if (!$res->isSuccess()) {
                    $result->addErrors($res->getErrors());
                }

            } catch (\Exception $e) {
                $result->addError(new \Bitrix\Main\Error($e->getMessage()));
            }
        } else {
            $result->addErrors($checkRights->getErrors());
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getEditLink($fields = [])
    {
        return \Local\Core\Inner\AdminHelper\AdminRoute::getUri([
            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => AdminEdit::ADMIN_ACTION_VALUE,
            "id" => $fields["ID"],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilterUri(array $arData = []): string
    {
        return \Local\Core\Inner\AdminHelper\AdminRoute::getUri([
            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => self::ADMIN_ACTION_VALUE,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSortUri(): string
    {
        return \Local\Core\Inner\AdminHelper\AdminRoute::getUri([
            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => self::ADMIN_ACTION_VALUE,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        foreach (\Local\Core\Model\Data\AttemptsTopUpBalanceLogTable::getMap() ?? [] as $column) {
            $GLOBALS['APPLICATION']->SetTitle('Логи попыток пополнения');

            if ($column instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                self::$fields[$column->getColumnName()] = $column->getTitle();
            }
        }

        parent::render();
    }
}
