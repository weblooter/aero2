<?php

class PersonalStoreFormComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public $intMaxUploadXMLFileSizeMb;
    public $intMaxDownloadXMLFileSizeMb;

    public function executeComponent()
    {
        if ($this->arParams['STORE_ID'] > 0) {
            $this->_checkStoreAccess($this->arParams['STORE_ID'], $GLOBALS['USER']->GetID());
        } else {
            $this->_checkCompanyAccess($this->arParams['COMPANY_ID'], $GLOBALS['USER']->GetID());
        }

        $this->intMaxUploadXMLFileSizeMb = \Bitrix\Main\Config\Configuration::getInstance()
                                               ->get('store')['upload_xml']['max_size_mb'] ?? 100;

        $this->intMaxDownloadXMLFileSizeMb = \Bitrix\Main\Config\Configuration::getInstance()
                                                 ->get('store')['download_xml']['max_size_mb'] ?? 300;

        $this->__tryUpdateOrAdd();

        $this->__fillResult();

        $this->includeComponentTemplate();
    }

    public function onPrepareComponentParams($arParams)
    {
        if ($arParams['COMPANY_ID'] < 1) {
            $this->_show404Page();
        }

        return $arParams;
    }

    private function __tryUpdateOrAdd()
    {
        if (
            !empty(\Bitrix\Main\Application::getInstance()
                ->getContext()
                ->getRequest()
                ->getPost('STORE_FIELD'))
            && check_bitrix_sessid()
        ) {
            $arFields = \Bitrix\Main\Application::getInstance()
                ->getContext()
                ->getRequest()
                ->getPost('STORE_FIELD');

            $arUpdateFields = [];
            $arUpdateFields['NAME'] = $arFields['NAME'];
            $arUpdateFields['DOMAIN'] = $arFields['DOMAIN'];
            $arUpdateFields['RESOURCE_TYPE'] = $arFields['RESOURCE_TYPE'];
            $arUpdateFields['COMPANY_ID'] = $this->arParams['COMPANY_ID'];
            $arUpdateFields['BEHAVIOR_IMPORT_ERROR'] = $arFields['BEHAVIOR_IMPORT_ERROR'];
            $arUpdateFields['ALERT_IF_XML_NOT_MODIFIED'] = $arFields['ALERT_IF_XML_NOT_MODIFIED'];

            try {
                switch ($arFields['RESOURCE_TYPE']) {
                    case 'LINK':

                        $arUpdateFields['FILE_LINK'] = $arFields['FILE_LINK'];

                        if ($arFields['HTTP_AUTH'] == 'Y') {
                            $arUpdateFields['HTTP_AUTH'] = $arFields['HTTP_AUTH'];
                            $arUpdateFields['HTTP_AUTH_LOGIN'] = $arFields['HTTP_AUTH_LOGIN'];
                            $arUpdateFields['HTTP_AUTH_PASS'] = $arFields['HTTP_AUTH_PASS'];
                        } else {
                            $arUpdateFields['HTTP_AUTH'] = 'N';
                            $arUpdateFields['HTTP_AUTH_LOGIN'] = '';
                            $arUpdateFields['HTTP_AUTH_PASS'] = '';
                        }

                        break;

                    case 'FILE': // Загрузить файл

                        if (
                            empty(\Bitrix\Main\Application::getInstance()
                                ->getContext()
                                ->getRequest()
                                ->getFile('STORE_FIELD')['name']['FILE'])
                            && ($this->arParams['STORE_ID'] < 1 || $arFields['OLD_FILE'] < 1)
                        ) {
                            throw new \Exception('Вы не выбрали файл');
                        }

                        if (
                            \Bitrix\Main\Application::getInstance()
                                ->getContext()
                                ->getRequest()
                                ->getFile('STORE_FIELD')['size']['FILE'] > 0
                        ) {
                            $arFile = array_combine(array_keys(\Bitrix\Main\Application::getInstance()
                                ->getContext()
                                ->getRequest()
                                ->getFile('STORE_FIELD')), array_column(\Bitrix\Main\Application::getInstance()
                                    ->getContext()
                                    ->getRequest()
                                    ->getFile('STORE_FIELD'), 'FILE'));

                            if (
                            !\Local\Core\Inner\BxModified\CFile::checkExtension($arFile, '.xml')
                            ) {
                                throw new \Exception('Файл должен быть XML');
                            }

                            if (
                                round(($arFile['size'] / 1000 / 1000), 3) > $this->intMaxUploadXMLFileSizeMb
                            ) {
                                throw new \Exception('Максимальный размер файла - '.$this->intMaxUploadXMLFileSizeMb.'Мб');
                            }

                            $intFileSave = \Local\Core\Inner\BxModified\CFile::saveFile($arFile, '/personal.store/xml/', $arFields['OLD_FILE']);
                            if ($intFileSave < 1) {
                                throw new \Exception('Не удалось сохранить файл');
                            }

                            $arUpdateFields['FILE_ID'] = $intFileSave;
                        }

                        break;
                }
            } catch (\Exception $e) {
                $this->arResult['STATUS'] = 'ERROR';
                $this->arResult['ERROR_TEXT'][] = $e->getMessage();
            }

            if (is_null($this->arResult['STATUS'])) {
                if ($this->arParams['STORE_ID'] > 0) {
                    /** @var \Bitrix\Main\ORM\Data\AddResult $obRes */
                    $obRes = \Local\Core\Model\Data\StoreTable::update($this->arParams['STORE_ID'], $arUpdateFields);
                    if ($obRes->isSuccess()) {
                        $this->arResult['STATUS'] = 'SUCCESS_UPDATE';
                    } else {
                        $this->arResult['STATUS'] = 'ERROR';
                        $this->arResult['ERROR_TEXT'] = $obRes->getErrorMessages();
                    }
                } else {
                    /** @var \Bitrix\Main\ORM\Data\AddResult $obRes */
                    $obRes = \Local\Core\Model\Data\StoreTable::add($arUpdateFields);
                    if ($obRes->isSuccess()) {
                        $this->arResult['STATUS'] = 'SUCCESS_ADD';
                        $this->arResult['ADD_ID'] = $obRes->getId();
                    } else {
                        $this->arResult['STATUS'] = 'ERROR';
                        $this->arResult['ERROR_TEXT'] = $obRes->getErrorMessages();
                    }
                }
            }

        }
    }

    private function __fillResult()
    {
        $arDefaultValues = [];
        if ($this->arParams['STORE_ID'] > 0) {
            $arDefaultValues = \Local\Core\Model\Data\StoreTable::getById($this->arParams['STORE_ID'])
                ->fetch();
        }

        $arRequestFields = \Bitrix\Main\Application::getInstance()
            ->getContext()
            ->getRequest()
            ->getPost('STORE_FIELD');

        if (!empty($arRequestFields)) {
            foreach ($arRequestFields as $k => $v) {
                $arDefaultValues[$k] = $v;
            }
        }

        /** @var \Bitrix\Main\ORM\Fields\ScalarField $obField */
        foreach (\Local\Core\Model\Data\StoreTable::getMap() as $obField) {
            if ($obField instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                $this->arResult['FIELDS'][$obField->getColumnName()] = [
                    'IS_REQUIRED' => $obField->isRequired(),
                    'VALUE' => $arDefaultValues[$obField->getColumnName()]
                ];
            }
        }
    }
}