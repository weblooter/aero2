<?php

namespace Local\Core\Inner\Condition;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\UserTable,
    \Bitrix\Main,
    \Bitrix\Iblock;

Loc::loadLanguageFile(__FILE__);

class GlobalCondTree
{
    protected $intMode = LOCAL_CORE_CONDITION_MODE_DEFAULT;			// work mode
    protected $arEvents = array();						// events ID
    protected $arInitParams = array();					// start params
    protected $boolError = false;						// error flag
    protected $arMsg = array();							// messages (errors)

    protected $strFormName = '';						// form name
    protected $strFormID = '';							// form id
    protected $strContID = '';							// container id
    protected $strJSName = '';							// js object var name
    protected $boolCreateForm = false;					// need create form
    protected $boolCreateCont = false;					// need create container
    protected $strPrefix = 'rule';						// prefix for input
    protected $strSepID = '__';							// separator for id

    protected $arSystemMess = array();					// system messages

    protected $arAtomList = null;						// atom list cache
    protected $arAtomJSPath = null;						// atom js files
    protected $arControlList = null;					// control list cache
    protected $arShowControlList = null;				// control show method list
    protected $arShowInGroups = null;					// showin group list
    protected $forcedShowInGroup = null;				// forced showin list
    protected $arInitControlList = null;				// control init list

    protected $arDefaultControl = array(
        'Parse',
        'GetConditionShow',
        'Generate',
        'ApplyValues'
    );													// required control fields

    protected $usedModules = array();					// modules for real conditions
    protected $usedExtFiles = array();					// files from AddEventHandler
    protected $usedEntity = array();					// entity list in conditions

    protected $arConditions = null;						// conditions array

    public function __construct()
    {
        \CJSCore::Init(array("core"));
        \Bitrix\Main\Page\Asset::getInstance()->addJs(\CLocalCore::getModuleAssetsPath().'/lib/Inner/Condition/js/condition.js');
        \Bitrix\Main\Page\Asset::getInstance()->addCss(\CLocalCore::getModuleAssetsPath().'/lib/Inner/Condition/css/condition.css');
    }

    public function __destruct()
    {

    }

    public function OnConditionAtomBuildList()
    {
        if ($this->boolError || isset($this->arAtomList))
            return;

        $this->arAtomList = array();
        $this->arAtomJSPath = array();

        $result = array();
        if (isset($this->arEvents['INTERFACE_ATOMS']))
        {
            $event = new Main\Event(
                $this->arEvents['INTERFACE_ATOMS']['MODULE_ID'],
                $this->arEvents['INTERFACE_ATOMS']['EVENT_ID']
            );
            $event->send();
            $resultList = $event->getResults();
            if (!empty($resultList))
            {
                foreach ($resultList as $eventResult)
                {
                    if ($eventResult->getType() != Main\EventResult::SUCCESS)
                        continue;
                    $module = $eventResult->getModuleId();
                    if (empty($module))
                        continue;
                    $result[] = $eventResult->getParameters();
                }
                unset($eventResult);
            }
            unset($resultList, $event);
        }
        if (isset($this->arEvents['ATOMS']))
        {
            foreach (GetModuleEvents($this->arEvents['ATOMS']['MODULE_ID'], $this->arEvents['ATOMS']['EVENT_ID'], true) as $arEvent)
            {
                $result[] = ExecuteModuleEventEx($arEvent);
            }
        }

        if (!empty($result))
        {
            foreach ($result as $row)
            {
                if (empty($row) || !is_array($row))
                    continue;
                if (empty($row['ID']) || isset($this->arAtomList[$row['ID']]))
                    continue;
                $this->arAtomList[$row['ID']] = $row;
                if (!empty($row['JS_SRC']) && !in_array($row['JS_SRC'], $this->arAtomJSPath))
                    $this->arAtomJSPath[] = $row['JS_SRC'];
            }
            unset($row);
        }
        unset($result);
    }

    public function OnConditionControlBuildList()
    {
        if ($this->boolError || isset($this->arControlList))
            return;

        $this->arControlList = array();
        $this->arShowInGroups = array();
        $this->forcedShowInGroup = array();
        $this->arShowControlList = array();
        $this->arInitControlList = array();

        $result = array();

        if (isset($this->arEvents['CONTROLS']))
        {
            foreach (GetModuleEvents($this->arEvents['CONTROLS']['MODULE_ID'], $this->arEvents['CONTROLS']['EVENT_ID'], true) as $arEvent)
            {
                $result[] = ExecuteModuleEventEx($arEvent);
            }
        }


        if (isset($this->arEvents['INTERFACE_CONTROLS']))
        {
            $event = new Main\Event(
                $this->arEvents['INTERFACE_CONTROLS']['MODULE_ID'],
                $this->arEvents['INTERFACE_CONTROLS']['EVENT_ID']
            );
            $event->send();
            $resultList = $event->getResults();
            if (!empty($resultList))
            {
                foreach ($resultList as $eventResult)
                {
                    if ($eventResult->getType() != Main\EventResult::SUCCESS)
                        continue;
                    $module = $eventResult->getModuleId();
                    if (empty($module))
                        continue;
                    $result[] = $eventResult->getParameters();
                }
                unset($eventResult);
            }
            unset($resultList, $event);
        }

        if (!empty($result))
        {
            $rawControls = array();
            $controlIndex = 0;
            foreach ($result as $arRes)
            {
                if (empty($arRes) || !is_array($arRes))
                    continue;
                if (isset($arRes['ID']))
                {
                    if (isset($arRes['EXIST_HANDLER']) && $arRes['EXIST_HANDLER'] === 'Y')
                    {
                        if (!isset($arRes['MODULE_ID']) && !isset($arRes['EXT_FILE']))
                            continue;
                    }
                    else
                    {
                        $arRes['MODULE_ID'] = '';
                        $arRes['EXT_FILE'] = '';
                    }
                    if (array_key_exists('EXIST_HANDLER', $arRes))
                        unset($arRes['EXIST_HANDLER']);
                    $arRes['GROUP'] = (isset($arRes['GROUP']) && $arRes['GROUP'] == 'Y' ? 'Y' : 'N');
                    if (isset($this->arControlList[$arRes['ID']]))
                    {
                        $this->arMsg[] = array('id' => 'CONTROLS', 'text' => str_replace('#CONTROL#', $arRes['ID'], Loc::getMessage('BT_MOD_COND_ERR_CONTROL_DOUBLE')));
                        $this->boolError = true;
                    }
                    else
                    {
                        if (!$this->CheckControl($arRes))
                            continue;
                        $this->arControlList[$arRes["ID"]] = $arRes;
                        if ($arRes['GROUP'] == 'Y')
                        {
                            if (empty($arRes['FORCED_SHOW_LIST']))
                            {
                                $this->arShowInGroups[] = $arRes['ID'];
                            }
                            else
                            {
                                $forcedList = $arRes['FORCED_SHOW_LIST'];
                                if (!is_array($forcedList))
                                    $forcedList = array($forcedList);
                                foreach ($forcedList as $forcedId)
                                {
                                    if (is_array($forcedId))
                                        continue;
                                    $forcedId = trim($forcedId);
                                    if ($forcedId == '')
                                        continue;
                                    if (!isset($this->forcedShowInGroup[$forcedId]))
                                        $this->forcedShowInGroup[$forcedId] = array();
                                    $this->forcedShowInGroup[$forcedId][] = $arRes['ID'];
                                }
                                unset($forcedId, $forcedList);
                            }
                        }
                        if (isset($arRes['GetControlShow']) && !empty($arRes['GetControlShow']))
                        {
                            if (!in_array($arRes['GetControlShow'], $this->arShowControlList))
                            {
                                $this->arShowControlList[] = $arRes['GetControlShow'];
                                $showDescription = array(
                                    'CONTROL' => $arRes['GetControlShow'],
                                );
                                if (isset($arRes['SORT']) && (int)$arRes['SORT'] > 0)
                                {
                                    $showDescription['SORT'] = (int)$arRes['SORT'];
                                    $showDescription['INDEX'] = 1;
                                }
                                else
                                {
                                    $showDescription['SORT'] = INF;
                                    $showDescription['INDEX'] = $controlIndex;
                                    $controlIndex++;
                                }
                                $rawControls[] = $showDescription;
                                unset($showDescription);
                            }
                        }
                        if (isset($arRes['InitParams']) && !empty($arRes['InitParams']))
                        {
                            if (!in_array($arRes['InitParams'], $this->arInitControlList))
                                $this->arInitControlList[] = $arRes['InitParams'];
                        }
                    }
                }
                elseif (isset($arRes['COMPLEX']) && 'Y' == $arRes['COMPLEX'])
                {
                    $complexModuleID = '';
                    $complexExtFiles = '';
                    if (isset($arRes['EXIST_HANDLER']) && $arRes['EXIST_HANDLER'] === 'Y')
                    {
                        if (isset($arRes['MODULE_ID']))
                            $complexModuleID = $arRes['MODULE_ID'];
                        if (isset($arRes['EXT_FILE']))
                            $complexExtFiles = $arRes['EXT_FILE'];
                    }
                    if (isset($arRes['CONTROLS']) && !empty($arRes['CONTROLS']) && is_array($arRes['CONTROLS']))
                    {
                        if (array_key_exists('EXIST_HANDLER', $arRes))
                            unset($arRes['EXIST_HANDLER']);
                        $arInfo = $arRes;
                        unset($arInfo['COMPLEX'], $arInfo['CONTROLS']);
                        foreach ($arRes['CONTROLS'] as &$arOneControl)
                        {
                            if (isset($arOneControl['ID']))
                            {
                                if (isset($arOneControl['EXIST_HANDLER']) && $arOneControl['EXIST_HANDLER'] === 'Y')
                                {
                                    if (!isset($arOneControl['MODULE_ID']) && !isset($arOneControl['EXT_FILE']))
                                        continue;
                                }
                                $arInfo['GROUP'] = 'N';
                                $arInfo['MODULE_ID'] = isset($arOneControl['MODULE_ID']) ? $arOneControl['MODULE_ID'] : $complexModuleID;
                                $arInfo['EXT_FILE'] = isset($arOneControl['EXT_FILE']) ? $arOneControl['EXT_FILE'] : $complexExtFiles;
                                $control = array_merge($arOneControl, $arInfo);
                                if (isset($this->arControlList[$control['ID']]))
                                {
                                    $this->arMsg[] = array('id' => 'CONTROLS', 'text' => str_replace('#CONTROL#', $control['ID'], Loc::getMessage('BT_MOD_COND_ERR_CONTROL_DOUBLE')));
                                    $this->boolError = true;
                                }
                                else
                                {
                                    if (!$this->CheckControl($control))
                                        continue;
                                    $this->arControlList[$control['ID']] = $control;
                                }
                                unset($control);
                            }
                        }
                        if (isset($arOneControl))
                            unset($arOneControl);
                        if (isset($arRes['GetControlShow']) && !empty($arRes['GetControlShow']))
                        {
                            if (!in_array($arRes['GetControlShow'], $this->arShowControlList))
                            {
                                $this->arShowControlList[] = $arRes['GetControlShow'];
                                $showDescription = array(
                                    'CONTROL' => $arRes['GetControlShow'],
                                );
                                if (isset($arRes['SORT']) && (int)$arRes['SORT'] > 0)
                                {
                                    $showDescription['SORT'] = (int)$arRes['SORT'];
                                    $showDescription['INDEX'] = 1;
                                }
                                else
                                {
                                    $showDescription['SORT'] = INF;
                                    $showDescription['INDEX'] = $controlIndex;
                                    $controlIndex++;
                                }
                                $rawControls[] = $showDescription;
                                unset($showDescription);
                            }
                        }
                        if (isset($arRes['InitParams']) && !empty($arRes['InitParams']))
                        {
                            if (!in_array($arRes['InitParams'], $this->arInitControlList))
                                $this->arInitControlList[] = $arRes['InitParams'];
                        }
                    }
                }
                else
                {
                    foreach ($arRes as &$arOneRes)
                    {
                        if (is_array($arOneRes) && isset($arOneRes['ID']))
                        {
                            if (isset($arOneRes['EXIST_HANDLER']) && $arOneRes['EXIST_HANDLER'] === 'Y')
                            {
                                if (!isset($arOneRes['MODULE_ID']) && !isset($arOneRes['EXT_FILE']))
                                    continue;
                            }
                            else
                            {
                                $arOneRes['MODULE_ID'] = '';
                                $arOneRes['EXT_FILE'] = '';
                            }
                            if (array_key_exists('EXIST_HANDLER', $arOneRes))
                                unset($arOneRes['EXIST_HANDLER']);
                            $arOneRes['GROUP'] = (isset($arOneRes['GROUP']) && $arOneRes['GROUP'] == 'Y' ? 'Y' : 'N');
                            if (isset($this->arControlList[$arOneRes['ID']]))
                            {
                                $this->arMsg[] = array('id' => 'CONTROLS', 'text' => str_replace('#CONTROL#', $arOneRes['ID'], Loc::getMessage('BT_MOD_COND_ERR_CONTROL_DOUBLE')));
                                $this->boolError = true;
                            }
                            else
                            {
                                if (!$this->CheckControl($arOneRes))
                                    continue;
                                $this->arControlList[$arOneRes['ID']] = $arOneRes;
                                if ($arOneRes['GROUP'] == 'Y')
                                {
                                    if (empty($arOneRes['FORCED_SHOW_LIST']))
                                    {
                                        $this->arShowInGroups[] = $arOneRes['ID'];
                                    }
                                    else
                                    {
                                        $forcedList = (!is_array($arOneRes['FORCED_SHOW_LIST']) ? array($arOneRes['FORCED_SHOW_LIST']) : $arOneRes['FORCED_SHOW_LIST']);
                                        foreach ($forcedList as &$forcedId)
                                        {
                                            if (is_array($forcedId))
                                                continue;
                                            $forcedId = trim($forcedId);
                                            if ($forcedId == '')
                                                continue;
                                            if (!isset($this->forcedShowInGroup[$forcedId]))
                                                $this->forcedShowInGroup[$forcedId] = array();
                                            $this->forcedShowInGroup[$forcedId][] = $arOneRes['ID'];
                                        }
                                        unset($forcedId);
                                    }
                                }
                                if (isset($arOneRes['GetControlShow']) && !empty($arOneRes['GetControlShow']))
                                {
                                    if (!in_array($arOneRes['GetControlShow'], $this->arShowControlList))
                                    {
                                        $this->arShowControlList[] = $arOneRes['GetControlShow'];
                                        $showDescription = array(
                                            'CONTROL' => $arOneRes['GetControlShow'],
                                        );
                                        if (isset($arOneRes['SORT']) && (int)$arOneRes['SORT'] > 0)
                                        {
                                            $showDescription['SORT'] = (int)$arOneRes['SORT'];
                                            $showDescription['INDEX'] = 1;
                                        }
                                        else
                                        {
                                            $showDescription['SORT'] = INF;
                                            $showDescription['INDEX'] = $controlIndex;
                                            $controlIndex++;
                                        }
                                        $rawControls[] = $showDescription;
                                        unset($showDescription);
                                    }
                                }
                                if (isset($arOneRes['InitParams']) && !empty($arOneRes['InitParams']))
                                {
                                    if (!in_array($arOneRes['InitParams'], $this->arInitControlList))
                                        $this->arInitControlList[] = $arOneRes['InitParams'];
                                }
                            }
                        }
                    }
                    unset($arOneRes);
                }
            }
            unset($arRes);

            if (!empty($rawControls))
            {
                $this->arShowControlList = array();
                Main\Type\Collection::sortByColumn($rawControls, array('SORT' => SORT_ASC, 'INDEX' => SORT_ASC));
                foreach ($rawControls as $row)
                    $this->arShowControlList[] = $row['CONTROL'];
                unset($row);
            }
            unset($controlIndex, $rawControls);
        }
        if (empty($this->arControlList))
        {
            $this->arMsg[] = array('id' => 'CONTROLS', 'text' => Loc::getMessage('BT_MOD_COND_ERR_CONTROLS_EMPTY'));
            $this->boolError = true;
        }
    }

    protected function CheckControl($arControl)
    {
        $boolResult = true;
        foreach ($this->arDefaultControl as &$strKey)
        {
            if (!isset($arControl[$strKey]) || empty($arControl[$strKey]))
            {
                $boolResult = false;
                break;
            }
        }
        unset($strKey);
        return $boolResult;
    }

    protected function GetModeList()
    {
        return array(
            LOCAL_CORE_CONDITION_MODE_DEFAULT,
            LOCAL_CORE_CONDITION_MODE_PARSE,
            LOCAL_CORE_CONDITION_MODE_GENERATE,
            LOCAL_CORE_CONDITION_MODE_SQL,
            LOCAL_CORE_CONDITION_MODE_SEARCH
        );
    }

    protected function GetEventList($intEventID)
    {
        $arEventList = array(
            LOCAL_CORE_CONDITION_BUILD_CATALOG => array(
                'INTERFACE_ATOMS' => array(
                    'MODULE_ID' => 'local.core',
                    'EVENT_ID' => 'onBuildDiscountInterfaceAtoms'
                ),
                'INTERFACE_CONTROLS' => array(
                    'MODULE_ID' => 'local.core',
                    'EVENT_ID' => 'onBuildDiscountInterfaceControls'
                ),
                'ATOMS' => array(
                    'MODULE_ID' => 'local.core',
                    'EVENT_ID' => 'OnCondCatAtomBuildList'
                ),
                'CONTROLS' => array(
                    'MODULE_ID' => 'local.core',
                    'EVENT_ID' => 'OnCondCatControlBuildList'
                )
            ),
            LOCAL_CORE_CONDITION_BUILD_SALE => array(
                'INTERFACE_ATOMS' => array(
                    'MODULE_ID' => 'sale',
                    'EVENT_ID' => 'onBuildDiscountConditionInterfaceAtoms'
                ),
                'INTERFACE_CONTROLS' => array(
                    'MODULE_ID' => 'sale',
                    'EVENT_ID' => 'onBuildDiscountConditionInterfaceControls'
                ),
                'ATOMS' => array(
                    'MODULE_ID' => 'sale',
                    'EVENT_ID' => 'OnCondSaleAtomBuildList'
                ),
                'CONTROLS' => array(
                    'MODULE_ID' => 'sale',
                    'EVENT_ID' => 'OnCondSaleControlBuildList'
                )
            ),
            LOCAL_CORE_CONDITION_BUILD_SALE_ACTIONS => array(
                'INTERFACE_ATOMS' => array(
                    'MODULE_ID' => 'sale',
                    'EVENT_ID' => 'onBuildDiscountActionInterfaceAtoms'
                ),
                'INTERFACE_CONTROLS' => array(
                    'MODULE_ID' => 'sale',
                    'EVENT_ID' => 'onBuildDiscountActionInterfaceControls'
                ),
                'ATOMS' => array(
                    'MODULE_ID' => 'sale',
                    'EVENT_ID' => 'OnCondSaleActionsAtomBuildList'
                ),
                'CONTROLS' => array(
                    'MODULE_ID' => 'sale',
                    'EVENT_ID' => 'OnCondSaleActionsControlBuildList'
                )
            )
        );
        return (isset($arEventList[$intEventID]) ? $arEventList[$intEventID] : false);
    }

    protected function CheckEvent($arEvent)
    {
        if (!is_array($arEvent))
            return false;
        if (!isset($arEvent['MODULE_ID']) || empty($arEvent['MODULE_ID']) || !is_string($arEvent['MODULE_ID']))
            return false;
        if (!isset($arEvent['EVENT_ID']) || empty($arEvent['EVENT_ID']) || !is_string($arEvent['EVENT_ID']))
            return false;
        return true;
    }

    public function Init($intMode, $mxEvent, $arParams = array())
    {
        global $APPLICATION;
        $this->arMsg = array();

        $intMode = (int)$intMode;
        if (!in_array($intMode, $this->GetModeList()))
            $intMode = LOCAL_CORE_CONDITION_MODE_DEFAULT;
        $this->intMode = $intMode;

        $arEvent = false;
        if (is_array($mxEvent))
        {
            $fields = array(
                'INTERFACE_ATOMS', 'INTERFACE_CONTROLS',
                'ATOMS', 'CONTROLS'
            );
            foreach ($fields as $fieldName)
            {
                if (!isset($mxEvent[$fieldName]) || !$this->CheckEvent($mxEvent[$fieldName]))
                    continue;
                $arEvent[$fieldName] = $mxEvent[$fieldName];
            }
            unset($fieldName);
            if (!isset($arEvent['INTERFACE_CONTROLS']) && !isset($arEvent['CONTROLS']))
                $arEvent = false;
        }
        else
        {
            $mxEvent = (int)$mxEvent;
            if ($mxEvent >= 0)
                $arEvent = $this->GetEventList($mxEvent);
        }

        if ($arEvent === false)
        {
            $this->boolError = true;
            $this->arMsg[] = array('id' => 'EVENT','text' => Loc::getMessage('BT_MOD_COND_ERR_EVENT_BAD'));
        }
        else
        {
            $this->arEvents = $arEvent;
        }

        $this->arInitParams = $arParams;

        if (!is_array($arParams))
            $arParams = array();

        if (LOCAL_CORE_CONDITION_MODE_DEFAULT == $this->intMode)
        {
            if (!empty($arParams) && is_array($arParams))
            {
                if (isset($arParams['FORM_NAME']) && !empty($arParams['FORM_NAME']))
                    $this->strFormName = $arParams['FORM_NAME'];
                if (isset($arParams['FORM_ID']) && !empty($arParams['FORM_ID']))
                    $this->strFormID = $arParams['FORM_ID'];
                if (isset($arParams['CONT_ID']) && !empty($arParams['CONT_ID']))
                    $this->strContID = $arParams['CONT_ID'];
                if (isset($arParams['JS_NAME']) && !empty($arParams['JS_NAME']))
                    $this->strJSName = $arParams['JS_NAME'];

                $this->boolCreateForm = (isset($arParams['CREATE_FORM']) && 'Y' == $arParams['CREATE_FORM']);
                $this->boolCreateCont = (isset($arParams['CREATE_CONT']) && 'Y' == $arParams['CREATE_CONT']);
            }

            if (empty($this->strJSName))
            {
                if (empty($this->strContID))
                {
                    $this->boolError = true;
                    $this->arMsg[] = array('id' => 'JS_NAME','text' => Loc::getMessage('BT_MOD_COND_ERR_JS_NAME_BAD'));
                }
                else
                {
                    $this->strJSName = md5($this->strContID);
                }
            }
        }

        if (LOCAL_CORE_CONDITION_MODE_DEFAULT == $this->intMode || LOCAL_CORE_CONDITION_MODE_PARSE == $this->intMode)
        {
            if (!empty($arParams) && is_array($arParams))
            {
                if (isset($arParams['PREFIX']) && !empty($arParams['PREFIX']))
                    $this->strPrefix = $arParams['PREFIX'];
                if (isset($arParams['SEP_ID']) && !empty($arParams['SEP_ID']))
                    $this->strSepID = $arParams['SEP_ID'];
            }
        }


        $this->OnConditionAtomBuildList();
        $this->OnConditionControlBuildList();

        if (!$this->boolError)
        {
            if (!empty($this->arInitControlList) && is_array($this->arInitControlList))
            {
                if (!empty($arParams) && is_array($arParams))
                {
                    if (isset($arParams['INIT_CONTROLS']) && !empty($arParams['INIT_CONTROLS']) && is_array($arParams['INIT_CONTROLS']))
                    {
                        foreach ($this->arInitControlList as &$arOneControl)
                        {
                            call_user_func_array($arOneControl,
                                array(
                                    $arParams['INIT_CONTROLS']
                                )
                            );
                        }
                        if (isset($arOneControl))
                            unset($arOneControl);
                    }
                }
            }
        }

        if (isset($arParams['SYSTEM_MESSAGES']) && !empty($arParams['SYSTEM_MESSAGES']) && is_array($arParams['SYSTEM_MESSAGES']))
        {
            $this->arSystemMess = $arParams['SYSTEM_MESSAGES'];
        }

        if ($this->boolError)
        {
            $obError = new \CAdminException($this->arMsg);
            $APPLICATION->ThrowException($obError);
        }
        return !$this->boolError;
    }

    public function Show($arConditions)
    {
        $this->arMsg = array();

        dump($this->boolError);

        if (!$this->boolError)
        {
            if (!empty($arConditions))
            {
                if (!is_array($arConditions))
                {
                    if (!CheckSerializedData($arConditions))
                    {
                        $this->boolError = true;
                        $this->arMsg[] = array('id' => 'CONDITIONS', 'text' => Loc::getMessage('BT_MOD_COND_ERR_SHOW_DATA_UNSERIALIZE'));
                    }
                    else
                    {
                        $arConditions = unserialize($arConditions);
                        if (!is_array($arConditions))
                        {
                            $this->boolError = true;
                            $this->arMsg[] = array('id' => 'CONDITIONS', 'text' => Loc::getMessage('BT_MOD_COND_ERR_SHOW_DATA_UNSERIALIZE'));
                        }
                    }
                }
            }
        }

        if (!$this->boolError)
        {
            $this->arConditions = (!empty($arConditions) ? $arConditions : $this->GetDefaultConditions());

            $strResult = '';

            $this->ShowScripts();

            if ($this->boolCreateForm)
            {

            }
            if ($this->boolCreateCont)
            {

            }

            $strResult .= '<script type="text/javascript">'."\n";
            $strResult .= 'var '.$this->strJSName.' = new BX.TreeConditions('."\n";
            $strResult .= $this->ShowParams().",\n";
            $strResult .= $this->ShowConditions().",\n";
            $strResult .= $this->ShowControls()."\n";

            $strResult .= ');'."\n";
            $strResult .= '</script>'."\n";

            if ($this->boolCreateCont)
            {

            }
            if ($this->boolCreateForm)
            {

            }

            echo $strResult;
        }
    }

    public function GetDefaultConditions()
    {
        return array(
            'CLASS_ID' => 'CondGroup',
            'DATA' => array('All' => 'AND', 'True' => 'True'),
            'CHILDREN' => array()
        );
    }

    public function Parse($arData = '', $arParams = false)
    {
        global $APPLICATION;
        $this->arMsg = array();

        $this->usedModules = array();
        $this->usedExtFiles = array();

        $arResult = array();
        if (!$this->boolError)
        {
            if (empty($arData) || !is_array($arData))
            {
                if (isset($_POST[$this->strPrefix]) && !empty($_POST[$this->strPrefix]) && is_array($_POST[$this->strPrefix]))
                {
                    $arData = $_POST[$this->strPrefix];
                }
                else
                {
                    $this->boolError = true;
                    $this->arMsg[] = array('id' => 'CONDITIONS', 'text' => Loc::getMessage('BT_MOD_COND_ERR_PARSE_DATA_EMPTY'));
                }
            }
        }

        if (!$this->boolError)
        {
            foreach ($arData as $strKey => $value)
            {
                $arKeys = $this->__ConvertKey($strKey);
                if (empty($arKeys))
                {
                    $this->boolError = true;
                    $this->arMsg[] = array('id' => 'CONDITIONS', 'text' => Loc::getMessage('BT_MOD_COND_ERR_PARSE_DATA_BAD_KEY'));
                    break;
                }

                if (!isset($value['controlId']) || empty($value['controlId']))
                {
                    $this->boolError = true;
                    $this->arMsg[] = array('id' => 'CONDITIONS', 'text' => Loc::getMessage('BT_MOD_COND_ERR_PARSE_DATA_EMPTY_CONTROLID'));
                    break;
                }

                if (!isset($this->arControlList[$value['controlId']]))
                {
                    $this->boolError = true;
                    $this->arMsg[] = array('id' => 'CONDITIONS', 'text' => Loc::getMessage('BT_MOD_COND_ERR_PARSE_DATA_BAD_CONTROLID'));
                    break;
                }

                $arOneCondition = call_user_func_array($this->arControlList[$value['controlId']]['Parse'],
                    array(
                        $value
                    )
                );
                if (false === $arOneCondition)
                {
                    $this->boolError = true;
                    $this->arMsg[] = array('id' => 'CONDITIONS', 'text' => Loc::getMessage('BT_MOD_COND_ERR_PARSE_DATA_CONTROL_BAD_VALUE'));
                    break;
                }

                $arItem = array(
                    'CLASS_ID' => $value['controlId'],
                    'DATA' => $arOneCondition
                );
                if ('Y' == $this->arControlList[$value['controlId']]['GROUP'])
                {
                    $arItem['CHILDREN'] = array();
                }
                if (!$this->__SetCondition($arResult, $arKeys, 0, $arItem))
                {
                    $this->boolError = true;
                    $this->arMsg[] = array('id' => 'CONDITIONS', 'text' => Loc::getMessage('BT_MOD_COND_ERR_PARSE_DATA_DOUBLE_KEY'));
                    break;
                }
            }
        }

        if ($this->boolError)
        {
            $obError = new \CAdminException($this->arMsg);
            $APPLICATION->ThrowException($obError);
        }
        return (!$this->boolError ? $arResult : '');
    }

    public function ShowScripts()
    {
        if (!$this->boolError)
        {
            $this->ShowAtoms();
        }
    }

    public function ShowAtoms()
    {
        global $APPLICATION;

        if (!$this->boolError)
        {
            if (!isset($this->arAtomList))
            {
                $this->OnConditionAtomBuildList();
            }
            if (isset($this->arAtomJSPath) && !empty($this->arAtomJSPath))
            {
                foreach ($this->arAtomJSPath as &$strJSPath)
                {
                    $APPLICATION->AddHeadScript($strJSPath);
                }
                if (isset($strJSPath))
                    unset($strJSPath);
            }
        }
    }

    public function ShowParams()
    {
        if (!$this->boolError)
        {
            $arParams = array(
                'parentContainer' => $this->strContID,
                'form' => $this->strFormID,
                'formName' => $this->strFormName,
                'sepID' => $this->strSepID,
                'prefix' => $this->strPrefix,
            );

            if (!empty($this->arSystemMess))
                $arParams['messTree'] = $this->arSystemMess;

            return \CUtil::PhpToJSObject($arParams);
        }
        else
        {
            return '';
        }
    }

    public function ShowControls()
    {
        if ($this->boolError)
            return '';

        $result = array();
        if (!empty($this->arShowControlList))
        {
            foreach ($this->arShowControlList as &$arOneControl)
            {
                $arShowControl = call_user_func_array($arOneControl, array(
                    array('SHOW_IN_GROUPS' => $this->arShowInGroups)
                ));
                if (!empty($arShowControl) && is_array($arShowControl))
                {
                    $this->fillForcedShow($arShowControl);
                    if (isset($arShowControl['controlId']) || isset($arShowControl['controlgroup']))
                    {
                        $result[] = $arShowControl;
                    }
                    else
                    {
                        foreach ($arShowControl as &$oneControl)
                            $result[] = $oneControl;
                        unset($oneControl);
                    }
                }
            }
            unset($arOneControl);
        }

        return \CUtil::PhpToJSObject($result);
    }

    public function ShowLevel(&$arLevel, $boolFirst = false)
    {
        $boolFirst = ($boolFirst === true);
        $arResult = array();
        if (empty($arLevel) || !is_array($arLevel))
            return $arResult;
        $intCount = 0;
        if ($boolFirst)
        {
            if (isset($arLevel['CLASS_ID']) && !empty($arLevel['CLASS_ID']))
            {
                if (isset($this->arControlList[$arLevel['CLASS_ID']]))
                {
                    $arOneControl = $this->arControlList[$arLevel['CLASS_ID']];
                    $arParams = array(
                        'COND_NUM' => $intCount,
                        'DATA' => $arLevel['DATA'],
                        'ID' => $arOneControl['ID'],
                    );
                    $arOneResult = call_user_func_array($arOneControl["GetConditionShow"],
                        array(
                            $arParams,
                        )
                    );
                    if ('Y' == $arOneControl['GROUP'])
                    {
                        $arOneResult['children'] = array();
                        if (isset($arLevel['CHILDREN']))
                            $arOneResult['children'] = $this->ShowLevel($arLevel['CHILDREN'], false);
                    }
                    $arResult[] = $arOneResult;
                    $intCount++;
                }
            }
        }
        else
        {
            foreach ($arLevel as &$arOneCondition)
            {
                if (isset($arOneCondition['CLASS_ID']) && !empty($arOneCondition['CLASS_ID']))
                {
                    if (isset($this->arControlList[$arOneCondition['CLASS_ID']]))
                    {
                        $arOneControl = $this->arControlList[$arOneCondition['CLASS_ID']];
                        $arParams = array(
                            'COND_NUM' => $intCount,
                            'DATA' => $arOneCondition['DATA'],
                            'ID' => $arOneControl['ID'],
                        );
                        $arOneResult = call_user_func_array($arOneControl["GetConditionShow"],
                            array(
                                $arParams,
                            )
                        );

                        if ('Y' == $arOneControl['GROUP'] && isset($arOneCondition['CHILDREN']))
                        {
                            $arOneResult['children'] = $this->ShowLevel($arOneCondition['CHILDREN'], false);
                        }
                        $arResult[] = $arOneResult;
                        $intCount++;
                    }
                }
            }
            if (isset($arOneCondition))
                unset($arOneCondition);
        }
        return $arResult;
    }

    public function ShowConditions()
    {
        if (!$this->boolError)
        {
            if (empty($this->arConditions))
                $this->arConditions = $this->GetDefaultConditions();

            $arResult = $this->ShowLevel($this->arConditions, true);

            return \CUtil::PhpToJSObject(current($arResult));
        }
        else
        {
            return '';
        }
    }

    public function Generate($arConditions, $arParams)
    {
        $this->usedModules = array();
        $this->usedExtFiles = array();
        $this->usedEntity = array();

        $strResult = '';
        if (!$this->boolError)
        {
            if (!empty($arConditions) && is_array($arConditions))
            {
                $arResult = $this->GenerateLevel($arConditions, $arParams, true);
                if (empty($arResult))
                {
                    $strResult = '';
                    $this->boolError = true;
                }
                else
                {
                    $strResult = current($arResult);
                }
            }
            else
            {
                $this->boolError = true;
            }
        }
        return $strResult;
    }

    public function GenerateLevel(&$arLevel, $arParams, $boolFirst = false)
    {
        $arResult = array();
        $boolFirst = ($boolFirst === true);
        if (empty($arLevel) || !is_array($arLevel))
        {
            return $arResult;
        }
        if ($boolFirst)
        {
            if (isset($arLevel['CLASS_ID']) && !empty($arLevel['CLASS_ID']))
            {
                if (isset($this->arControlList[$arLevel['CLASS_ID']]))
                {
                    $arOneControl = $this->arControlList[$arLevel['CLASS_ID']];
                    if ('Y' == $arOneControl['GROUP'])
                    {
                        $arSubEval = $this->GenerateLevel($arLevel['CHILDREN'], $arParams);
                        if (false === $arSubEval || !is_array($arSubEval))
                            return false;
                        $strEval = call_user_func_array($arOneControl['Generate'],
                            array($arLevel['DATA'], $arParams, $arLevel['CLASS_ID'], $arSubEval)
                        );
                    }
                    else
                    {
                        $strEval = call_user_func_array($arOneControl['Generate'],
                            array($arLevel['DATA'], $arParams, $arLevel['CLASS_ID'])
                        );
                    }
                    if (false === $strEval || !is_string($strEval) || 'false' === $strEval)
                    {
                        return false;
                    }
                    $arResult[] = '('.$strEval.')';
                    $this->fillUsedData($arOneControl);
                }
            }
        }
        else
        {
            foreach ($arLevel as &$arOneCondition)
            {
                if (isset($arOneCondition['CLASS_ID']) && !empty($arOneCondition['CLASS_ID']))
                {
                    if (isset($this->arControlList[$arOneCondition['CLASS_ID']]))
                    {
                        $arOneControl = $this->arControlList[$arOneCondition['CLASS_ID']];
                        if ('Y' == $arOneControl['GROUP'])
                        {
                            $arSubEval = $this->GenerateLevel($arOneCondition['CHILDREN'], $arParams);
                            if (false === $arSubEval || !is_array($arSubEval))
                                return false;
                            $strEval = call_user_func_array($arOneControl['Generate'],
                                array($arOneCondition['DATA'], $arParams, $arOneCondition['CLASS_ID'], $arSubEval)
                            );
                        }
                        else
                        {
                            $strEval = call_user_func_array($arOneControl['Generate'],
                                array($arOneCondition['DATA'], $arParams, $arOneCondition['CLASS_ID'])
                            );
                        }

                        if (false === $strEval || !is_string($strEval) || 'false' === $strEval)
                        {
                            return false;
                        }
                        $arResult[] = '('.$strEval.')';
                        $this->fillUsedData($arOneControl);
                    }
                }
            }
            if (isset($arOneCondition))
                unset($arOneCondition);
        }

        if (!empty($arResult))
        {
            foreach ($arResult as $key => $value)
            {
                if ('' == $value || '()' == $value)
                    unset($arResult[$key]);
            }
        }
        if (!empty($arResult))
            $arResult = array_values($arResult);

        return $arResult;
    }

    public function GetConditionValues($arConditions)
    {
        $arResult = false;
        if (!$this->boolError)
        {
            if (!empty($arConditions) && is_array($arConditions))
            {
                $arValues = array();
                $this->GetConditionValuesLevel($arConditions, $arValues, true);
                $arResult = $arValues;
            }
        }
        return $arResult;
    }

    public function GetConditionValuesLevel(&$arLevel, &$arResult, $boolFirst = false)
    {
        $boolFirst = ($boolFirst === true);
        if (is_array($arLevel) && !empty($arLevel))
        {
            if ($boolFirst)
            {
                if (isset($arLevel['CLASS_ID']) && !empty($arLevel['CLASS_ID']))
                {
                    if (isset($this->arControlList[$arLevel['CLASS_ID']]))
                    {
                        $arOneControl = $this->arControlList[$arLevel['CLASS_ID']];
                        if ('Y' == $arOneControl['GROUP'])
                        {
                            if (call_user_func_array($arOneControl['ApplyValues'],
                                array($arLevel['DATA'], $arLevel['CLASS_ID'])))
                            {
                                $this->GetConditionValuesLevel($arLevel['CHILDREN'], $arResult, false);
                            }
                        }
                        else
                        {
                            $arCondInfo = call_user_func_array($arOneControl['ApplyValues'],
                                array($arLevel['DATA'], $arLevel['CLASS_ID'])
                            );
                            if (!empty($arCondInfo) && is_array($arCondInfo))
                            {
                                if (!isset($arResult[$arLevel['CLASS_ID']]) || empty($arResult[$arLevel['CLASS_ID']]) || !is_array($arResult[$arLevel['CLASS_ID']]))
                                {
                                    $arResult[$arLevel['CLASS_ID']] = $arCondInfo;
                                }
                                else
                                {
                                    $arResult[$arLevel['CLASS_ID']]['VALUES'] = array_merge($arResult[$arLevel['CLASS_ID']]['VALUES'], $arCondInfo['VALUES']);
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                foreach ($arLevel as &$arOneCondition)
                {
                    if (isset($arOneCondition['CLASS_ID']) && !empty($arOneCondition['CLASS_ID']))
                    {
                        if (isset($this->arControlList[$arOneCondition['CLASS_ID']]))
                        {
                            $arOneControl = $this->arControlList[$arOneCondition['CLASS_ID']];
                            if ('Y' == $arOneControl['GROUP'])
                            {
                                if (call_user_func_array($arOneControl['ApplyValues'],
                                    array($arOneCondition['DATA'], $arOneCondition['CLASS_ID'])))
                                {
                                    $this->GetConditionValuesLevel($arOneCondition['CHILDREN'], $arResult, false);
                                }
                            }
                            else
                            {
                                $arCondInfo = call_user_func_array($arOneControl['ApplyValues'],
                                    array($arOneCondition['DATA'], $arOneCondition['CLASS_ID'])
                                );
                                if (!empty($arCondInfo) && is_array($arCondInfo))
                                {
                                    if (!isset($arResult[$arOneCondition['CLASS_ID']]) || empty($arResult[$arOneCondition['CLASS_ID']]) || !is_array($arResult[$arOneCondition['CLASS_ID']]))
                                    {
                                        $arResult[$arOneCondition['CLASS_ID']] = $arCondInfo;
                                    }
                                    else
                                    {
                                        $arResult[$arOneCondition['CLASS_ID']]['VALUES'] = array_merge($arResult[$arOneCondition['CLASS_ID']]['VALUES'], $arCondInfo['VALUES']);
                                    }
                                }
                            }
                        }
                    }
                }
                if (isset($arOneCondition))
                    unset($arOneCondition);
            }
        }
    }

    public function GetConditionHandlers()
    {
        return array(
            'MODULES' => (!empty($this->usedModules) ? array_keys($this->usedModules) : array()),
            'EXT_FILES' => (!empty($this->usedExtFiles) ? array_keys($this->usedExtFiles) : array())
        );
    }

    public function GetUsedEntityList()
    {
        return $this->usedEntity;
    }

    protected function __ConvertKey($strKey)
    {
        if ('' !== $strKey)
        {
            $arKeys = explode($this->strSepID, $strKey);
            if (is_array($arKeys))
            {
                foreach ($arKeys as &$intOneKey)
                {
                    $intOneKey = (int)$intOneKey;
                }
            }
            return $arKeys;
        }
        else
        {
            return false;
        }
    }

    protected function __SetCondition(&$arResult, $arKeys, $intIndex, $arOneCondition)
    {
        if (0 == $intIndex)
        {
            if (1 == sizeof($arKeys))
            {
                $arResult = $arOneCondition;
                return true;
            }
            else
            {
                return $this->__SetCondition($arResult, $arKeys, $intIndex + 1, $arOneCondition);
            }
        }
        else
        {
            if (!isset($arResult['CHILDREN']))
            {
                $arResult['CHILDREN'] = array();
            }
            if (!isset($arResult['CHILDREN'][$arKeys[$intIndex]]))
            {
                $arResult['CHILDREN'][$arKeys[$intIndex]] = array();
            }
            if (($intIndex + 1) < sizeof($arKeys))
            {
                return $this->__SetCondition($arResult['CHILDREN'][$arKeys[$intIndex]], $arKeys, $intIndex + 1, $arOneCondition);
            }
            else
            {
                if (!empty($arResult['CHILDREN'][$arKeys[$intIndex]]))
                {
                    return false;
                }
                else
                {
                    $arResult['CHILDREN'][$arKeys[$intIndex]] = $arOneCondition;
                    return true;
                }
            }
        }
    }

    protected function fillUsedData(&$control)
    {
        if (!empty($control['MODULE_ID']))
        {
            if (is_array($control['MODULE_ID']))
            {
                foreach ($control['MODULE_ID'] as &$oneModuleID)
                {
                    if ($oneModuleID != $this->arEvents['CONTROLS']['MODULE_ID'])
                        $this->usedModules[$oneModuleID] = true;
                }
                unset($oneModuleID);
            }
            else
            {
                if ($control['MODULE_ID'] != $this->arEvents['CONTROLS']['MODULE_ID'])
                    $this->usedModules[$control['MODULE_ID']] = true;
            }
        }
        if (!empty($control['EXT_FILE']))
        {
            if (is_array($control['EXT_FILE']))
            {
                foreach ($control['EXT_FILE'] as &$oneExtFile)
                    $this->usedExtFiles[$oneExtFile] = true;
                unset($oneExtFile);
            }
            else
            {
                $this->usedExtFiles[$control['EXT_FILE']] = true;
            }
        }

        if (!empty($control['ENTITY']))
        {
            $entityID = $control['ENTITY'].'|';
            $entityID .= (is_array($control['FIELD']) ? implode('-', $control['FIELD']) : $control['FIELD']);
            if (!isset($this->usedEntity[$entityID]))
            {
                $this->usedEntity[$entityID] = array(
                    'MODULE' => (!empty($control['MODULE_ID']) ? $control['MODULE_ID'] : $control['MODULE_ENTITY']),
                    'ENTITY' => $control['ENTITY'],
                    'FIELD_ENTITY' => $control['FIELD'],
                    'FIELD_TABLE' => (!empty($control['FIELD_TABLE']) ? $control['FIELD_TABLE'] : $control['FIELD'])
                );
            }
            unset($entityID);
        }
    }

    protected function fillForcedShow(&$showControl)
    {
        if (empty($this->forcedShowInGroup))
            return;
        if (isset($showControl['controlId']) || isset($showControl['controlgroup']))
        {
            if (!isset($showControl['controlgroup']))
            {
                if (isset($this->forcedShowInGroup[$showControl['controlId']]))
                    $showControl['showIn'] = array_values(array_unique(array_merge(
                        $showControl['showIn'], $this->forcedShowInGroup[$showControl['controlId']]
                    )));
            }
            else
            {
                $forcedGroup = array();
                foreach ($showControl['children'] as &$oneControl)
                {
                    if (isset($oneControl['controlId']))
                    {
                        if (isset($this->forcedShowInGroup[$oneControl['controlId']]))
                        {
                            $oneControl['showIn'] = array_values(array_unique(array_merge(
                                $oneControl['showIn'], $this->forcedShowInGroup[$oneControl['controlId']]
                            )));
                            $forcedGroup = array_merge($forcedGroup, $this->forcedShowInGroup[$oneControl['controlId']]);
                        }
                    }
                }
                unset($oneControl);
                if (!empty($forcedGroup))
                {
                    $forcedGroup = array_values(array_unique($forcedGroup));
                    $showControl['showIn'] = array_values(array_unique(array_merge($showControl['showIn'], $forcedGroup)));
                }
                unset($forcedGroup);

            }
        }
        else
        {
            foreach ($showControl as &$oneControl)
            {
                if (isset($oneControl['controlId']))
                {
                    if (isset($this->forcedShowInGroup[$oneControl['controlId']]))
                        $oneControl['showIn'] = array_values(array_unique(array_merge(
                            $oneControl['showIn'], $this->forcedShowInGroup[$oneControl['controlId']]
                        )));
                }
            }
            unset($oneControl);
        }
    }
}