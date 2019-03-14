<?php

namespace Local\Core\Inner\AdminHelper;

/**
 * Class EditBase - Класс помощник построения страницы редактирования в админпанели
 * @package Local\Core\Inner\AdminHelper
 */
abstract class EditBase
{

    /**
     * @var $USER
     */
    protected $user = null;

    /**
     * @var $APPLICATION
     */
    protected $app = null;

    /**
     * @var \Bitrix\Main\Result
     */
    protected $resultAction = null;

    /**
     * Идентификатор элемента
     *
     * @var int
     */
    protected $id = null;

    /**
     * Данные элемента
     *
     * @var array|object
     */
    protected $data = [];

    /**
     * @var \CAdminTabControl
     */
    protected $CAdminTabControl = null;


    public function __construct()
    {
        $this->app = &$GLOBALS["APPLICATION"];
        $this->user = &$GLOBALS["USER"];
        $this->resultAction = new \Bitrix\Main\Result();
    }

    /**
     * Проверка прав пользователя
     *
     * @param $operation - выполняемая операция
     *
     * @return \Bitrix\Main\Result
     */
    abstract protected function checkRights($operation = ""): \Bitrix\Main\Result;

    /**
     * Устанавливает данные текущего элемента в this->id и this->data
     * Может вернуть ошибку, если полученные данные не валидны
     *
     * @return \Bitrix\Main\Result
     */
    abstract protected function setData();

    /**
     * Возвращает массив верхнего меню, над табами
     *
     * @return array
     *
     * @see https://dev.1c-bitrix.ru/api_help/main/general/admin.section/classes/cadmincontextmenu/cadmincontextmenu.php
     * @example
     *          <pre>
     *          [
     *              [
     *                  "TEXT"  => "Добавить",
     *                  "LINK"  => "",
     *                  "TITLE" => "Добавить элемент",
     *                  "ICON"  => "btn_new",
     *              ]
     *          ]
     *          </pre>
     */
    abstract protected function getUpperButtons();

    /**
     * Возвращает массив закладок
     *
     * @return array
     *
     * @see https://dev.1c-bitrix.ru/api_help/main/general/admin.section/classes/cadmintabcontrol/cadmintabcontrol.php
     * @example
     *          <pre>
     *          [
     *              [
     *                  "DIV"  => "Код",
     *                  "TAB"  => "Заголовок",
     *                  "ICON" => "Класс иконки",
     *                  "TITLE" => "Подсказка",
     *              ]
     *          ]
     *          </pre>
     */
    abstract protected function getTabsList();

    /**
     * Возвращает массив полей для закладки.
     * Ключ масива - это код закладки, установленной в getTabsList
     * Каждое поле это объект \Local\Core\Inner\AdminHelper\Field\Base
     *
     * @return array
     *
     * @example
     *          <pre>
     *          [
     *              "main" => [
     *                  new \Local\Core\Inner\AdminHelper\EditField\Html("ID", "ID", $this->id),
     *                  new \Local\Core\Inner\AdminHelper\EditField\Hidden("", "ID", $this->id),
     *                  new \Local\Core\Inner\AdminHelper\EditField\Checkbox("Активность", "ACTIVE"),
     *              ]
     *          ]
     *          </pre>
     */
    abstract protected function getTabsContent();

    /**
     * Возвращает путь до списка элементов
     *
     * @return string
     */
    abstract protected function getListLink();

    /**
     * Возвращает заметку внизу страницы
     *
     * @return string
     */
    abstract protected function getNote();

    /**
     * Сохранение элемента
     *
     * @param \Bitrix\Main\HttpRequest $request
     *
     * @return \Bitrix\Main\Result
     */
    abstract protected function editAction(\Bitrix\Main\HttpRequest $request);


    /**
     * Возвращает путь до редактирования элемента
     *
     * @param $fields - массив полей
     *
     * @return string
     */
    protected function getEditLink($fields = [])
    {
        $editLink = new \Bitrix\Main\Web\Uri($this->app->GetCurPage());
        $editLink->addParams(
            array_merge(
                [
                    "id"   => $this->id,
                    "lang" => LANGUAGE_ID,
                ],
                $fields
            )
        );

        return $editLink->getUri();
    }


    /**
     * Возвращает путь до создания нового элемента
     *
     * @param $fields - массив полей
     *
     * @return string
     */
    protected function getEditAndNewLink($fields = [])
    {
        $editLink = new \Bitrix\Main\Web\Uri($this->app->GetCurPage());
        $editLink->addParams(
            array_merge(
                [
                    "lang" => LANGUAGE_ID,
                ],
                [
                    'adminEntity' => \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('adminEntity'),
                    'adminAction' => \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('adminAction'),
                ],
                $fields
            )
        );

        return $editLink->getUri();
    }

    /**
     * Выполнение действий на странице
     */
    private function executeAction()
    {
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();

        if( ( !$request->isPost() && $request->get("action") === null ) || !check_bitrix_sessid() )
        {
            return;
        }

        if( ( $request->get("save") || $request->get("save_and_add") || $request->get("apply") ) && $this->checkRights("editAction") )
        {

            $result = $this->editAction($request);

            if( $result->isSuccess() )
            {
                if( $request->get("apply") !== null )
                {
                    LocalRedirect($this->getEditLink(["tabControl_active_tab" => $this->CAdminTabControl->GetSelectedTab()]));
                }
                elseif( $request->get("save") !== null )
                {
                    LocalRedirect($this->getListLink());
                }
                elseif( $request->get("save_and_add") !== null )
                {
                    LocalRedirect($this->getEditAndNewLink());
                }
            }
            else
            {
                $this->resultAction->addErrors($result->getErrors());
            }

        }
        else
        {
            if( $request->get("action") )
            {

                $methodName = $request->get("action")."Action";
                if(
                    $this->checkRights($methodName)
                    && method_exists(
                        $this,
                        $methodName
                    )
                )
                {

                    $resAction = call_user_func(
                        [$this, $methodName],
                        $request
                    );
                    if( $resAction->isSuccess() )
                    {
                        LocalRedirect($this->getListLink());
                    }
                    else
                    {
                        $this->resultAction->addErrors($resAction->getErrors());
                    }

                }

            }
        }
    }

    /**
     * Выводит страницу
     */
    public function render()
    {
        global $USER, $APPLICATION, $adminPage, $adminMenu, $adminChain, $SiteExpireDate;

        if( !$this->checkRights("render") )
        {
            $this->app->AuthForm("Недостаточно прав доступа");

            return;
        }

        require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );

        $setDataResult = $this->setData();
        if( !$setDataResult->isSuccess() )
        {
            echo ( new \CAdminMessage(
                implode(
                    "\n",
                    $setDataResult->getErrorMessages()
                )
            ) )->Show();
            require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php" );

            return;
        }

        ( new \CAdminContextMenu($this->getUpperButtons()) )->Show();
        $this->CAdminTabControl = new \CAdminTabControl(
            "tabControl", $this->getTabsList()
        );

        $this->executeAction();
        if( !$this->resultAction->isSuccess() )
        {
            echo ( new \CAdminMessage(
                implode(
                    "\n",
                    $this->resultAction->getErrorMessages()
                )
            ) )->Show();
        }

        ?>
        <form method="POST" action="<?=$this->getEditLink()?>" enctype="multipart/form-data" name="post_form" id="post_form"><?
        echo bitrix_sessid_post();
        $this->CAdminTabControl->Begin();

        $fields = $this->getTabsContent();
        if( !empty($fields) )
        {
            foreach( $fields as $tab )
            {
                $this->CAdminTabControl->BeginNextTab();

                /** @var $field \Local\Core\Inner\AdminHelper\EditField\Base */
                foreach( $tab as $field )
                {
                    if( $field instanceof \Local\Core\Inner\AdminHelper\EditField\Base )
                    {

                        $field->setElementData($this->data);
                        echo $field->getRowHtml();

                    }
                }
            }
        }

        $this->CAdminTabControl->Buttons(
            [
                "disabled"      => false,
                "back_url"      => $this->getListLink(),
                'btnSaveAndAdd' => true
            ]
        );
        $this->CAdminTabControl->End();

        if( !empty($this->getNote()) )
        {
            echo BeginNote();
            echo $this->getNote();
            echo EndNote();
        }

        require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php" );
    }

}
