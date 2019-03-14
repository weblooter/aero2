<?php

namespace Local\Core\Inner\AdminHelper\Reference\Measure;

use Local\Core\Model\Reference\MeasureTable;

/**
 * Class AdminList
 * @package Local\Core\Inner\AdminHelper\Data\Company
 */
class AdminEdit extends \Local\Core\Inner\AdminHelper\EditBase
{

    const ADMIN_ENTITY_VALUE = 'model_reference_measure';
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
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();

        if( $request->get("id") !== null )
        {
            try
            {

                $body = \Local\Core\Model\Reference\MeasureTable::getList(
                    [
                        "select" => [
                            "*"
                        ],
                        "filter" => [
                            "=ID" => (int)$request->get("id")
                        ],
                        "limit"  => 1
                    ]
                )->fetch();

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
            $this->app->SetTitle("Редактирование единицы");
        }
        else
        {
            $rightAdd = $this->checkRights("can_add");
            if( $rightAdd->isSuccess() )
            {
                $this->app->SetTitle("Создание единицы");
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
                "TEXT"  => "Вернуться к списку ",
                "LINK"  => $this->getListLink(),
                "TITLE" => "Вернуться к списку ",
                "ICON"  => "btn_list",
            ]
        ];

        if( (int)$this->id > 0 )
        {

            if(
            $this->checkRights("can_add")->isSuccess()
            )
            {
                $buttons[] = [
                    "TEXT"  => "Добавить",
                    "LINK"  => \Local\Core\Inner\AdminHelper\AdminRoute::getUri(
                        [
                            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
                            \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => self::ADMIN_ACTION_VALUE,
                        ]
                    ),
                    "TITLE" => "Добавить единицу",
                    "ICON"  => "btn_new",
                ];
            }

            if(
            $this->checkRights("can_delete")->isSuccess()
            )
            {
                $buttons[] = [
                    "TEXT"  => "Удалить",
                    "LINK"  => "javascript:if(confirm('Действительно удалить?'))window.location='".$this->getEditLink(
                            ["id" => $this->id]
                        )."&action=delete&".bitrix_sessid_get()."';",
                    "TITLE" => "Удалить единицу",
                    "ICON"  => "btn_delete",
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
                "DIV"   => "main",
                "TAB"   => "Основное",
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
                       && $this->checkRights("can_edit")->isSuccess() )
                     || ( (int)$this->id == 0
                          && $this->checkRights(
                    "can_add"
                )->isSuccess() ) ) ? true : false;

        $columnName = [];
        $map = \Local\Core\Model\Reference\MeasureTable::getMap();
        foreach( $map as $column )
        {
            if( $column instanceof \Bitrix\Main\ORM\Fields\ScalarField )
            {
                $columnName[$column->getColumnName()] = $column->getTitle();
            }
        }

        return [
            "main" => [
                ( (int)$this->id > 0 ) ? new \Local\Core\Inner\AdminHelper\EditField\Html(
                    $columnName["ID"], "ID", $this->id
                ) : null,
                ( (int)$this->id > 0 ) ? new \Local\Core\Inner\AdminHelper\EditField\Hidden(
                    "", "ID", $this->id
                ) : null,

                ( new \Local\Core\Inner\AdminHelper\EditField\Date(
                    $columnName["DATE_CREATE"], "DATE_CREATE"
                ) )->setEditable(false),

                ( new \Local\Core\Inner\AdminHelper\EditField\Date(
                    $columnName["DATE_MODIFIED"], "DATE_MODIFIED"
                ) )->setEditable(false),

                ( new \Local\Core\Inner\AdminHelper\EditField\Select(
                    $columnName['GROUP'], 'GROUP'
                ) )
                    ->setVariants(MeasureTable::getEnumFieldHtmlValues('GROUP'))
                    ->setEditable(
                    $canEdit
                )->setRequired(true),

                ( new \Local\Core\Inner\AdminHelper\EditField\Text(
                    $columnName['SORT'], 'SORT'
                ) )->setEditable(
                    $canEdit
                )->setRequired(false),

                ( new \Local\Core\Inner\AdminHelper\EditField\Text(
                    $columnName['NAME'], 'NAME'
                ) )->setEditable(
                    $canEdit
                )->setRequired(true),

                ( new \Local\Core\Inner\AdminHelper\EditField\Text(
                    $columnName['CODE'], 'CODE'
                ) )->setEditable(
                    $canEdit
                )->setRequired(true),
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
            'NAME' => trim($request->getPost('NAME')),
            'CODE' => trim($request->getPost('CODE')),
            'GROUP' => trim($request->getPost('GROUP')),
            'SORT' => trim($request->getPost('SORT')),
        ];

        if( (int)$id > 0 )
        {

            $rightEdit = $this->checkRights("can_edit");
            if( $rightEdit->isSuccess() )
            {
                try
                {
                    $res = \Local\Core\Model\Reference\MeasureTable::update(
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
                    $res = \Local\Core\Model\Reference\MeasureTable::add($arFields);
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
                    $res = \Local\Core\Model\Reference\MeasureTable::delete((int)$this->id);
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
                "id"                                                   => $this->id,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
//    protected function getEditAndNewLink($fields = [])
//    {
//        return \Local\Core\Inner\AdminHelper\AdminRoute::getUri(
//            [
//                \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ENTITY => self::ADMIN_ENTITY_VALUE,
//                \Local\Core\Inner\AdminHelper\AdminRoute::ADMIN_ACTION => self::ADMIN_ACTION_VALUE,
//            ]
//        );
//    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        parent::render();
    }
}
