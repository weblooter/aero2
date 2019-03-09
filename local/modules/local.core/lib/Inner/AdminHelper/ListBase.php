<?php

namespace Local\Core\Inner\AdminHelper;

/**
 * Class ListBase - Класс помощник построения интерфейса списка в админпанели
 * @package Local\Core\Inner\AdminHelper
 */
abstract class ListBase
{
    /**
     * @var \CUser
     */
    protected $user = null;

    /**
     * @var \CMain
     */
    protected $app = null;

    /**
     * @var \CAdminList
     */
    protected $CAdminList;

    /**
     * Используется для фильтрации getList
     *
     * @var array
     */
    protected $filterList = [];

    /**
     * Префикс ключей фильтра (поиска)
     *
     * @var string
     */
    protected $filterSearchPrefix = "find_";

    /**
     * Фильтр поиска
     *
     * @var array
     */
    protected $filterSearch = [];

    /**
     * Потенциально хранит продвинутую навигацию
     * @var \Bitrix\Main\UI\AdminPageNavigation|null
     */
    protected $nav;

    public function __construct()
    {
        $this->app = &$GLOBALS["APPLICATION"];
        $this->user = &$GLOBALS["USER"];
        if ($this->useAdvancedNavigation())
            $this->nav = new \Bitrix\Main\UI\AdminPageNavigation($this->getTableListId());
    }

    /**
     * Выводит страницу
     */
    public function render()
    {
        global $USER, $APPLICATION, $adminPage, $adminMenu, $adminChain, $SiteExpireDate;
        $result = $this->checkRights("render");
        if (!$result->isSuccess()) {
            $this->app->AuthForm(join('<br>', $result->getErrorMessages()));
            return;
        }

        $currentUri = $APPLICATION->GetCurPage();
        $requiredUri = $this->getSortUri();
        if ($currentUri <> $requiredUri) {
            \Closure::bind(
                function () use ($requiredUri) {
                    $this->sDocPath2 = $requiredUri;
                },
                $this->app,
                '\CMain'
            )();
        }

        $this->CAdminList = new \CAdminList(
            $this->getTableListId(),
            new \CAdminSorting($this->getTableListId(), $this->getIdentificationField(), "asc")
        );

        if ($currentUri <> $requiredUri) {
            \Closure::bind(
                function () use ($currentUri) {
                    $this->sDocPath2 = $currentUri;
                },
                $this->app,
                '\CMain'
            )();
        }


        $this->executeGroupAction();

        /**
         * Фильтрация
         */
        $filterKeys = [];
        foreach ($this->getFilterSearchFields() as $key => $array) {
            $filterKeys[] = $this->filterSearchPrefix . $key;

            if ($array["TYPE"] == "DATE_PERIOD") {
                $filterKeys[] = $this->filterSearchPrefix . $key . "_2";
            }
        }
        $this->CAdminList->InitFilter($filterKeys);
        $this->filterSearch = $this->CAdminList->getFilter();
        $clearFilter = [];
        foreach ($this->filterSearch as $key => $value) {
            $clearFilter[str_replace($this->filterSearchPrefix, "", $key)] = $value;
        }
        $this->filterList = $this->prepareFilterList($clearFilter);


        /**
         * Строки
         */
        $resultList = $this->prepareList();
        if (!empty($this->getGroupAction())) {
            $this->CAdminList->AddGroupActionTable($this->getGroupAction());
        }
        $this->CAdminList->AddAdminContextMenu($this->getUpperButtons());
        $this->CAdminList->CheckListMode();


        require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
        if ($resultList->isSuccess()) {
            $this->displayFilter();
            $this->CAdminList->DisplayList();
        } else {
            \CAdminMessage::ShowMessage(implode("\n", $resultList->getErrorMessages()));
        }
        require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
    }

    /**
     * Проверка прав пользователя
     *
     * @param string $operation - выполняемая операция
     *
     * @return \Bitrix\Main\Result
     */
    abstract protected function checkRights($operation = ""): \Bitrix\Main\Result;

    /**
     * Подменяет \CMain->sDocPath2 <br>
     * при создании экземляра класса \CAdminSorting
     * @return string
     */
    protected function getSortUri(): string
    {
        return $this->app->GetCurPage();
    }

    /**
     * Возвращает идентификатор списка
     *
     * @return string
     */
    abstract protected function getTableListId();

    /**
     * Возвращает код идентификатора элемента
     *
     * @return string
     */
    protected function getIdentificationField()
    {
        return "ID";
    }

    /**
     * Действия выполняемые при сохранении, удалении или других операциях
     */
    private function executeGroupAction()
    {
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        if (!empty($_REQUEST["action_button"])) {
            $_REQUEST["action"] = $_REQUEST["action_button"];
        }

        if (!$request->isPost() && !$_REQUEST["action"]) {
            return;
        }

        # Групповое редактирование
        if ($this->CAdminList->EditAction() && $this->checkRights("editAction")->isSuccess() && check_bitrix_sessid()) {
            $arRows = $request->get("FIELDS");
            if (!empty($arRows)) {

                foreach ($arRows as $id => $fields) {
                    if ($this->CAdminList->IsUpdated($id)) {
                        $resSave = $this->editAction($id, $fields);
                        if (!$resSave->isSuccess()) {
                            $this->CAdminList->AddUpdateError(implode("\n", $resSave->getErrorMessages()), $id);
                        }
                    }
                }
            }
        }

        # Групповое действие
        if (isset($_REQUEST["action"])) {
            $methodName = $_REQUEST["action"] . "Action";

            if ($this->CAdminList->GroupAction() && $this->checkRights($methodName)->isSuccess() && check_bitrix_sessid()) {
                $ids = $this->CAdminList->GroupAction();

                if (!empty($ids)) {

                    if (method_exists($this, $methodName)) {
                        foreach ($ids as $id) {
                            $resAction = call_user_func([$this, $methodName], $id);
                            if (!$resAction->isSuccess()) {
                                $this->CAdminList->AddGroupError(implode("\n", $resAction->getErrorMessages()), $id);
                            }
                        }
                    }
                }

            }
        }

    }

    /**
     * Выполняется при редактировании полей
     *
     * @param $id
     * @param array $fields
     *
     * @return \Bitrix\Main\Result
     */
    abstract protected function editAction($id, $fields);

    /**
     * Формирует список полей для фильтрации (поиск)<br>
     * Типы доступных полей описаны в методе {@see \Local\Core\Inner\AdminHelper\ListBase->displayFilter()}
     *
     * <code>
     * [
     *     "id" => [
     *         "NAME" => "ID",
     *         "TYPE" => "TEXT",
     *     ],
     *     "active" => [
     *         "NAME"     => "Активность",
     *         "TYPE"     => "SELECT",
     *         "VARIANTS" => [
     *             "Y" => "Да",
     *             "N" => "Нет",
     *         ]
     *     ],
     * ]
     * </code>
     * @return array
     */
    abstract protected function getFilterSearchFields();

    /**
     * Подготавливает $filterList для передачи в getList
     *
     * @param $filterSearch - массив значений фильтра поиска<br>
     *                      с ключами из getFilterSearchFields
     *
     * @return array
     */
    abstract protected function prepareFilterList($filterSearch);

    /**
     * Подготовка списка элементов
     *
     * @return \Bitrix\Main\Result
     */
    private function prepareList()
    {
        $result = new \Bitrix\Main\Result();

        try {
            $listQuery = $this->getList();
        } catch (\Exception $e) {
            $result->addError(new \Bitrix\Main\Error($e->getMessage()));

            return $result;
        }

        if ($this->useAdvancedNavigation()) {
            $this->nav->setRecordCount($listQuery->getCount());
            $this->CAdminList->setNavigation($this->nav, 'Записей');
        } else {
            # Данные и навигация
            $dbResultList = new \CAdminResult($listQuery, $this->getTableListId());
            $dbResultList->NavStart(20, true);
            $this->CAdminList->NavText($dbResultList->GetNavPrint("Страница"));
        }

        # Заголовки
        $this->CAdminList->AddHeaders($this->getHeaders());

        # Строки
        while ($fields = !$this->useAdvancedNavigation()
            ? $dbResultList->NavNext(true, "f_")
            : $listQuery->fetch()
        ) {
            $row = &$this->CAdminList->AddRow($fields[$this->getIdentificationField()], $fields,
                $this->getEditLink($fields));

            # Подготовка полей
            $this->prepareRowField($row, $fields);

            # По умолчанию поля
            $visibleColumns = $this->CAdminList->GetVisibleHeaderColumns();
            foreach ($this->getHeaders() as $item) {
                if (in_array($item['id'], $visibleColumns)
                    && is_array($row->$fields[$item['id']])
                ) {
                    $row->AddViewField($item['id'],
                        $fields[$item['id']]);
                }
            }
            # Действия
            $row->AddActions($this->getRowAction($fields));
        }
        $this->finalizeAdminrow($this->CAdminList->aRows);
//        p($this->CAdminList->aRows);
        return $result;
    }


    /**
     * Возвращает результат выборки элементов
     *
     * @return \Bitrix\Main\ORM\Query\Result
     * @throws \Exception
     */
    abstract protected function getList();

    /**
     * Возвращает заголовки списка
     * <code>
     * [
     *     [
     *         "id"      => "ID",
     *         "content" => "ID",
     *         "sort"    => "ID",
     *         "default" => true
     *     ],
     * ]
     * </code>
     * @see https://dev.1c-bitrix.ru/api_help/main/general/admin.section/classes/cadminlist/addheaders.php
     * @return array
     */
    abstract protected function getHeaders();

    /**
     * Возвращает путь до редактирования элемента
     *
     * @param $fields - массив полей
     *
     * @return null|string
     */
    protected function getEditLink($fields = [])
    {
        return null;
    }

    /**
     * Видоизменяет поля строк
     *
     * @param \CAdminListRow $row
     * @param array $fields - поля строк
     *
     * @see https://dev.1c-bitrix.ru/api_help/main/general/admin.section/classes/cadminlistrow/index.php
     */
    abstract protected function prepareRowField(\CAdminListRow $row, $fields);

    /**
     * Возвращает список действий для элемента
     * <code>
     * [
     *     [
     *         "ICON"   => "edit",
     *         "TEXT"   => "Редактировать",
     *         "ACTION" => $this->CAdminList->ActionRedirect("ссылка"),
     *     ],
     *     [
     *         "ICON"   => "delete",
     *         "TEXT"   => "Удалить",
     *         "ACTION" => "if(confirm('Действительно удалить?')) " . $this->CAdminList->ActionDoGroup($fields["ID"], "delete")
     *     ],
     * ]
     * </code>
     *
     * @param array $fields
     * @see https://dev.1c-bitrix.ru/api_help/main/general/admin.section/classes/cadminlistrow/addactions.php
     * @return array
     */
    abstract protected function getRowAction($fields);

    /**
     * Возвращает варианты групповых изменений<br>
     * Для каждого ключа, кроме edit, необходимо создать свой метод обработчик<br>
     * Название метода - действиеAction($id). Метод должен возвращать \Bitrix\Main\Result<br>
     * <code>
     * [
     *     "delete" => "Удалить",
     *     "edit"   => "Редактировать",
     * ]
     * </code>
     * @see https://dev.1c-bitrix.ru/api_help/main/general/admin.section/classes/cadminlist/addgroupactiontable.php
     * @return array
     */
    abstract protected function getGroupAction();

    /**
     * Возвращает массив верхнего меню, над списком
     *
     * <code>
     * [
     *     [
     *         "TEXT"  => "Добавить",
     *         "LINK"  => "",
     *         "TITLE" => "Добавить элемент",
     *         "ICON"  => "btn_new",
     *     ],
     * ]
     * </code>
     * @see https://dev.1c-bitrix.ru/api_help/main/general/admin.section/classes/cadmincontextmenu/cadmincontextmenu.php
     * @return array
     */
    abstract protected function getUpperButtons();

    /**
     * Отображает html форму фильтрации
     */
    private function displayFilter()
    {
        $filterFields = $this->getFilterSearchFields();

        #Список доступных полей фильтрации
        $popupList = [];
        foreach ($filterFields as $key => $field) {
            $popupList[$key] = $field["NAME"];
        }
        $oFilter = new \CAdminFilter($this->getTableListId() . "_filter", $popupList);
        ?>
        <form name="find_form" id="find_form" method="get" action="<?= $this->getFilterUri(); ?>"><?
        $oFilter->Begin();

        foreach ($filterFields as $key => $field) {
            ?>
            <tr>
                <td><?= $field["NAME"] ?>:</td>
                <td>
                    <?

                    $name = $this->filterSearchPrefix . $key;
                    $value = $this->filterSearch[$name];
                    $variants = (isset($field["VARIANTS"])) ? [
                        "reference" => array_values($field["VARIANTS"]),
                        "reference_id" => array_keys($field["VARIANTS"]),
                    ] : [];

                    if (is_callable($field["TYPE"])) {
                        $field["TYPE"](...[$name, $value, $field["OPTIONAL_DATA"] ?? null]);
                    } else {
                        switch ($field["TYPE"]) {
                            case "TEXT":
                                ?><input type="text" name="<?= $name ?>" size="47" value="<?= $value ?>" title="" /><?
                                break;

                            case "CHECKBOX":
                                ?><input type="checkbox" name="<?= $name ?>"
                                         value="Y" <?= ($value == "Y") ? "checked" : "" ?> title=""/><?
                                break;

                            case "SELECT":
                                echo SelectBoxFromArray($name, $variants, $value, "Не выбрано", "");
                                break;

                            case "SELECT_MULTIPLE":
                                echo SelectBoxMFromArray($name . '[]', $variants, $value, "", "");
                                break;

                            case "USER":
                                echo FindUserID($name, $value, "", "find_form");
                                break;

                            case "DATE_PERIOD":
                                echo \CAdminCalendar::CalendarPeriod($name, $name . "_2", $value,
                                    $this->filterSearch[$name . "_2"], false, 15, true);
                                break;

                        }
                    }
                    ?>
                </td>
            </tr>
            <?
        }
        $oFilter->Buttons([
            "table_id" => $this->getTableListId(),
            "url" => $this->getFilterUri(),
            "form" => "find_form"
        ]);
        $oFilter->End();

        ?></form><?
    }

    /**
     * Значение для атрибута action формы фильтра
     * @return string
     */
    protected function getFilterUri(): string
    {
        return $this->app->GetCurPage();
    }

    /**
     * Применять ли {@see \Bitrix\Main\UI\AdminPageNavigation}<br>
     * <b>!!!</b>Используйте ключ <b>'count_total'</b> в ORM Query<br>
     * А также offset и limit
     * @return bool
     */
    public function useAdvancedNavigation()
    {
        return false;
    }

    /**
     * @return \Bitrix\Main\UI\AdminPageNavigation|null
     */
    public function getAdvancedNavigation()
    {
        return $this->nav;
    }

    /**
     * @param $rows \CAdminListRow[]
     * @see \Local\Core\Inner\AdminHelper\Reference\Transport\Model\AdminList::finalizeAdminRow()
     */
    protected function finalizeAdminRow($rows)
    {

    }


}
