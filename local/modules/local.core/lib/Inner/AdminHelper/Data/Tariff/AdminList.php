<?php

namespace Local\Core\Inner\AdminHelper\Data\Tariff;


use Local\Core\Model\Data\TariffTable;

/**
 * Class AdminList
 * @package Local\Core\Inner\AdminHelper\Data\Company
 */
class AdminList extends \Local\Core\Inner\AdminHelper\ListBase
{

    const ADMIN_ENTITY_VALUE = "model_data_tariff";
    const ADMIN_ACTION_VALUE = "list";

    static $fields = [];
    static $currencies = [];
    static $SWITCH_AFTER_ACTIVE_TO = [];

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
                    "TITLE" => "Добавить тариф",
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
            "ACTIVE" => [
                "NAME" => self::$fields["ACTIVE"],
                "TYPE" => "SELECT",
                "VARIANTS" => TariffTable::getEnumFieldHtmlValues('ACTIVE')
            ],
            "DATE_CREATE" => [
                "NAME" => self::$fields["DATE_CREATE"],
                "TYPE" => "DATE_PERIOD",
            ],
            "DATE_MODIFIED" => [
                "NAME" => self::$fields["DATE_MODIFIED"],
                "TYPE" => "DATE_PERIOD",
            ],

            "DATE_ACTIVE_FROM" => [
                "NAME" => self::$fields["DATE_ACTIVE_FROM"],
                "TYPE" => "TEXT",
            ],
            "DATE_ACTIVE_TO" => [
                "NAME" => self::$fields["DATE_ACTIVE_TO"],
                "TYPE" => "TEXT",
            ],
            "NAME" => [
                "NAME" => self::$fields["NAME"],
                "TYPE" => "TEXT",
            ],
            "CODE" => [
                "NAME" => self::$fields["CODE"],
                "TYPE" => "TEXT",
            ],
            "LIMIT_TRADING_PLATFORM" => [
                "NAME" => self::$fields["LIMIT_TRADING_PLATFORM"],
                "TYPE" => "TEXT",
            ],
            "LIMIT_IMPORT_PRODUCTS" => [
                "NAME" => self::$fields["LIMIT_IMPORT_PRODUCTS"],
                "TYPE" => "TEXT",
            ],
            "PRICE_PER_TRADING_PLATFORM" => [
                "NAME" => self::$fields["PRICE_PER_TRADING_PLATFORM"],
                "TYPE" => "TEXT",
            ],
            "IS_DEFAULT" => [
                "NAME" => self::$fields["IS_DEFAULT"],
                "TYPE" => "SELECT",
                "VARIANTS" => TariffTable::getEnumFieldHtmlValues('IS_DEFAULT')
            ],
            "IS_ACTION" => [
                "NAME" => self::$fields["IS_ACTION"],
                "TYPE" => "SELECT",
                "VARIANTS" => TariffTable::getEnumFieldHtmlValues('IS_ACTION')
            ],
            "TYPE" => [
                "NAME" => self::$fields["TYPE"],
                "TYPE" => "SELECT",
                "VARIANTS" => TariffTable::getEnumFieldHtmlValues('TYPE')
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
                    case "LIMIT_TRADING_PLATFORM":
                    case "LIMIT_IMPORT_PRODUCTS":
                    case "PRICE_PER_TRADING_PLATFORM":
                        $arFilter["=".$code] = trim($value);
                        break;

                    case "NAME":
                    case "CODE":
                        $arFilter["%".$code] = trim($value);
                        break;

                    case "RESOURCE_TYPE":
                        if (in_array($value, TariffTable::getEnumFieldValues('RESOURCE_TYPE'))) {
                            $arFilter["=".$code] = trim($value);
                        }
                        break;

                    case "ACTIVE":
                    case "IS_DEFAULT":
                    case "IS_ACTION":
                        if (
                        in_array($value, ['Y', 'N'])
                        ) {
                            $arFilter["=".$code] = trim($value);
                        }
                        break;

                    case "TYPE":
                        if (
                        in_array($value, TariffTable::getEnumFieldValues('TYPE'))
                        ) {
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

                    case "DATE_ACTIVE_FROM":
                        $arFilter[">=".$code] = $value;
                        break;
                    case "DATE_ACTIVE_FROM_2":
                        $arFilter["<=".$code] = $value;
                        break;

                    case "DATE_ACTIVE_TO":
                        $arFilter[">=".$code] = $value;
                        break;
                    case "DATE_ACTIVE_TO_2":
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
        $rsTariffList = TariffTable::getList([
            'order' => ['ACTIVE' => 'DESC', 'CODE' => 'ASC'],
            'select' => [
                'NAME',
                'CODE',
                'ACTIVE'
            ]
        ]);
        while ($ar = $rsTariffList->fetch()) {
            self::$SWITCH_AFTER_ACTIVE_TO[$ar['CODE']] = $ar['ACTIVE'].' ['.$ar['CODE'].'] '.$ar['NAME'];
        }

        return \Local\Core\Model\Data\TariffTable::getlist([
            "select" => [
                "*"
            ],
            "filter" => $this->filterList,
            "order" => [$this->CAdminList->sort->getField() => $this->CAdminList->sort->getOrder()],
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
        $row->AddCheckField("ACTIVE");
        $row->AddViewField("IS_DEFAULT", TariffTable::getEnumFieldHtmlValues('IS_DEFAULT')[$fields["IS_DEFAULT"]]);
        $row->AddViewField("IS_ACTION", TariffTable::getEnumFieldHtmlValues('IS_ACTION')[$fields["IS_ACTION"]]);
        $row->AddViewField("TYPE", TariffTable::getEnumFieldHtmlValues('TYPE')[$fields["TYPE"]]);

        $row->AddViewField("SWITCH_AFTER_ACTIVE_TO", self::$SWITCH_AFTER_ACTIVE_TO[$fields["SWITCH_AFTER_ACTIVE_TO"]]);

        $row->AddInputField('SORT');
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
            $actions["delete"] = "Удалить";
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
                $arFields = [
                    "ACTIVE" => $fields["ACTIVE"] ?? "N",
                    "SORT" => $fields["SORT"] ?? 50
                ];

                $res = \Local\Core\Model\Data\TariffTable::update($id, $arFields);
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

                $res = \Local\Core\Model\Data\TariffTable::delete((int)$id);
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
        foreach (\Local\Core\Model\Data\TariffTable::getMap() ?? [] as $column) {
            $GLOBALS['APPLICATION']->SetTitle('Тарифы');

            if ($column instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                self::$fields[$column->getColumnName()] = $column->getTitle();
            }
        }

        parent::render();
    }
}
