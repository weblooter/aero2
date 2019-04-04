<?php

namespace Local\Core\Inner\AdminHelper\Data\AttemptsTopUpBalanceLog;

use Local\Core\Model\Data\AttemptsTopUpBalanceLogTable;
use Local\Core\Model\Data\StoreTariffChangeLogTable;
use Local\Core\Model\Data\TariffTable;
use Local\Core\Model\Robofeed\ImportLogTable;

/**
 * Class AdminList
 * @package Local\Core\Inner\AdminHelper\Data\Company
 */
class AdminEdit extends \Local\Core\Inner\AdminHelper\EditBase
{

    const ADMIN_ENTITY_VALUE = 'model_data_attempts_top_up_balance_log';
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

                $body = \Local\Core\Model\Data\AttemptsTopUpBalanceLogTable::getList([
                        "select" => [
                            "*",
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
            $this->app->SetTitle("Редактирование лога попытки");
        } else {
            $rightAdd = $this->checkRights("can_add");
            if ($rightAdd->isSuccess()) {
                $this->app->SetTitle("Создание лога попытки");
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

            /*
            if (
            $this->checkRights("can_delete")
                ->isSuccess()
            ) {
                $buttons[] = [
                    "TEXT" => "Удалить",
                    "LINK" => "javascript:if(confirm('Действительно удалить?'))window.location='".$this->getEditLink(["id" => $this->id])."&action=delete&".bitrix_sessid_get()."';",
                    "TITLE" => "Удалить магазин",
                    "ICON" => "btn_delete",
                ];
            }
            */

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
            ]
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
        $map = \Local\Core\Model\Data\AttemptsTopUpBalanceLogTable::getMap();
        foreach ($map as $column) {
            if ($column instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                $columnName[$column->getColumnName()] = $column->getTitle();
            }
        }

        $strQueryData = '<pre>'.print_r((array)json_decode($this->data['QUERY_DATA']), true).'</pre>';

        $strAdditionalData = \Local\Core\Inner\Payment\Factory::factory($this->data['HANDLER'])::getAdditionalDataInAdmin($this->data['ADDITIONAL_DATA']);

        return [
            "main" => [
                ((int)$this->id > 0) ? new \Local\Core\Inner\AdminHelper\EditField\Html($columnName["ID"], "ID", $this->id) : null,
                ((int)$this->id > 0) ? new \Local\Core\Inner\AdminHelper\EditField\Hidden("", "ID", $this->id) : null,

                (new \Local\Core\Inner\AdminHelper\EditField\Date($columnName["DATE_CREATE"], "DATE_CREATE"))->setEditable(false),

                (new \Local\Core\Inner\AdminHelper\EditField\Date($columnName["DATE_MODIFIED"], "DATE_MODIFIED"))->setEditable(false),

                (new \Local\Core\Inner\AdminHelper\EditField\SimpleText($columnName['USER_ID'], 'USER_ID')),

                (new \Local\Core\Inner\AdminHelper\EditField\SimpleText($columnName['HANDLER'], 'HANDLER', '['.$this->data['HANDLER'].'] '.\Local\Core\Inner\Payment\Factory::factory($this->data['HANDLER'])::getTitle())),

                (new \Local\Core\Inner\AdminHelper\EditField\SimpleText($columnName['QUERY_DATA'], 'QUERY_DATA', $strQueryData)),
                (new \Local\Core\Inner\AdminHelper\EditField\SimpleText($columnName['ADDITIONAL_DATA'], 'ADDITIONAL_DATA', $strAdditionalData)),

                (new \Local\Core\Inner\AdminHelper\EditField\SimpleText($columnName["QUERY_CHECK_RESULT"], "QUERY_CHECK_RESULT", AttemptsTopUpBalanceLogTable::getEnumFieldHtmlValues('QUERY_CHECK_RESULT')[ $this->data['QUERY_CHECK_RESULT'] ])),

                (new \Local\Core\Inner\AdminHelper\EditField\SimpleText($columnName['QUERY_CHECK_ERROR_TEXT'], '', $this->data['QUERY_CHECK_ERROR_TEXT'])),

                (new \Local\Core\Inner\AdminHelper\EditField\SimpleText($columnName["TRY_TOP_UP_BALANCE_RESULT"], "TRY_TOP_UP_BALANCE_RESULT", AttemptsTopUpBalanceLogTable::getEnumFieldHtmlValues('TRY_TOP_UP_BALANCE_RESULT')[ $this->data['TRY_TOP_UP_BALANCE_RESULT'] ])),

                (new \Local\Core\Inner\AdminHelper\EditField\SimpleText($columnName['TRY_TOP_UP_BALANCE_ERROR_TEXT'], '', $this->data['TRY_TOP_UP_BALANCE_ERROR_TEXT'])),

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
        ];

        /*
        if ((int)$id > 0) {

            $rightEdit = $this->checkRights("can_edit");
            if ($rightEdit->isSuccess()) {
                try {
                    $res = \Local\Core\Model\Data\AttemptsTopUpBalanceLogTable::update($id, $arFields);
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
                    $res = \Local\Core\Model\Data\AttemptsTopUpBalanceLogTable::add($arFields);
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
        */

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function deleteAction(\Bitrix\Main\HttpRequest $request)
    {
        $result = new \Bitrix\Main\Result();

        /*
        if ($this->id) {
            $rightDelete = $this->checkRights("can_delete");

            if ($rightDelete->isSuccess()) {
                try {
                    $res = \Local\Core\Model\Data\AttemptsTopUpBalanceLogTable::delete((int)$this->id);
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
        */

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
