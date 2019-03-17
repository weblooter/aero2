<?

class PersonalCompanyFormEditComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    const ACCESS_MY_COMPANY = 0x001;
    const ACCESS_COMPANY_NOT_FOUND = 0x002;
    const ACCESS_COMPANY_NOT_MINE = 0x003;

    public function executeComponent()
    {
        $this->_checkCompanyAccess(
            $this->arParams['COMPANY_ID'],
            $GLOBALS['USER']->GetID()
        );

        if(
            !empty(
            \Bitrix\Main\Application::getInstance()
                ->getContext()
                ->getRequest()
                ->get('COMPANY_FIELD')
            )
            && check_bitrix_sessid()
        )
        {
            $this->__tryUpdate();
        }

        $this->__getAndSetOrmFields();

        $this->includeComponentTemplate();
    }

    public function onPrepareComponentParams($arParams)
    {
        if( !empty($arParams['ALLOW_FIELDS_LIST']) )
        {
            $arParams['ALLOW_FIELDS_LIST'] = array_diff(
                $arParams['ALLOW_FIELDS_LIST'],
                ['']
            );
        }
        else
        {
            $arParams['ALLOW_FIELDS_LIST'] = [];
        }

        if( $arParams['COMPANY_ID'] < 1 )
        {
            $this->_show404Page();
        }

        return $arParams;
    }

    private function __getAndSetOrmFields()
    {

        $rs = \Local\Core\Model\Data\CompanyTable::getList(
            [
                'filter' => [
                    'ID' => $this->arParams['COMPANY_ID'],
                    'USER_OWN_ID' => $GLOBALS['USER']->GetID()
                ]
            ]
        );
        $arCompanyFields = $rs->fetch();

        /** @var \Bitrix\Main\ORM\Fields\ScalarField $obField */
        foreach( \Local\Core\Model\Data\CompanyTable::getMap() as $obField )
        {
            if( $obField instanceof \Bitrix\Main\ORM\Fields\ScalarField )
            {
                if(
                !in_array(
                    $obField->getColumnName(),
                    $this->arParams['ALLOW_FIELDS_LIST']
                )
                )
                {
                    continue;
                }

                $this->arResult['FIELDS'][$obField->getColumnName()] = [
                    'TITLE' => $obField->getTitle(),
                    'CODE' => $obField->getColumnName(),
                    'IS_REQUIRED' => $obField->isRequired(),
                    'VALUE' => $arCompanyFields[$obField->getColumnName()] ?? ''
                ];
            }
        }

    }

    private function __tryUpdate()
    {
        $arCompanyFields = \Bitrix\Main\Application::getInstance()
            ->getContext()
            ->getRequest()
            ->get('COMPANY_FIELD');

        $arUpdateFields = [];
        foreach( $arCompanyFields as $key => $val )
        {
            if(
            in_array(
                $key,
                $this->arParams['ALLOW_FIELDS_LIST']
            )
            )
            {
                $val = trim(strip_tags($val));
                $arUpdateFields[$key] = $val;
            }
        }

        if( !empty($arUpdateFields) )
        {
            /** @var \Bitrix\Main\ORM\Data\UpdateResult $obUpdateResult */
            $obUpdateResult = \Local\Core\Model\Data\CompanyTable::update(
                $this->arParams['COMPANY_ID'],
                $arUpdateFields
            );
            if( $obUpdateResult->isSuccess() )
            {
                $this->arResult['UPDATE_STATUS'] = 'SUCCESS';
            }
            else
            {
                $this->arResult['UPDATE_STATUS'] = 'ERROR';
                $this->arResult['UPDATE_ERRORS'] = $obUpdateResult->getErrorMessages();
            }
        }
    }
}