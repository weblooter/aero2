<?php

namespace Local\Core\Inner\AdminHelper\Data\Store;

use Local\Core\Model\Data\StoreTable;
use Local\Core\Model\Data\StoreTariffChangeLogTable;
use Local\Core\Model\Data\TariffTable;
use Local\Core\Model\Robofeed\ImportLogTable;

/**
 * Class AdminList
 * @package Local\Core\Inner\AdminHelper\Data\Company
 */
class AdminEdit extends \Local\Core\Inner\AdminHelper\EditBase
{

    const ADMIN_ENTITY_VALUE = 'model_data_store';
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

                $body = \Local\Core\Model\Data\StoreTable::getList([
                        "select" => [
                            "*",
                            'COMPANY_DATA_' => 'COMPANY',
                            'FILE_DATA_' => 'B_FILE',
                            'TARIFF_DATA_' => 'TARIFF'
                        ],
                        "filter" => [
                            "=ID" => (int)$request->get("id")
                        ],
                        "limit" => 1,
                        'runtime' => [
                            new \Bitrix\Main\ORM\Fields\Relations\Reference('B_FILE', \Bitrix\Main\FileTable::class, \Bitrix\Main\ORM\Query\Join::on('this.FILE_ID', 'ref.ID'))
                        ]
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
            $this->app->SetTitle("Редактирование магазина");
        } else {
            $rightAdd = $this->checkRights("can_add");
            if ($rightAdd->isSuccess()) {
                $this->app->SetTitle("Создание магазина");
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
                    "TITLE" => "Добавить магазин",
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
                    "TITLE" => "Удалить магазин",
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
                "DIV" => "last_import",
                "TAB" => "Данные по импорту",
                "TITLE" => "Данные по импорту"
            ],
            [
                "DIV" => "import_logs",
                "TAB" => "Логи импортов",
                "TITLE" => "Логи импортов"
            ],
            [
                "DIV" => "tariff_logs",
                "TAB" => "Логи тарифов",
                "TITLE" => "Логи тарифов"
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
        $map = \Local\Core\Model\Data\StoreTable::getMap();
        foreach ($map as $column) {
            if ($column instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                $columnName[$column->getColumnName()] = $column->getTitle();
            }
        }

        $strHtmlFileValue = '<a href="/upload'.$this->data['FILE_DATA_SUBDIR'].$this->data['FILE_DATA_FILE_NAME'].'" target="_blank" >['.$this->data['FILE_ID'].'] /upload'
                            .$this->data['FILE_DATA_SUBDIR'].$this->data['FILE_DATA_FILE_NAME'].'</a>';

        $strImportLogs = '';
        $rsImportLogs = ImportLogTable::getList([
            'filter' => ['STORE_ID' => $this->id],
            'order' => ['ID' => 'DESC']
        ]);
        while ($ar = $rsImportLogs->fetch()) {
            $tt = '<b>Дата создания:</b><br/>'.$ar['DATE_CREATE']->format('Y-m-d H:i:s').'<br/>';
            $tt .= '<br/><b>Дата изменения:</b><br/>'.$ar['DATE_MODIFIED']->format('Y-m-d H:i:s').'<br/>';
            $tt .= '<br/><b>Результат импорта:</b><br/>'.ImportLogTable::getEnumFieldHtmlValues('IMPORT_RESULT')[$ar['IMPORT_RESULT']].'<br/>';
            $tt .= '<br/><b>Поведение импорта при ошибке:</b><br/>'.ImportLogTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[$ar['BEHAVIOR_IMPORT_ERROR']].'<br/>';
            $tt .= '<br/><b>Информировать о не изменившемся Robofeed XML?:</b><br/>'.ImportLogTable::getEnumFieldHtmlValues('ALERT_IF_XML_NOT_MODIFIED')[$ar['ALERT_IF_XML_NOT_MODIFIED']].'<br/>';
            $tt .= '<br/><b>Кол-во товаров в Robofeed XML:</b><br/>'.$ar['PRODUCT_TOTAL_COUNT'].'<br/>';
            $tt .= '<br/><b>Кол-во валидных товаров в Robofeed XML:</b><br/>'.$ar['PRODUCT_SUCCESS_IMPORT'].'<br/>';
            if (!empty($ar['ERROR_TEXT'])) {
                $tt .= '<br/><b>Ошибка</b><br/>'.$ar['ERROR_TEXT'].'<br/>';
            }

            $strImportLogs .= $tt.'<hr/>';
        }

        $arTariffes = [];
        $rsTariffes = TariffTable::getList([]);
        while ($ar = $rsTariffes->fetch()) {
            $arTariffes[$ar['CODE']] = $ar;
        }

        $strTariffLog = '';
        $rsTariffLog = StoreTariffChangeLogTable::getList([
            'filter' => [
                'STORE_ID' => $this->id
            ],
            'order' => [
                'ID' => 'DESC'
            ]
        ]);
        while ($ar = $rsTariffLog->fetch()) {
            $tariffLink = (new \Local\Core\Inner\AdminHelper\Data\Tariff\AdminList())->getEditLink($arTariffes[$ar['TARIFF_CODE']]);

            $tt = '<b>Дата создания:</b><br/>'.$ar['DATE_CREATE']->format('Y-m-d H:i:s').'<br/>';
            $tt .= '<b>Тариф:</b><br/><a href="'.$tariffLink.'" target="_blank">'.$arTariffes[$ar['TARIFF_CODE']]['NAME'].'</a> ['.$arTariffes[$ar['TARIFF_CODE']]['CODE'].']<br/>';
            $strTariffLog .= $tt.'<hr/>';
        }

        $strCurrentTariff = '<a href="'.((new \Local\Core\Inner\AdminHelper\Data\Tariff\AdminList())->getEditLink($arTariffes[$this->data['TARIFF_CODE']])).'" target="_blank">'
                            .$arTariffes[$this->data['TARIFF_CODE']]['NAME'].'</a> ['.$arTariffes[$this->data['TARIFF_CODE']]['CODE'].']';

        return [
            "main" => [
                ((int)$this->id > 0) ? new \Local\Core\Inner\AdminHelper\EditField\Html($columnName["ID"], "ID", $this->id) : null,
                ((int)$this->id > 0) ? new \Local\Core\Inner\AdminHelper\EditField\Hidden("", "ID", $this->id) : null,

                (new \Local\Core\Inner\AdminHelper\EditField\Checkbox($columnName["ACTIVE"], "ACTIVE"))->setEditable($canEdit),

                (new \Local\Core\Inner\AdminHelper\EditField\Date($columnName["DATE_CREATE"], "DATE_CREATE"))->setEditable(false),

                (new \Local\Core\Inner\AdminHelper\EditField\Date($columnName["DATE_MODIFIED"], "DATE_MODIFIED"))->setEditable(false),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['COMPANY_ID'], 'COMPANY_ID'))->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['NAME'], 'NAME'))->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['DOMAIN'], 'DOMAIN'))->setEditable($canEdit),

                (new \Local\Core\Inner\AdminHelper\EditField\Select($columnName["RESOURCE_TYPE"], "RESOURCE_TYPE"))->setEditable($canEdit)
                    ->setRequired(true)
                    ->setVariants(StoreTable::getEnumFieldHtmlValues('RESOURCE_TYPE')),

                (new \Local\Core\Inner\AdminHelper\EditField\SimpleText($columnName['FILE_ID'], 'FILE_ID', $strHtmlFileValue))->setEditable(false),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['FILE_LINK'], 'FILE_LINK'))->setEditable($canEdit),

                (new \Local\Core\Inner\AdminHelper\EditField\Checkbox($columnName['HTTP_AUTH'], 'HTTP_AUTH'))->setEditable($canEdit),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['HTTP_AUTH_LOGIN'], 'HTTP_AUTH_LOGIN'))->setEditable($canEdit),

                (new \Local\Core\Inner\AdminHelper\EditField\Text($columnName['HTTP_AUTH_PASS'], 'HTTP_AUTH_PASS'))->setEditable($canEdit),

                (new \Local\Core\Inner\AdminHelper\EditField\Select($columnName['BEHAVIOR_IMPORT_ERROR'],
                    'BEHAVIOR_IMPORT_ERROR'))->setVariants(StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

                (new \Local\Core\Inner\AdminHelper\EditField\Select($columnName['ALERT_IF_XML_NOT_MODIFIED'],
                    'ALERT_IF_XML_NOT_MODIFIED'))->setVariants(StoreTable::getEnumFieldHtmlValues('ALERT_IF_XML_NOT_MODIFIED'))
                    ->setEditable($canEdit)
                    ->setRequired(true),

            ],

            'last_import' => [
                new \Local\Core\Inner\AdminHelper\EditField\Html($columnName["DATE_LAST_IMPORT"], "DATE_LAST_IMPORT"),
                new \Local\Core\Inner\AdminHelper\EditField\Html($columnName["LAST_IMPORT_RESULT"], "LAST_IMPORT_RESULT",
                    StoreTable::getEnumFieldHtmlValues('LAST_IMPORT_RESULT')[$this->data['LAST_IMPORT_RESULT']]),
                new \Local\Core\Inner\AdminHelper\EditField\Html($columnName["DATE_LAST_SUCCESS_IMPORT"], "DATE_LAST_SUCCESS_IMPORT"),
                new \Local\Core\Inner\AdminHelper\EditField\Html($columnName["PRODUCT_TOTAL_COUNT"], "PRODUCT_TOTAL_COUNT"),
                new \Local\Core\Inner\AdminHelper\EditField\Html($columnName["PRODUCT_SUCCESS_IMPORT"], "PRODUCT_SUCCESS_IMPORT"),
            ],

            'import_logs' => [
                new \Local\Core\Inner\AdminHelper\EditField\Html('Логи последних импортов', "", $strImportLogs),
            ],

            'tariff_logs' => [
                new \Local\Core\Inner\AdminHelper\EditField\SimpleText('Текущий тариф', "", $strCurrentTariff),
                new \Local\Core\Inner\AdminHelper\EditField\SimpleText('Логи тарифов', "", $strTariffLog),
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
            'COMPANY_ID' => trim($request->getPost('COMPANY_ID')),
            'DOMAIN' => trim($request->getPost('DOMAIN')),
            'NAME' => trim($request->getPost('NAME')),
            'RESOURCE_TYPE' => $request->getPost("RESOURCE_TYPE"),
            'FILE_LINK' => trim($request->getPost('FILE_LINK')),
            'HTTP_AUTH' => trim($request->getPost('HTTP_AUTH')) ?? 'N',
            'HTTP_AUTH_LOGIN' => trim($request->getPost('HTTP_AUTH_LOGIN')),
            'HTTP_AUTH_PASS' => trim($request->getPost('HTTP_AUTH_PASS')),
            'BEHAVIOR_IMPORT_ERROR' => trim($request->getPost('BEHAVIOR_IMPORT_ERROR')),
        ];

        if ((int)$id > 0) {

            $rightEdit = $this->checkRights("can_edit");
            if ($rightEdit->isSuccess()) {
                try {
                    $res = \Local\Core\Model\Data\StoreTable::update($id, $arFields);
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
                    $res = \Local\Core\Model\Data\StoreTable::add($arFields);
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
                    $res = \Local\Core\Model\Data\StoreTable::delete((int)$this->id);
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
