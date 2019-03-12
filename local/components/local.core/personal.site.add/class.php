<?php

class PersonalSiteAddComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public $intMaxUploadXMLFileSizeMb;

    public function executeComponent()
    {
        $this->_checkCompanyAccess( $this->arParams[ 'COMPANY_ID' ], $GLOBALS[ 'USER' ]->GetID() );

        $this->intMaxUploadXMLFileSizeMb = \Bitrix\Main\Config\Configuration::getInstance()->get( 'site' )[ 'upload_xml' ][ 'max_size_mb' ] ?? 100;

        $this->__tryAdd();

        $this->__getAndSetResult();

        $this->includeComponentTemplate();
    }

    private function __tryAdd()
    {
        if ( !empty( \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getPost( 'SITE_FIELD' ) ) && check_bitrix_sessid() )
        {
            $arFields = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getPost( 'SITE_FIELD' );

            $arAddFields = [];
            $arAddFields[ 'DOMAIN' ] = $arFields[ 'DOMAIN' ];
            $arAddFields[ 'RESOURCE_TYPE' ] = $arFields[ 'RESOURCE_TYPE' ];
            $arAddFields[ 'COMPANY_ID' ] = $this->arParams[ 'COMPANY_ID' ];


            try
            {
                switch ( $arFields[ 'RESOURCE_TYPE' ] )
                {
                    case 'LINK':
                        break;

                    case 'FILE': // Загрузить файл

                        if ( empty( \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getFile( 'SITE_FIELD' )[ 'name' ][ 'FILE' ] ) )
                        {
                            throw new \Exception( 'Вы не выбрали файл' );
                        }

                        $arFile = array_combine(
                            array_keys( \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getFile( 'SITE_FIELD' ) ),
                            array_column( \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getFile( 'SITE_FIELD' ),
                                'FILE' )
                        );

                        if ( preg_match( '/(\.xml)$/', $arFile[ 'name' ] ) !== 1 )
                        {
                            throw new \Exception( 'Файл должен быть XML' );
                        }

                        if ( round( ( $arFile[ 'size' ] / 1000 / 1000 ), 3 ) > $this->intMaxUploadXMLFileSizeMb )
                        {
                            throw new \Exception( 'Максимальный размер файла - '.$this->intMaxUploadXMLFileSizeMb.'Мб' );
                        }

                        $arFile[ 'MODULE_ID' ] = 'local.core';
                        $intFileSave = \CFile::SaveFile(
                            $arFile,
                            '/local.core/personal.site/xml/'
                        );
                        if ( $intFileSave < 1 )
                        {
                            throw new \Exception( 'Не удалось сохранить файл' );
                        }

                        $arAddFields[ 'FILE_ID' ] = $intFileSave;

                        break;
                }
            }
            catch ( \Exception $e )
            {
                $this->arResult[ 'ADD_STATUS' ] = 'ERROR';
                $this->arResult[ 'ERROR_TEXT' ][] = $e->getMessage();
            }

            /** @var \Bitrix\Main\ORM\Data\AddResult $obRes */
            $obRes = \Local\Core\Model\Data\SiteTable::add( $arAddFields );
            if ( $obRes->isSuccess() )
            {
                $this->arResult[ 'ADD_STATUS' ] = 'SUCCESS';
            }
            else
            {
                $this->arResult[ 'ADD_STATUS' ] = 'ERROR';
                $this->arResult[ 'ERROR_TEXT' ] = $obRes->getErrorMessages();
            }

        }
    }

    private function __getAndSetResult()
    {
        $arRequestFields = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getPost( 'SITE_FIELD' );

        /** @var \Bitrix\Main\ORM\Fields\ScalarField $obField */
        foreach ( \Local\Core\Model\Data\SiteTable::getMap() as $obField )
        {
            if ( $obField instanceof \Bitrix\Main\ORM\Fields\ScalarField )
            {
                $this->arResult[ 'FIELDS' ][ $obField->getColumnName() ] = [
                    'IS_REQUIRED' => $obField->isRequired(),
                    'VALUE' => $arRequestFields[ $obField->getColumnName() ]
                ];
            }
        }
    }
}