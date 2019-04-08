<?php

namespace Local\Core\Inner\AdminHelper\Data\Store;


use Local\Core\Model\Data\StoreTable;
use Local\Core\Model\Data\TariffTable;

/**
 * Class AdminList
 * @package Local\Core\Inner\AdminHelper\Data\Company
 */
class AdminList extends \Local\Core\Inner\AdminHelper\ListBase
{

    const ADMIN_ENTITY_VALUE = "model_data_store";
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
                    "TITLE" => "Добавить магазин",
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
        $rsTariff = TariffTable::getList([
            'order' => ['ACTIVE' => 'DESC', 'CODE' => 'ASC'],
            'select' => ['CODE', 'NAME', 'ACTIVE']
        ]);
        $arTariff = [];
        while ($ar = $rsTariff->fetch()) {
            $arTariff[$ar['CODE']] = $ar['ACTIVE'].' ['.$ar['CODE'].'] '.$ar['NAME'];
        }

        return [
            "ID" => [
                "NAME" => self::$fields["ID"],
                "TYPE" => "TEXT",
            ],
            "ACTIVE" => [
                "NAME" => self::$fields["ACTIVE"],
                "TYPE" => "SELECT",
                "VARIANTS" => StoreTable::getEnumFieldHtmlValues('ACTIVE')
            ],
            "DATE_CREATE" => [
                "NAME" => self::$fields["DATE_CREATE"],
                "TYPE" => "DATE_PERIOD",
            ],
            "DATE_MODIFIED" => [
                "NAME" => self::$fields["DATE_MODIFIED"],
                "TYPE" => "DATE_PERIOD",
            ],

            "COMPANY_ID" => [
                "NAME" => self::$fields["COMPANY_ID"],
                "TYPE" => "TEXT",
            ],
            "NAME" => [
                "NAME" => self::$fields["NAME"],
                "TYPE" => "TEXT",
            ],
            "DOMAIN" => [
                "NAME" => self::$fields["DOMAIN"],
                "TYPE" => "TEXT",
            ],
            "RESOURCE_TYPE" => [
                "NAME" => self::$fields["RESOURCE_TYPE"],
                "TYPE" => "SELECT",
                "VARIANTS" => StoreTable::getEnumFieldHtmlValues('RESOURCE_TYPE')
            ],
            "FILE_LINK" => [
                "NAME" => self::$fields["FILE_LINK"],
                "TYPE" => "TEXT",
            ],
            "HTTP_AUTH" => [
                "NAME" => self::$fields["HTTP_AUTH"],
                "TYPE" => "SELECT",
                "VARIANTS" => StoreTable::getEnumFieldHtmlValues('HTTP_AUTH')
            ],
            "HTTP_AUTH_LOGIN" => [
                "NAME" => self::$fields["HTTP_AUTH_LOGIN"],
                "TYPE" => "TEXT",
            ],
            "HTTP_AUTH_PASS" => [
                "NAME" => self::$fields["HTTP_AUTH_PASS"],
                "TYPE" => "TEXT",
            ],
            "BEHAVIOR_IMPORT_ERROR" => [
                "NAME" => self::$fields["BEHAVIOR_IMPORT_ERROR"],
                "TYPE" => "SELECT",
                "VARIANTS" => StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')
            ],
            'TARIFF_CODE' => [
                "NAME" => self::$fields["TARIFF_CODE"],
                "TYPE" => "SELECT",
                "VARIANTS" => $arTariff
            ]
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
                    case "COMPANY_ID":
                    case "TARIFF_CODE":
                        $arFilter["=".$code] = trim($value);
                        break;

                    case "NAME":
                    case "DOMAIN":
                    case "FILE_LINK":
                    case "HTTP_AUTH_LOGIN":
                        $arFilter["%".$code] = trim($value);
                        break;

                    case "RESOURCE_TYPE":
                        if (in_array($value, StoreTable::getEnumFieldValues('RESOURCE_TYPE'))) {
                            $arFilter["=".$code] = trim($value);
                        }
                        break;

                    case "ACTIVE":
                    case "HTTP_AUTH":
                        if (
                        in_array($value, ['Y', 'N'])
                        ) {
                            $arFilter["=".$code] = trim($value);
                        }
                        break;

                    case "BEHAVIOR_IMPORT_ERROR":
                        if (in_array($value, StoreTable::getEnumFieldValues('BEHAVIOR_IMPORT_ERROR'))) {
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
        return \Local\Core\Model\Data\StoreTable::getlist([
            "select" => [
                "*",
                'COMPANY_DATA_' => 'COMPANY',
                'TARIFF_DATA_' => 'TARIFF'
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
        $row->AddViewField("COMPANY_ID",
            '<a href="'.((new \Local\Core\Inner\AdminHelper\Data\Company\AdminList())->getEditLink(['ID' => $fields["COMPANY_ID"]])).'" target="_blank">['.$fields["COMPANY_ID"].'] '
            .$fields['COMPANY_DATA_COMPANY_NAME_SHORT'].'</a>');
        $row->AddViewField("RESOURCE_TYPE", StoreTable::getEnumFieldHtmlValues('RESOURCE_TYPE')[$fields["RESOURCE_TYPE"]]);
        $row->AddViewField("HTTP_AUTH", StoreTable::getEnumFieldHtmlValues('HTTP_AUTH')[$fields["HTTP_AUTH"]]);

        $row->AddViewField("TARIFF", '<a href="'.((new \Local\Core\Inner\AdminHelper\Data\Tariff\AdminList())->getEditLink(['CODE' => $fields["TARIFF"]])).'" target="_blank">['.$fields["TARIFF"].'] '
                                     .$fields['TARIFF_DATA_TARIFF_NAME'].'</a>');
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
                    "ACTIVE" => $fields["ACTIVE"] ?? "N"
                ];

                $res = \Local\Core\Model\Data\StoreTable::update($id, $arFields);
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

                $res = \Local\Core\Model\Data\StoreTable::delete((int)$id);
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
        foreach (\Local\Core\Model\Data\StoreTable::getMap() ?? [] as $column) {
            $GLOBALS['APPLICATION']->SetTitle('Сайты');

            if ($column instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                self::$fields[$column->getColumnName()] = $column->getTitle();
            }
        }

        parent::render();
    }
}
