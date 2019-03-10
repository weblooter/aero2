<?php

namespace Local\Core\Inner\AdminHelper\Data\Company;

/**
 * Class AdminList
 * @package Local\Core\Inner\AdminHelper\Data\Company
 */
class AdminEdit extends \Local\Core\Inner\AdminHelper\EditBase
{

    const ADMIN_ENTITY_VALUE = 'data_company';
    const ADMIN_ACTION_VALUE = 'edit';

    static $approved_values = [
        "N" => "Нет",
        "P" => "В процессе проверки",
        "E" => "Ошибочные данные",
        "Y" => "Да"
    ];

    static $verified_values = [
        "N" => "Ожидается проверка",
        "E" => "Проверка не пройдена",
        "Y" => "Проверка пройдена"
    ];

    /**
     * Ссылка в админку с учётом прав
     * <code>
     * ->getData['uri']
     * </code>
     *
     * @param string $operation
     *
     * @return \Bitrix\Main\Result
     */
    public function getAdminUri(string $operation = 'menuVisible'): \Bitrix\Main\Result
    {
        $result = new \Bitrix\Main\Result;

        $check = $this->checkRights($operation);
        if ($check->isSuccess()) {
            $result->setData([
                'uri' => \Local\Core\Inner\AdminHelper\AdminRoute::getUri([
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
            $result->addError(new \Bitrix\Main\Error('Необходим доступ администратора'));
        }

        switch ($operation) {

            case 'can_add':
                break;

            case 'can_edit':
                break;

            case 'can_delete':
                break;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function setData()
    {
        $result = new \Bitrix\Main\Result();
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();

        if ($request->get("id") !== null) {
            try {

                $body = \Local\Core\Model\Data\CompanyTable::getList([
                    "select" => ["*"],
                    "filter" => [
                        "=ID" => (int)$request->get("id")
                    ],
                    "limit" => 1
                ])->fetch();

            } catch (\Exception $e) {
                $result->addError(new \Bitrix\Main\Error($e->getMessage()));

                return $result;
            }
        }

        if (!empty($body)) {
            $this->id = $body["ID"];
            $this->data = $body;
            $this->app->SetTitle("Редактирование компании");
        } else {
            $rightAdd = $this->checkRights("can_add");
            if ($rightAdd->isSuccess()) {
                $this->app->SetTitle("Создание компании");
            } else {
                $result->addErrors($rightAdd->getErrors());
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUpperButtons()
    {
        $buttons = [
            [
                "TEXT" => "Вернуться к списку ",
                "LINK" => $this->getListLink(),
                "TITLE" => "Вернуться к списку ",
                "ICON" => "btn_list",
            ]
        ];

        if ((int)$this->id > 0) {

            if ($this->checkRights("can_add")->isSuccess()) {
                $buttons[] = [
                    "TEXT" => "Добавить",
                    "LINK" => \Local\Core\Inner\AdminHelper\AdminRoute::getUri([
                        \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
                        \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => self::ADMIN_ACTION_VALUE,
                    ]),
                    "TITLE" => "Добавить компанию",
                    "ICON" => "btn_new",
                ];
            }

            if ($this->checkRights("can_delete")->isSuccess()) {
                $buttons[] = [
                    "TEXT" => "Удалить",
                    "LINK" => "javascript:if(confirm('Действительно удалить?'))window.location='" . $this->getEditLink(["id" => $this->id]) . "&action=delete&" . bitrix_sessid_get() . "';",
                    "TITLE" => "Удалить компанию",
                    "ICON" => "btn_delete",
                ];
            }

        }

        return $buttons;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTabsList()
    {
        return [
            [
                "DIV" => "main",
                "TAB" => "Основное",
                "TITLE" => "Основное"
            ],
            [
                "DIV" => "ur_data",
                "TAB" => "Данные юр. лица",
                "TITLE" => "Данные юр. лица"
            ],
            [
                "DIV" => "ur_address",
                "TAB" => "Юр. адрес",
                "TITLE" => "Юр. адрес"
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTabsContent()
    {

        $canEdit = (
            ((int)$this->id > 0 && $this->checkRights("can_edit")->isSuccess()) ||
            ((int)$this->id == 0 && $this->checkRights("can_add")->isSuccess())
        ) ? true : false;

        $columnName = [];
        $map = \Local\Core\Model\Data\CompanyTable::getMap();
        foreach ($map as $column) {
            if ($column instanceof \Bitrix\Main\ORM\Fields\ScalarField)
                $columnName[$column->getColumnName()] = $column->getTitle();
        }

        return [
            "main" => [
                ((int)$this->id > 0) ? new \Local\Core\Inner\AdminHelper\EditField\Html($columnName["ID"], "ID",
                    $this->id) : null,
                ((int)$this->id > 0) ? new \Local\Core\Inner\AdminHelper\EditField\Hidden("", "ID", $this->id) : null,

                (new \Local\Core\Inner\AdminHelper\EditField\Checkbox($columnName["ACTIVE"], "ACTIVE"))
                    ->setEditable($canEdit),

                (new \Local\Core\Inner\AdminHelper\EditField\User($columnName['USER_OWN_ID'], 'USER_OWN_ID'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Date($columnName["DATE_CREATE"], "DATE_CREATE"))
                    ->setEditable(false),

                (new \Local\Core\Inner\AdminHelper\EditField\Date($columnName["DATE_MODIFIED"], "DATE_MODIFIED"))
                    ->setEditable(false),

                (new \Local\Core\Inner\AdminHelper\EditField\Select($columnName["VERIFIED"], "VERIFIED"))
                    ->setEditable($canEdit)
                    ->setVariants(self::$verified_values),

                (new \Local\Core\Inner\AdminHelper\EditField\Textarea($columnName['VERIFIED_NOTE'], 'VERIFIED_NOTE'))
                    ->setEditable($canEdit)
                    ->setRequired(true),
            ],

            'ur_data' => [
                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_INN'], 'COMPANY_INN'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_NAME_SHORT'], 'COMPANY_NAME_SHORT'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_NAME_FULL'], 'COMPANY_NAME_FULL'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_OGRN'], 'COMPANY_OGRN'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_KPP'], 'COMPANY_KPP'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_OKPO'], 'COMPANY_OKPO'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_OKTMO'], 'COMPANY_OKTMO'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_DIRECTOR'], 'COMPANY_DIRECTOR'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_ACCOUNTANT'], 'COMPANY_ACCOUNTANT'))
                    ->setEditable($canEdit)
                    ->setRequired(true),
            ],

            'ur_address' => [
                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_ADDRESS_COUNTRY'], 'COMPANY_ADDRESS_COUNTRY'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_ADDRESS_REGION'], 'COMPANY_ADDRESS_REGION'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_ADDRESS_AREA'], 'COMPANY_ADDRESS_AREA'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_ADDRESS_CITY'], 'COMPANY_ADDRESS_CITY'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_ADDRESS_ADDRESS'], 'COMPANY_ADDRESS_ADDRESS'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_ADDRESS_OFFICE'], 'COMPANY_ADDRESS_OFFICE'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_ADDRESS_ZIP'], 'COMPANY_ADDRESS_ZIP'))
                    ->setEditable($canEdit)
                    ->setRequired(true),
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getNote()
    {
        return "";
    }

    /**
     * {@inheritdoc}
     */
    protected function editAction(\Bitrix\Main\HttpRequest $request)
    {
        $result = new \Bitrix\Main\Result();

        $id = (int)$request->getPost("ID");
        $arFields = [
            'ACTIVE' => $request->getPost("ACTIVE") ?? "N",
            'USER_OWN_ID' => trim($request->getPost('USER_OWN_ID')),
            'VERIFIED' => $request->getPost("VERIFIED") ?? "N",
            'VERIFIED_NOTE' => trim($request->getPost('VERIFIED_NOTE')),

            'COMPANY_INN' => trim($request->getPost('COMPANY_INN')),
            'COMPANY_NAME_SHORT' => trim($request->getPost('COMPANY_NAME_SHORT')),
            'COMPANY_NAME_FULL' => trim($request->getPost('COMPANY_NAME_FULL')),
            'COMPANY_OGRN' => trim($request->getPost('COMPANY_OGRN')),
            'COMPANY_KPP' => trim($request->getPost('COMPANY_KPP')),
            'COMPANY_OKPO' => trim($request->getPost('COMPANY_OKPO')),
            'COMPANY_OKTMO' => trim($request->getPost('COMPANY_OKTMO')),
            'COMPANY_DIRECTOR' => trim($request->getPost('COMPANY_DIRECTOR')),
            'COMPANY_ACCOUNTANT' => trim($request->getPost('COMPANY_ACCOUNTANT')),

            'COMPANY_ADDRESS_COUNTRY' => trim($request->getPost('COMPANY_ADDRESS_COUNTRY')),
            'COMPANY_ADDRESS_REGION' => trim($request->getPost('COMPANY_ADDRESS_REGION')),
            'COMPANY_ADDRESS_AREA' => trim($request->getPost('COMPANY_ADDRESS_AREA')),
            'COMPANY_ADDRESS_CITY' => trim($request->getPost('COMPANY_ADDRESS_CITY')),
            'COMPANY_ADDRESS_ADDRESS' => trim($request->getPost('COMPANY_ADDRESS_ADDRESS')),
            'COMPANY_ADDRESS_OFFICE' => trim($request->getPost('COMPANY_ADDRESS_OFFICE')),
            'COMPANY_ADDRESS_ZIP' => trim($request->getPost('COMPANY_ADDRESS_ZIP')),

        ];

        if ((int)$id > 0) {

            $rightEdit = $this->checkRights("can_edit");
            if ($rightEdit->isSuccess()) {
                try {
                    $res = \Local\Core\Model\Data\CompanyTable::update($id, $arFields);
                    if (!$res->isSuccess()) {
                        $result->addErrors($res->getErrors());
                    }
                } catch (\Exception $e) {
                    $result->addError(new \Bitrix\Main\Error($e->getMessage()));
                }
            } else {
                $result->addErrors($rightEdit->getErrors());
            }
        } else {

            $rightAdd = $this->checkRights("can_add");
            if ($rightAdd->isSuccess()) {
                try {
                    $res = \Local\Core\Model\Data\CompanyTable::add($arFields);
                    if ($res->isSuccess()) {
                        $this->id = $res->getId();
                    } else {
                        $result->addErrors($res->getErrors());
                    }
                } catch (\Exception $e) {
                    $result->addError(new \Bitrix\Main\Error($e->getMessage()));
                }
            } else {
                $result->addErrors($rightAdd->getErrors());
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function deleteAction(\Bitrix\Main\HttpRequest $request)
    {
        $result = new \Bitrix\Main\Result();

        if ($this->id) {
            $rightDelete = $this->checkRights("can_delete");

            if ($rightDelete->isSuccess()) {
                try {
                    $res = \Local\Core\Model\Data\CompanyTable::delete((int)$this->id);
                    if (!$res->isSuccess()) {
                        $result->addErrors($res->getErrors());
                    }
                } catch (\Exception $e) {
                    $result->addError(new \Bitrix\Main\Error($e->getMessage()));
                }
            } else {
                $result->addErrors($rightDelete->getErrors());
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListLink()
    {
        return \Local\Core\Inner\AdminHelper\AdminRoute::getUri([
            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => AdminList::ADMIN_ENTITY_VALUE,
            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => AdminList::ADMIN_ACTION_VALUE,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditLink($fields = [])
    {
        return \Local\Core\Inner\AdminHelper\AdminRoute::getUri([
            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => self::ADMIN_ACTION_VALUE,
            "id" => $this->id,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        parent::render();
    }
}
