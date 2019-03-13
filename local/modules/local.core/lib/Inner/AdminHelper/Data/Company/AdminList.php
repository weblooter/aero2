<?php

namespace Local\Core\Inner\AdminHelper\Data\Company;


/**
 * Class AdminList
 * @package Local\Core\Inner\AdminHelper\Data\Company
 */
class AdminList extends \Local\Core\Inner\AdminHelper\ListBase
{

    const ADMIN_ENTITY_VALUE = "data_company";
    const ADMIN_ACTION_VALUE = "list";

    static $fields          = [];
    static $currencies      = [];
    static $approved_values = [
        "N" => "Ожидается проверка",
        "E" => "Проверка не пройдена",
        "Y" => "Проверка пройдена"
    ];
    static $verified_values = [
        "N" => "Ожидается проверка",
        "E" => "Проверка не пройдена",
        "Y" => "Проверка пройдена"
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
                    "TITLE" => "Добавить компанию",
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
                "VARIANTS" => [
                    "Y" => "Да",
                    "N" => "Нет",
                ]
            ],
            "USER_OWN_ID" => [
                "NAME" => self::$fields["USER_OWN_ID"],
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
            "VERIFIED" => [
                "NAME" => self::$fields["VERIFIED"],
                "TYPE" => "SELECT",
                "VARIANTS" => self::$verified_values
            ],
            "COMPANY_INN" => [
                "NAME" => self::$fields["COMPANY_INN"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_NAME_SHORT" => [
                "NAME" => self::$fields["COMPANY_NAME_SHORT"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_NAME_FULL" => [
                "NAME" => self::$fields["COMPANY_NAME_FULL"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_OGRN" => [
                "NAME" => self::$fields["COMPANY_OGRN"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_KPP" => [
                "NAME" => self::$fields["COMPANY_KPP"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_OKPO" => [
                "NAME" => self::$fields["COMPANY_OKPO"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_OKTMO" => [
                "NAME" => self::$fields["COMPANY_OKTMO"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_DIRECTOR" => [
                "NAME" => self::$fields["COMPANY_DIRECTOR"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_ACCOUNTANT" => [
                "NAME" => self::$fields["COMPANY_ACCOUNTANT"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_ADDRESS_COUNTRY" => [
                "NAME" => self::$fields["COMPANY_ADDRESS_COUNTRY"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_ADDRESS_REGION" => [
                "NAME" => self::$fields["COMPANY_ADDRESS_REGION"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_ADDRESS_AREA" => [
                "NAME" => self::$fields["COMPANY_ADDRESS_AREA"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_ADDRESS_CITY" => [
                "NAME" => self::$fields["COMPANY_ADDRESS_CITY"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_ADDRESS_ADDRESS" => [
                "NAME" => self::$fields["COMPANY_ADDRESS_ADDRESS"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_ADDRESS_OFFICE" => [
                "NAME" => self::$fields["COMPANY_ADDRESS_OFFICE"],
                "TYPE" => "TEXT",
            ],
            "COMPANY_ADDRESS_ZIP" => [
                "NAME" => self::$fields["COMPANY_ADDRESS_ZIP"],
                "TYPE" => "TEXT",
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
                    case "USER_OWN_ID":
                        $arFilter["=".$code] = trim($value);
                        break;

                    case "COMPANY_INN":
                    case "COMPANY_NAME_SHORT":
                    case "COMPANY_NAME_FULL":
                    case "COMPANY_OGRN":
                    case "COMPANY_KPP":
                    case "COMPANY_OKPO":
                    case "COMPANY_OKTMO":
                    case "COMPANY_DIRECTOR":
                    case "COMPANY_ACCOUNTANT":
                    case "COMPANY_ADDRESS_COUNTRY":
                    case "COMPANY_ADDRESS_REGION":
                    case "COMPANY_ADDRESS_AREA":
                    case "COMPANY_ADDRESS_CITY":
                    case "COMPANY_ADDRESS_ADDRESS":
                    case "COMPANY_ADDRESS_OFFICE":
                    case "COMPANY_ADDRESS_ZIP":
                        $arFilter["%".$code] = trim($value);
                        break;

                    case "VERIFIED":
                        if( isset(self::$verified_values[$value]) )
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
        return \Local\Core\Model\Data\CompanyTable::getlist([
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


        $row->AddCheckField("ACTIVE");

        $row->AddInputField("USER_OWN_ID", ['size' => 20]);
        $row->AddViewField("VERIFIED", ( self::$verified_values[$fields["VERIFIED"]] ? : "Ошибка: Неизвестный статус" ));
        $row->AddSelectField("VERIFIED", self::$verified_values);

        $row->AddInputField("COMPANY_INN", ['size' => 20]);
        $row->AddInputField("COMPANY_NAME_SHORT", ['size' => 20]);
        $row->AddInputField("COMPANY_NAME_FULL", ['size' => 20]);
        $row->AddInputField("COMPANY_OGRN", ['size' => 20]);
        $row->AddInputField("COMPANY_KPP", ['size' => 20]);
        $row->AddInputField("COMPANY_OKPO", ['size' => 20]);
        $row->AddInputField("COMPANY_OKTMO", ['size' => 20]);
        $row->AddInputField("COMPANY_DIRECTOR", ['size' => 20]);
        $row->AddInputField("COMPANY_ACCOUNTANT", ['size' => 20]);
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
                    "ACTIVE" => $fields["ACTIVE"] ?? "N",
                    "USER_OWN_ID" => trim($fields['USER_OWN_ID']),
                    "VERIFIED" => $fields["VERIFIED"] ?? "N",
                    "COMPANY_INN" => trim($fields['COMPANY_INN']),
                    "COMPANY_NAME_SHORT" => trim($fields['COMPANY_NAME_SHORT']),
                    "COMPANY_NAME_FULL" => trim($fields['COMPANY_NAME_FULL']),
                    "COMPANY_OGRN" => trim($fields['COMPANY_OGRN']),
                    "COMPANY_KPP" => trim($fields['COMPANY_KPP']),
                    "COMPANY_OKPO" => trim($fields['COMPANY_OKPO']),
                    "COMPANY_OKTMO" => trim($fields['COMPANY_OKTMO']),
                    "COMPANY_DIRECTOR" => trim($fields['COMPANY_DIRECTOR']),
                    "COMPANY_ACCOUNTANT" => trim($fields['COMPANY_ACCOUNTANT']),
                ];

                $res = \Local\Core\Model\Data\CompanyTable::update($id, $arFields);
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

                $res = \Local\Core\Model\Data\CompanyTable::delete((int)$id);
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
        foreach( \Local\Core\Model\Data\CompanyTable::getMap() ?? [] as $column )
        {
            if( $column instanceof \Bitrix\Main\ORM\Fields\ScalarField )
            {
                self::$fields[$column->getColumnName()] = $column->getTitle();
            }
        }

        parent::render();
    }
}
