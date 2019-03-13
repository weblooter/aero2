<?php

namespace Sprint\Migration\Helpers;

use Sprint\Migration\Helper;

class AgentHelper extends Helper
{

    /**
     * Получает список агентов по фильтру
     * @param array $filter
     * @return array
     */
    public function getList($filter = array()) {
        $res = array();
        $dbres = \CAgent::GetList(array("MODULE_ID" => "ASC"), $filter);
        while ($item = $dbres->Fetch()) {
            $res[] = $item;
        }
        return $res;
    }

    /**
     * Получает список агентов по фильтру
     * Данные подготовлены для экспорта в миграцию или схему
     * @param array $filter
     * @return array
     */
    public function exportAgents($filter = array()) {
        $agents = $this->getList($filter);

        $exportAgents = array();
        foreach ($agents as $agent) {
            $exportAgents[] = $this->prepareExportAgent($agent);
        }

        return $exportAgents;
    }

    /**
     * Получает агента
     * Данные подготовлены для экспорта в миграцию или схему
     * @param $moduleId
     * @param string $name
     * @return bool
     */
    public function exportAgent($moduleId, $name = '') {
        $agent = $this->getAgent($moduleId, $name);
        if (empty($agent)) {
            return false;
        }

        return $this->prepareExportAgent($agent);
    }

    /**
     * Получает агента
     * @param $moduleId
     * @param string $name
     * @return array
     */
    public function getAgent($moduleId, $name = '') {
        $filter = is_array($moduleId) ? $moduleId : array(
            'MODULE_ID' => $moduleId
        );

        if (!empty($name)) {
            $filter['NAME'] = $name;
        }

        return \CAgent::GetList(array(
            "MODULE_ID" => "ASC"
        ), $filter)->Fetch();
    }

    /**
     * Удаляет агента
     * @param $moduleId
     * @param $name
     * @return bool
     */
    public function deleteAgent($moduleId, $name) {
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        \CAgent::RemoveAgent($name, $moduleId);
        return true;
    }

    /**
     * Удаляет агента если существует
     * @param $moduleId
     * @param $name
     * @return bool
     */
    public function deleteAgentIfExists($moduleId, $name) {
        $item = $this->getAgent($moduleId, $name);
        if (empty($item)) {
            return false;
        }

        return $this->deleteAgent($moduleId, $name);
    }

    /**
     * Сохраняет агента
     * Создаст если не было, обновит если существует и отличается
     * @param array $fields , обязательные параметры - id модуля, функция агента
     * @return bool|mixed
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function saveAgent($fields = array()) {
        $this->checkRequiredKeys(__METHOD__, $fields, array('MODULE_ID', 'NAME'));

        $exists = $this->getAgent(array(
            'MODULE_ID' => $fields['MODULE_ID'],
            'NAME' => $fields['NAME']
        ));

        $exportExists = $this->prepareExportAgent($exists);
        $fields = $this->prepareExportAgent($fields);

        if (empty($exists)) {
            $ok = $this->getMode('test') ? true : $this->addAgent($fields);
            $this->outNoticeIf($ok, 'Агент %s: добавлен', $fields['NAME']);
            return $ok;
        }

        if (strtotime($fields['NEXT_EXEC']) <= strtotime($exportExists['NEXT_EXEC'])) {
            unset($fields['NEXT_EXEC']);
            unset($exportExists['NEXT_EXEC']);
        }

        if ($this->hasDiff($exportExists, $fields)) {
            $ok = $this->getMode('test') ? true : $this->updateAgent($fields);
            $this->outNoticeIf($ok, 'Агент %s: обновлен', $fields['NAME']);
            $this->outDiffIf($ok, $exportExists, $fields);
            return $ok;
        }


        $ok = $this->getMode('test') ? true : $exists['ID'];
        if ($this->getMode('out_equal')) {
            $this->outIf($ok, 'Агент %s: совпадает', $fields['NAME']);
        }
        return $ok;
    }


    /**
     * Обновление агента, бросает исключение в случае неудачи
     * @param $fields , обязательные параметры - id модуля, функция агента
     * @return bool
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function updateAgent($fields) {
        $this->checkRequiredKeys(__METHOD__, $fields, array('MODULE_ID', 'NAME'));
        $this->deleteAgent($fields['MODULE_ID'], $fields['NAME']);
        return $this->addAgent($fields);
    }

    /**
     * Создание агента, бросает исключение в случае неудачи
     * @param $fields , обязательные параметры - id модуля, функция агента
     * @return bool
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function addAgent($fields) {
        $this->checkRequiredKeys(__METHOD__, $fields, array('MODULE_ID', 'NAME'));

        global $DB;

        $fields = array_merge(array(
            'AGENT_INTERVAL' => 86400,
            'ACTIVE' => 'Y',
            'IS_PERIOD' => 'N',
            'NEXT_EXEC' => $DB->GetNowDate(),
        ), $fields);

        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $agentId = \CAgent::AddAgent(
            $fields['NAME'],
            $fields['MODULE_ID'],
            $fields['IS_PERIOD'],
            $fields['AGENT_INTERVAL'],
            '',
            $fields['ACTIVE'],
            $fields['NEXT_EXEC']
        );

        if ($agentId) {
            return $agentId;
        }

        /* @global $APPLICATION \CMain */
        global $APPLICATION;
        if ($APPLICATION->GetException()) {
            $this->throwException(__METHOD__, $APPLICATION->GetException()->GetString());
        } else {
            $this->throwException(__METHOD__, 'Agent %s not added', $fields['NAME']);
        }
    }

    /**
     * @deprecated
     * @param $moduleId
     * @param $name
     * @param $interval
     * @param $nextExec
     * @return bool|mixed
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function replaceAgent($moduleId, $name, $interval, $nextExec) {
        return $this->saveAgent(array(
            'MODULE_ID' => $moduleId,
            'NAME' => $name,
            'AGENT_INTERVAL' => $interval,
            'NEXT_EXEC' => $nextExec,
        ));
    }

    /**
     * @deprecated
     * @param $moduleId
     * @param $name
     * @param $interval
     * @param $nextExec
     * @return bool|mixed
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function addAgentIfNotExists($moduleId, $name, $interval, $nextExec) {
        return $this->saveAgent(array(
            'MODULE_ID' => $moduleId,
            'NAME' => $name,
            'AGENT_INTERVAL' => $interval,
            'NEXT_EXEC' => $nextExec,
        ));
    }

    protected function prepareExportAgent($item) {
        if (empty($item)) {
            return $item;
        }

        unset($item['ID']);
        unset($item['LOGIN']);
        unset($item['USER_NAME']);
        unset($item['LAST_NAME']);
        unset($item['RUNNING']);
        unset($item['DATE_CHECK']);
        unset($item['LAST_EXEC']);

        return $item;
    }
}