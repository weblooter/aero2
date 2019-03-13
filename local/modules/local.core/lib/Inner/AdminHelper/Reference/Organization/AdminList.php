<?php

namespace Local\Core\Inner\AdminHelper\Reference\Organization;


/**
 * Class AdminList
 * @package Local\Core\Inner\AdminHelper\Reference\Transport\Body
 */
class AdminList extends \Local\Core\Inner\AdminHelper\ListBase
{

    const ADMIN_ENTITY_VALUE = "reference_organization";
    const ADMIN_ACTION_VALUE = "list";

    static $fields          = [];
    static $currencies      = [];
    static $approved_values = [
        "N" => "Нет",
        "P" => "В процессе проверки",
        "E" => "Ошибочные данные",
        "Y" => "Да"
    ];

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
        $result = new \Bitrix\Main\Result;

        $check = $this->checkRights($operation);
        if( $check->isSuccess() )
        {
            $result->setData([
                "uri" => \Local\Core\Inner\AdminHelper\AdminRoute::getUri([
                    \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
                    \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => self::ADMIN_ACTION_VALUE,
                ]),
            ]);
        }
        else
        {
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

        if( $this->user instanceof \CUser && !$this->user->isAdmin() )
        {
            $result->addError(new \Bitrix\Main\Error("Необходим доступ администратора"));
        }

        switch( $operation )
        {

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

        if( $this->checkRights("can_add")->isSuccess() )
        {
            $buttons = [
                [
                    "TEXT" => "Добавить",
                    "LINK" => $this->getEditLink(),
                    "TITLE" => "Добавить юридическое лицо",
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
            "APPROVED" => [
                "NAME" => self::$fields["APPROVED"],
                "TYPE" => "SELECT",
                "VARIANTS" => self::$approved_values
            ],
            "ACTIVE" => [
                "NAME" => self::$fields["ACTIVE"],
                "TYPE" => "SELECT",
                "VARIANTS" => [
                    "Y" => "Да",
                    "N" => "Нет",
                ]
            ],
            "NAME" => [
                "NAME" => self::$fields["NAME"],
                "TYPE" => "TEXT",
            ],
            "OPF" => [
                "NAME" => self::$fields["OPF"],
                "TYPE" => "TEXT",
            ],
            "INN" => [
                "NAME" => self::$fields["INN"],
                "TYPE" => "TEXT",
            ],
            "KPP" => [
                "NAME" => self::$fields["KPP"],
                "TYPE" => "TEXT",
            ],
            "LEGAL_ADDRESS" => [
                "NAME" => self::$fields["LEGAL_ADDRESS"],
                "TYPE" => "TEXT",
            ],
            "POST_ADDRESS" => [
                "NAME" => self::$fields["POST_ADDRESS"],
                "TYPE" => "TEXT",
            ],
            "BANK" => [
                "NAME" => self::$fields["BANK"],
                "TYPE" => "TEXT",
            ],
            "BIK" => [
                "NAME" => self::$fields["BIK"],
                "TYPE" => "TEXT",
            ],
            "RSCH" => [
                "NAME" => self::$fields["RSCH"],
                "TYPE" => "TEXT",
            ],
            "FAX" => [
                "NAME" => self::$fields["FAX"],
                "TYPE" => "TEXT",
            ],
            "DATE_INSERT" => [
                "NAME" => self::$fields["DATE_INSERT"],
                "TYPE" => "DATE_PERIOD",
            ],
            "TIMESTAMP_X" => [
                "NAME" => self::$fields["TIMESTAMP_X"],
                "TYPE" => "DATE_PERIOD",
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareFilterList($filterSearch)
    {
        $arFilter = [];

        foreach( $filterSearch ?? [] as $code => $value )
        {

            if( !empty(trim($value)) )
            {
                switch( $code )
                {

                    case "ID":
                        $arFilter["=".$code] = trim($value);
                        break;

                    case "NAME":
                    case "OPF":
                    case "INN":
                    case "KPP":
                    case "LEGAL_ADDRESS":
                    case "POST_ADDRESS":
                    case "BANK":
                    case "BIK":
                    case "RSCH":
                    case "FAX":
                        $arFilter["%".$code] = trim($value);
                        break;

                    case "APPROVED":
                        if( isset(self::$approved_values[$value]) )
                        {
                            $arFilter["=".$code] = trim($value);
                        }
                        break;

                    case "ACTIVE":
                        if( in_array($value, ['Y', 'N']) )
                        {
                            $arFilter["=".$code] = trim($value);
                        }
                        break;

                    case "TIMESTAMP_X":
                        $arFilter[">=".$code] = $value;
                        break;
                    case "TIMESTAMP_X_2":
                        $arFilter["<=".$code] = $value;
                        break;

                    case "DATE_INSERT":
                        $arFilter[">=".$code] = $value;
                        break;
                    case "DATE_INSERT_2":
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
        return \Local\Core\Model\Reference\OrganizationTable::getlist([
            "select" => [
                "*",
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

        foreach( self::$fields as $columnCode => $columnName )
        {
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


        $row->AddViewField("APPROVED", ( self::$approved_values[$fields["APPROVED"]] ? : "Ошибка: Неизвестный статус" ));
        $row->AddCheckField("ACTIVE");

        $row->AddInputField("NAME", ['size' => 20]);
        $row->AddInputField("OPF", ['size' => 20]);
        $row->AddInputField("INN", ['size' => 20]);
        $row->AddInputField("KPP", ['size' => 20]);
        $row->AddInputField("LEGAL_ADDRESS", ['size' => 20]);
        $row->AddInputField("POST_ADDRESS", ['size' => 20]);
        $row->AddInputField("BANK", ['size' => 20]);
        $row->AddInputField("BIK", ['size' => 20]);
        $row->AddInputField("RSCH", ['size' => 20]);
        $row->AddInputField("FAX", ['size' => 20]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRowAction($fields)
    {
        $actions = [];

        if( $this->checkRights("can_edit")->isSuccess() )
        {
            $actions[] = [
                "ICON" => "edit",
                "TEXT" => "Редактировать",
                "ACTION" => $this->CAdminList->ActionRedirect($this->getEditLink($fields)),
            ];
        }

        if( $this->checkRights("can_delete")->isSuccess() )
        {
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

        if( $this->checkRights("can_delete")->isSuccess() )
        {
            $actions["delete"] = "Удалить";
        }

        if( $this->checkRights("can_edit")->isSuccess() )
        {
            $actions["edit"] = "Редактировать";
        }

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    protected function editAction($id, $fields)
    {
        $result = new \Bitrix\Main\Result;
        $checkRights = $this->checkRights("can_edit");

        if( $checkRights->isSuccess() )
        {
            try
            {
                $arFields = [
                    "APPROVED" => $fields["APPROVED"] ?? "N",
                    "ACTIVE" => $fields["ACTIVE"] ?? "N",
                    "NAME" => trim($fields['NAME']),
                    "OPF" => trim($fields['OPF']),
                    "INN" => trim($fields['INN']),
                    "KPP" => trim($fields['KPP']),
                    "LEGAL_ADDRESS" => trim($fields['LEGAL_ADDRESS']),
                    "POST_ADDRESS" => trim($fields['POST_ADDRESS']),
                    "BANK" => trim($fields['BANK']),
                    "BIK" => trim($fields['BIK']),
                    "RSCH" => trim($fields['RSCH']),
                    "FAX" => trim($fields['FAX']),
                ];

                $res = \Local\Core\Model\Reference\OrganizationTable::update($id, $arFields);
                if( !$res->isSuccess() )
                {
                    $result->addErrors($res->getErrors());
                }

            }
            catch( \Exception $e )
            {
                $result->addError(new \Bitrix\Main\Error($e->getMessage()));
            }
        }
        else
        {
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
        $result = new \Bitrix\Main\Result;
        $checkRights = $this->checkRights("can_delete");

        if( $checkRights->isSuccess() )
        {
            try
            {

                $res = \Local\Core\Model\Reference\OrganizationTable::delete((int)$id);
                if( !$res->isSuccess() )
                {
                    $result->addErrors($res->getErrors());
                }

            }
            catch( \Exception $e )
            {
                $result->addError(new \Bitrix\Main\Error($e->getMessage()));
            }
        }
        else
        {
            $result->addErrors($checkRights->getErrors());
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditLink($fields = [])
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
        foreach( \Local\Core\Model\Reference\OrganizationTable::getMap() ?? [] as $column )
        {
            if( $column instanceof \Bitrix\Main\ORM\Fields\ScalarField )
            {
                self::$fields[$column->getColumnName()] = $column->getTitle();
            }
        }

        parent::render();
    }
}
