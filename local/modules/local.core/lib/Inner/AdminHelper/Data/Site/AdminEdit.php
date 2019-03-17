<?php

namespace Local\Core\Inner\AdminHelper\Data\Site;

use Local\Core\Model\Data\SiteTable;

/**
 * Class AdminList
 * @package Local\Core\Inner\AdminHelper\Data\Company
 */
class AdminEdit extends \Local\Core\Inner\AdminHelper\EditBase
{

    const ADMIN_ENTITY_VALUE = 'model_data_site';
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
        if( $check->isSuccess() )
        {
            $result->setData(
                [
                    'uri' => \Local\Core\Inner\AdminHelper\AdminRoute::getUri(
                        [
                            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
                            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => self::ADMIN_ACTION_VALUE,
                        ]
                    ),
                ]
            );
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
            $result->addError(new \Bitrix\Main\Error('Необходим доступ администратора'));
        }

        switch( $operation )
        {

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

        if( $request->get("id") !== null )
        {
            try
            {

                $body = \Local\Core\Model\Data\SiteTable::getList(
                    [
                        "select" => [
                            "*",
                            'COMPANY_DATA_' => 'COMPANY',
                            'FILE_DATA_' => 'B_FILE'
                        ],
                        "filter" => [
                            "=ID" => (int)$request->get("id")
                        ],
                        "limit" => 1,
                        'runtime' => [
                            new \Bitrix\Main\ORM\Fields\Relations\Reference(
                                'B_FILE', \Bitrix\Main\FileTable::class, \Bitrix\Main\ORM\Query\Join::on(
                                'this.FILE_ID',
                                'ref.ID'
                            )
                            )
                        ]
                    ]
                )
                    ->fetch();

            }
            catch( \Exception $e )
            {
                $result->addError(new \Bitrix\Main\Error($e->getMessage()));

                return $result;
            }
        }

        if( !empty($body) )
        {
            $this->id = $body["ID"];
            $this->data = $body;
            $this->app->SetTitle("Редактирование сайта");
        }
        else
        {
            $rightAdd = $this->checkRights("can_add");
            if( $rightAdd->isSuccess() )
            {
                $this->app->SetTitle("Создание сайта");
            }
            else
            {
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

        if( (int)$this->id > 0 )
        {

            if(
            $this->checkRights("can_add")
                ->isSuccess()
            )
            {
                $buttons[] = [
                    "TEXT" => "Добавить",
                    "LINK" => \Local\Core\Inner\AdminHelper\AdminRoute::getUri(
                        [
                            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
                            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => self::ADMIN_ACTION_VALUE,
                        ]
                    ),
                    "TITLE" => "Добавить сайт",
                    "ICON" => "btn_new",
                ];
            }

            if(
            $this->checkRights("can_delete")
                ->isSuccess()
            )
            {
                $buttons[] = [
                    "TEXT" => "Удалить",
                    "LINK" => "javascript:if(confirm('Действительно удалить?'))window.location='".$this->getEditLink(
                            ["id" => $this->id]
                        )."&action=delete&".bitrix_sessid_get()."';",
                    "TITLE" => "Удалить сайт",
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTabsContent()
    {

        $canEdit = ( ( (int)$this->id > 0
                       && $this->checkRights("can_edit")
                           ->isSuccess() )
                     || ( (int)$this->id == 0
                          && $this->checkRights(
                    "can_add"
                )
                              ->isSuccess() ) ) ? true : false;

        $columnName = [];
        $map = \Local\Core\Model\Data\SiteTable::getMap();
        foreach( $map as $column )
        {
            if( $column instanceof \Bitrix\Main\ORM\Fields\ScalarField )
            {
                $columnName[$column->getColumnName()] = $column->getTitle();
            }
        }

        $strHtmlFileValue = '<a href="'.$this->data['FILE_DATA_SUBDIR'].$this->data['FILE_DATA_FILE_NAME'].'" target="_blank" >['.$this->data['FILE_ID'].'] '.$this->data['FILE_DATA_SUBDIR']
                            .$this->data['FILE_DATA_FILE_NAME'].'</a>';

        return [
            "main" => [
                ( (int)$this->id > 0 ) ? new \Local\Core\Inner\AdminHelper\EditField\Html(
                    $columnName["ID"], "ID", $this->id
                ) : null,
                ( (int)$this->id > 0 ) ? new \Local\Core\Inner\AdminHelper\EditField\Hidden(
                    "", "ID", $this->id
                ) : null,

                ( new \Local\Core\Inner\AdminHelper\EditField\Checkbox(
                    $columnName["ACTIVE"], "ACTIVE"
                ) )->setEditable(
                    $canEdit
                ),

                ( new \Local\Core\Inner\AdminHelper\EditField\Date(
                    $columnName["DATE_CREATE"], "DATE_CREATE"
                ) )->setEditable(false),

                ( new \Local\Core\Inner\AdminHelper\EditField\Date(
                    $columnName["DATE_MODIFIED"], "DATE_MODIFIED"
                ) )->setEditable(false),

                ( new \Local\Core\Inner\AdminHelper\EditField\Text(
                    $columnName['COMPANY_ID'], 'COMPANY_ID'
                ) )->setEditable($canEdit)
                    ->setRequired(true),

                ( new \Local\Core\Inner\AdminHelper\EditField\Text(
                    $columnName['NAME'], 'NAME'
                ) )->setEditable(
                    $canEdit
                )
                    ->setRequired(true),

                ( new \Local\Core\Inner\AdminHelper\EditField\Text(
                    $columnName['DOMAIN'], 'DOMAIN'
                ) )->setEditable(
                    $canEdit
                )
                    ->setRequired(true),

                ( new \Local\Core\Inner\AdminHelper\EditField\Select(
                    $columnName["RESOURCE_TYPE"], "RESOURCE_TYPE"
                ) )->setEditable($canEdit)
                    ->setRequired(true)
                    ->setVariants(SiteTable::getEnumFieldHtmlValues('RESOURCE_TYPE')),

                ( new \Local\Core\Inner\AdminHelper\EditField\SimpleText(
                    $columnName['FILE_ID'], 'FILE_ID', $strHtmlFileValue
                ) )->setEditable(false)
                    ->setRequired(false),

                ( new \Local\Core\Inner\AdminHelper\EditField\Text(
                    $columnName['FILE_LINK'], 'FILE_LINK'
                ) )->setEditable($canEdit)
                    ->setRequired(false),

                ( new \Local\Core\Inner\AdminHelper\EditField\Checkbox(
                    $columnName['HTTP_AUTH'], 'HTTP_AUTH'
                ) )->setEditable($canEdit)
                    ->setRequired(false),

                ( new \Local\Core\Inner\AdminHelper\EditField\Text(
                    $columnName['HTTP_AUTH_LOGIN'], 'HTTP_AUTH_LOGIN'
                ) )->setEditable($canEdit)
                    ->setRequired(false),

                ( new \Local\Core\Inner\AdminHelper\EditField\Text(
                    $columnName['HTTP_AUTH_PASS'], 'HTTP_AUTH_PASS'
                ) )->setEditable($canEdit)
                    ->setRequired(false),
            ],
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
        ];

        if( (int)$id > 0 )
        {

            $rightEdit = $this->checkRights("can_edit");
            if( $rightEdit->isSuccess() )
            {
                try
                {
                    $res = \Local\Core\Model\Data\SiteTable::update(
                        $id,
                        $arFields
                    );
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
                $result->addErrors($rightEdit->getErrors());
            }
        }
        else
        {

            $rightAdd = $this->checkRights("can_add");
            if( $rightAdd->isSuccess() )
            {
                try
                {
                    $res = \Local\Core\Model\Data\SiteTable::add($arFields);
                    if( $res->isSuccess() )
                    {
                        $this->id = $res->getId();
                    }
                    else
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

        if( $this->id )
        {
            $rightDelete = $this->checkRights("can_delete");

            if( $rightDelete->isSuccess() )
            {
                try
                {
                    $res = \Local\Core\Model\Data\SiteTable::delete((int)$this->id);
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
        return \Local\Core\Inner\AdminHelper\AdminRoute::getUri(
            [
                \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => AdminList::ADMIN_ENTITY_VALUE,
                \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => AdminList::ADMIN_ACTION_VALUE,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditLink($fields = [])
    {
        return \Local\Core\Inner\AdminHelper\AdminRoute::getUri(
            [
                \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
                \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => self::ADMIN_ACTION_VALUE,
                "id" => $this->id,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        parent::render();
    }
}
