<?php

class PersonalSiteAddComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public $intMaxUploadXMLFileSizeMb;
    public $intMaxDownloadXMLFileSizeMb;

    public function executeComponent()
    {
        $this->_checkCompanyAccess(
            $this->arParams['COMPANY_ID'],
            $GLOBALS['USER']->GetID()
        );

        $this->_checkStoreAccess(
            $this->arParams['STORE_ID'],
            $GLOBALS['USER']->GetID()
        );

        $this->intMaxUploadXMLFileSizeMb = \Bitrix\Main\Config\Configuration::getInstance()
                                               ->get('store')['upload_xml']['max_size_mb'] ?? 100;

        $this->intMaxDownloadXMLFileSizeMb = \Bitrix\Main\Config\Configuration::getInstance()
                                                 ->get('store')['download_xml']['max_size_mb'] ?? 300;

        $this->__tryUpdate();

        $this->__getAndSetResult();

        $this->includeComponentTemplate();
    }

    public function onPrepareComponentParams($arParams)
    {
        if( $arParams['COMPANY_ID'] < 1 )
        {
            $this->_show404Page();
        }

        if( $arParams['STORE_ID'] < 1 )
        {
            $this->_show404Page();
        }

        return $arParams;
    }

    private function __tryUpdate()
    {
        if(
            !empty(
            \Bitrix\Main\Application::getInstance()
                ->getContext()
                ->getRequest()
                ->getPost('STORE_FIELD')
            )
            && check_bitrix_sessid()
        )
        {
            $arFields = \Bitrix\Main\Application::getInstance()
                ->getContext()
                ->getRequest()
                ->getPost('STORE_FIELD');

            $arUpdateFields = [];
            $arUpdateFields['DOMAIN'] = $arFields['DOMAIN'];
            $arUpdateFields['RESOURCE_TYPE'] = $arFields['RESOURCE_TYPE'];
            $arUpdateFields['COMPANY_ID'] = $this->arParams['COMPANY_ID'];

            try
            {
                switch( $arFields['RESOURCE_TYPE'] )
                {
                    case 'LINK':

                        $arUpdateFields['FILE_LINK'] = $arFields['FILE_LINK'];

                        if( $arFields['HTTP_AUTH'] == 'Y' )
                        {
                            $arUpdateFields['HTTP_AUTH'] = $arFields['HTTP_AUTH'];
                            $arUpdateFields['HTTP_AUTH_LOGIN'] = $arFields['HTTP_AUTH_LOGIN'];
                            $arUpdateFields['HTTP_AUTH_PASS'] = $arFields['HTTP_AUTH_PASS'];
                        }
                        else
                        {
                            $arUpdateFields['HTTP_AUTH'] = 'N';
                            $arUpdateFields['HTTP_AUTH_LOGIN'] = '';
                            $arUpdateFields['HTTP_AUTH_PASS'] = '';
                        }

                        break;

                    case 'FILE': // Загрузить файл

                        if(
                        empty(
                        \Bitrix\Main\Application::getInstance()
                            ->getContext()
                            ->getRequest()
                            ->getFile('STORE_FIELD')['name']['FILE']
                        )
                        )
                        {
                            throw new \Exception('Вы не выбрали файл');
                        }

                        $arFile = array_combine(
                            array_keys(
                                \Bitrix\Main\Application::getInstance()
                                    ->getContext()
                                    ->getRequest()
                                    ->getFile('STORE_FIELD')
                            ),
                            array_column(
                                \Bitrix\Main\Application::getInstance()
                                    ->getContext()
                                    ->getRequest()
                                    ->getFile('STORE_FIELD'),
                                'FILE'
                            )
                        );

                        if(
                        !\Local\Core\Inner\BxModified\CFile::checkExtension(
                            $arFile,
                            '.xml'
                        )
                        )
                        {
                            throw new \Exception('Файл должен быть XML');
                        }

                        if(
                            round(
                                ( $arFile['size'] / 1000 / 1000 ),
                                3
                            ) > $this->intMaxUploadXMLFileSizeMb
                        )
                        {
                            throw new \Exception('Максимальный размер файла - '.$this->intMaxUploadXMLFileSizeMb.'Мб');
                        }

                        $intFileSave = \Local\Core\Inner\BxModified\CFile::saveFile(
                            $arFile,
                            '/personal.site/xml/'
                        );
                        if( $intFileSave < 1 )
                        {
                            throw new \Exception('Не удалось сохранить файл');
                        }

                        $arUpdateFields['FILE_ID'] = $intFileSave;

                        break;
                }
            }
            catch( \Exception $e )
            {
                $this->arResult['UPDATE_STATUS'] = 'ERROR';
                $this->arResult['ERROR_TEXT'][] = $e->getMessage();
            }

            /** @var \Bitrix\Main\ORM\Data\AddResult $obRes */
            $obRes = \Local\Core\Model\Data\StoreTable::update(
                $this->arParams['STORE_ID'],
                $arUpdateFields
            );
            if( $obRes->isSuccess() )
            {
                $this->arResult['UPDATE_STATUS'] = 'SUCCESS';
            }
            else
            {
                $this->arResult['UPDATE_STATUS'] = 'ERROR';
                $this->arResult['ERROR_TEXT'] = $obRes->getErrorMessages();
            }

        }
    }

    private function __getAndSetResult()
    {
        $arDefaultValues = \Local\Core\Model\Data\StoreTable::getById($this->arParams['STORE_ID'])
            ->fetch();

        /** @var \Bitrix\Main\ORM\Fields\ScalarField $obField */
        foreach( \Local\Core\Model\Data\StoreTable::getMap() as $obField )
        {
            if( $obField instanceof \Bitrix\Main\ORM\Fields\ScalarField )
            {
                $this->arResult['FIELDS'][$obField->getColumnName()] = [
                    'IS_REQUIRED' => $obField->isRequired(),
                    'VALUE' => $arDefaultValues[$obField->getColumnName()]
                ];
            }
        }
    }
}