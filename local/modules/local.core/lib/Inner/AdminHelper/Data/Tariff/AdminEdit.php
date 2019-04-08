<?php

namespace Local\Core\Inner\AdminHelper\Data\Tariff;

use Local\Core\Model\Data\TariffTable;

/**
 * Class AdminList
 * @package Local\Core\Inner\AdminHelper\Data\Company
 */
class AdminEdit extends \Local\Core\Inner\AdminHelper\EditBase
{

    const ADMIN_ENTITY_VALUE = 'model_data_tariff';
    const ADMIN_ACTION_VALUE = 'edit';

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
        $result = new \Bitrix\Main\Result();

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
        $request = \Bitrix\Main\Context::getCurrent()
            ->getRequest();

        if ($request->get("id") !== null) {
            try {

                $body = \Local\Core\Model\Data\TariffTable::getList([
                    "select" => [
                        "*"
                    ],
                    "filter" => [
                        "=ID" => (int)$request->get("id")
                    ],
                    "limit" => 1,
                ])
                    ->fetch();

            } catch (\Exception $e) {
                $result->addError(new \Bitrix\Main\Error($e->getMessage()));

                return $result;
            }
        }

        if (!empty($body)) {
            $this->id = $body["ID"];
            $this->data = $body;
            $this->app->SetTitle("Редактирование тарифа");
        } else {
            $rightAdd = $this->checkRights("can_add");
            if ($rightAdd->isSuccess()) {
                $this->app->SetTitle("Создание тарифа");
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

            if (
            $this->checkRights("can_add")
                ->isSuccess()
            ) {
                $buttons[] = [
                    "TEXT" => "Добавить",
                    "LINK" => \Local\Core\Inner\AdminHelper\AdminRoute::getUri([
                        \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
                        \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => self::ADMIN_ACTION_VALUE,
                    ]),
                    "TITLE" => "Добавить",
                    "ICON" => "btn_new",
                ];
            }

            if (
            $this->checkRights("can_delete")
                ->isSuccess()
            ) {
                $buttons[] = [
                    "TEXT" => "Удалить",
                    "LINK" => "javascript:if(confirm('Действительно удалить?'))window.location='".$this->getEditLink(["id" => $this->id])."&action=delete&".bitrix_sessid_get()."';",
                    "TITLE" => "Удалить",
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
                "DIV" => "limit",
                "TAB" => "Лимиты",
                "TITLE" => "Лимиты"
            ],
            [
                "DIV" => "active_dates",
                "TAB" => "Период активности",
                "TITLE" => "Период активности"
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTabsContent()
    {

        $canEdit = (((int)$this->id > 0
                     && $this->checkRights("can_edit")
                         ->isSuccess())
                    || ((int)$this->id == 0
                        && $this->checkRights("can_add")
                            ->isSuccess())) ? true : false;

        $columnName = [];
        $columnDefaultValue = [];
        $map = \Local\Core\Model\Data\TariffTable::getMap();
        foreach ($map as $column) {
            if ($column instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                $columnName[$column->getColumnName()] = $column->getTitle();
                $columnDefaultValue[$column->getColumnName()] = $column->getDefaultValue();
            }
        }

        $arTariffList = [];
        $rsTariffList = TariffTable::getList([
            'filter' => [
                'ACTIVE' => 'Y',
                '!ID' => $this->id
            ],
            'order' => ['ACTIVE' => 'DESC', 'CODE' => 'ASC'],
            'select' => [
                'NAME',
                'CODE',
                'ACTIVE'
            ]
        ]);
        while ($ar = $rsTariffList->fetch()) {
            $arTariffList[$ar['CODE']] = $ar['ACTIVE'].' ['.$ar['CODE'].'] '.$ar['NAME'];
        }

        return [
            "main" => [
                ((int)$this->id > 0) ? new \Local\Core\Inner\AdminHelper\EditField\Html($columnName["ID"], "ID", $this->id) : null,
                ((int)$this->id > 0) ? new \Local\Core\Inner\AdminHelper\EditField\Hidden("", "ID", $this->id) : null,

                (new \Local\Core\Inner\AdminHelper\EditField\Checkbox($columnName["ACTIVE"], "ACTIVE", ($this->data['ACTIVE'] ?? $columnDefaultValue['ACTIVE'])))->setEditable($canEdit),

                (new \Local\Core\Inner\AdminHelper\EditField\Date($columnName["DATE_CREATE"], "DATE_CREATE"))->setEditable(false),

                (new \Local\Core\Inner\AdminHelper\EditField\Date($columnName["DATE_MODIFIED"], "DATE_MODIFIED"))->setEditable(false),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['SORT'], 'SORT'))->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['NAME'], 'NAME'))->setEditable($canEdit)
                    ->setRequired(true),

                ((int)$this->id < 1) ? (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['CODE'], 'CODE'))->setEditable($canEdit)
                    ->setRequired(true) : null,

                ((int)$this->id > 0) ? (new \Local\Core\Inner\AdminHelper\EditField\Html($columnName["CODE"].' (что бы не уебать нечаянно - менять через базу)', "ID",
                    $this->data['CODE']))->setRequired(true) : null,

                (new \Local\Core\Inner\AdminHelper\EditField\Select($columnName["IS_DEFAULT"], "IS_DEFAULT", ($this->data['IS_DEFAULT'] ?? $columnDefaultValue['IS_DEFAULT'])))->setEditable($canEdit)
                    ->setRequired(true)
                    ->setVariants(TariffTable::getEnumFieldHtmlValues('IS_DEFAULT')),

                (new \Local\Core\Inner\AdminHelper\EditField\Select($columnName["IS_ACTION"], "IS_ACTION", ($this->data['IS_ACTION'] ?? $columnDefaultValue['IS_ACTION'])))->setEditable($canEdit)
                    ->setRequired(true)
                    ->setVariants(TariffTable::getEnumFieldHtmlValues('IS_ACTION')),

                (new \Local\Core\Inner\AdminHelper\EditField\Select($columnName["TYPE"], "TYPE", ($this->data['TYPE'] ?? $columnDefaultValue['TYPE'])))->setEditable($canEdit)
                    ->setRequired(true)
                    ->setVariants(TariffTable::getEnumFieldHtmlValues('TYPE')),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['PERSONAL_BY_STORE'], 'PERSONAL_BY_STORE'))->setEditable($canEdit),
            ],

            'limit' => [

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['LIMIT_TRADING_PLATFORM'], 'LIMIT_TRADING_PLATFORM',
                    ($this->data['LIMIT_TRADING_PLATFORM'] ?? $columnDefaultValue['LIMIT_TRADING_PLATFORM'])))->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['LIMIT_IMPORT_PRODUCTS'], 'LIMIT_IMPORT_PRODUCTS',
                    ($this->data['LIMIT_IMPORT_PRODUCTS'] ?? $columnDefaultValue['LIMIT_IMPORT_PRODUCTS'])))->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['PRICE_PER_TRADING_PLATFORM'], 'PRICE_PER_TRADING_PLATFORM',
                    ($this->data['PRICE_PER_TRADING_PLATFORM'] ?? $columnDefaultValue['PRICE_PER_TRADING_PLATFORM'])))->setEditable($canEdit)
                    ->setRequired(true),
            ],

            'active_dates' => [
                (new \Local\Core\Inner\AdminHelper\EditField\Date($columnName["DATE_ACTIVE_FROM"], "DATE_ACTIVE_FROM"))->setEditable($canEdit),

                (new \Local\Core\Inner\AdminHelper\EditField\Date($columnName["DATE_ACTIVE_TO"], "DATE_ACTIVE_TO"))->setEditable($canEdit),

                (new \Local\Core\Inner\AdminHelper\EditField\Select($columnName["SWITCH_AFTER_ACTIVE_TO"], "SWITCH_AFTER_ACTIVE_TO"))->setEditable($canEdit)
                    ->setVariants($arTariffList),
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
            'SORT' => $request->getPost("SORT") ?? 50,
            'ACTIVE' => $request->getPost("ACTIVE") ?? "N",
            'DATE_ACTIVE_FROM' => (!empty(trim($request->getPost('DATE_ACTIVE_FROM'))) ? new \Bitrix\Main\Type\DateTime(trim($request->getPost('DATE_ACTIVE_FROM')), 'd.m.Y H:i:s') : ''),
            'DATE_ACTIVE_TO' => (!empty(trim($request->getPost('DATE_ACTIVE_TO'))) ? new \Bitrix\Main\Type\DateTime(trim($request->getPost('DATE_ACTIVE_TO')), 'd.m.Y H:i:s') : ''),
            'NAME' => trim($request->getPost('NAME')),
            'LIMIT_TRADING_PLATFORM' => trim($request->getPost('LIMIT_TRADING_PLATFORM')),
            'LIMIT_IMPORT_PRODUCTS' => trim($request->getPost('LIMIT_IMPORT_PRODUCTS')),
            'PRICE_PER_TRADING_PLATFORM' => trim($request->getPost('PRICE_PER_TRADING_PLATFORM')),
            'IS_DEFAULT' => trim($request->getPost('IS_DEFAULT')) ?? 'N',
            'IS_ACTION' => trim($request->getPost('IS_ACTION')) ?? 'N',
            'TYPE' => trim($request->getPost('TYPE')),
            'PERSONAL_BY_STORE' => trim($request->getPost('PERSONAL_BY_STORE')),
            'SWITCH_AFTER_ACTIVE_TO' => trim($request->getPost('SWITCH_AFTER_ACTIVE_TO')),
        ];

        if ($this->id < 1) {
            $arFields['CODE'] = trim($request->getPost("CODE"));
        }

        if ((int)$id > 0) {

            $rightEdit = $this->checkRights("can_edit");
            if ($rightEdit->isSuccess()) {
                try {
                    $res = \Local\Core\Model\Data\TariffTable::update($id, $arFields);
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
                    $res = \Local\Core\Model\Data\TariffTable::add($arFields);
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
                    $res = \Local\Core\Model\Data\TariffTable::delete((int)$this->id);
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
