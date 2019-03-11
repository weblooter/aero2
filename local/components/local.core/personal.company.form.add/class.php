<?
class PersonalCompanyFormAddComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{

    public function executeComponent()
    {

        if( !empty( \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('COMPANY_FIELD') ) && check_bitrix_sessid() )
            $this->__tryAdd();

        if( $this->arResult['ADD_STATUS'] != 'SUCCESS' )
            $this->__getAndSetOrmFields();

        $this->includeComponentTemplate();
    }

    public function onPrepareComponentParams( $arParams )
    {
        if( !empty( $arParams['ALLOW_FIELDS_LIST'] ) )
        {
            $arParams['ALLOW_FIELDS_LIST'] = array_diff($arParams['ALLOW_FIELDS_LIST'], ['']);
        }
        else
            $arParams['ALLOW_FIELDS_LIST'] = [];

        return $arParams;
    }

    private function __getAndSetOrmFields()
    {
        $arRequestFieldsValues = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('COMPANY_FIELD');

        /** @var \Bitrix\Main\ORM\Fields\ScalarField $obField */
        foreach (\Local\Core\Model\Data\CompanyTable::getMap() as $obField)
        {
            if( !in_array($obField->getColumnName(), $this->arParams['ALLOW_FIELDS_LIST']) )
                continue;

            $this->arResult['FIELDS'][ $obField->getColumnName() ] = [
                'TITLE' => $obField->getTitle(),
                'CODE' => $obField->getColumnName(),
                'IS_REQUIRED' => $obField->isRequired(),
                'VALUE' => $arRequestFieldsValues[ $obField->getColumnName() ] ?? ''
            ];
        }
    }

    private function __tryAdd()
    {
        $arCompanyFields = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('COMPANY_FIELD');

        $arAddFields = [];
        foreach ($arCompanyFields as $key=>$val)
        {
            if( in_array($key, $this->arParams['ALLOW_FIELDS_LIST']) )
            {
                $val = trim( strip_tags( $val ) );
                $arAddFields[ $key ] = $val;
            }
        }

        if( !empty( $arAddFields ) )
        {
            /** @var \Bitrix\Main\ORM\Data\AddResult $addResult */
            $addResult = \Local\Core\Model\Data\CompanyTable::add($arAddFields);
            if( $addResult->isSuccess() )
            {
                $this->arResult['ADD_STATUS'] = 'SUCCESS';
                $this->arResult['COMPANY_ID'] = $addResult->getId();
            }
            else
            {
                $this->arResult['ADD_STATUS'] = 'ERROR';
                $this->arResult['ADD_ERRORS'] = $addResult->getErrorMessages();
            }
        }
    }
}