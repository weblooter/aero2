<?

class PersonalCompanyFormComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        if ($this->arParams['COMPANY_ID'] > 0) {
            $this->_checkCompanyAccess($this->arParams['COMPANY_ID'], $GLOBALS['USER']->GetID());
        }

        $this->__tryUpdate();

        $this->__fillResult();

        $this->includeComponentTemplate();
    }

    public function onPrepareComponentParams($arParams)
    {
        if (empty($arParams['COMPANY_ID'])) {
            $arParams['COMPANY_ID'] = 0;
        }

        return $arParams;
    }

    private function __fillResult()
    {

        $arCompanyFields = [];
        if ($this->arParams['COMPANY_ID'] > 0) {
            $rs = \Local\Core\Model\Data\CompanyTable::getList([
                    'filter' => [
                        'ID' => $this->arParams['COMPANY_ID'],
                        'USER_OWN_ID' => $GLOBALS['USER']->GetID()
                    ]
                ]);
            $arCompanyFields = $rs->fetch();
        }

        if (
        !empty(\Bitrix\Main\Application::getInstance()
            ->getContext()
            ->getRequest()
            ->get('COMPANY_FIELD'))
        ) {
            foreach (\Bitrix\Main\Application::getInstance()
                         ->getContext()
                         ->getRequest()
                         ->get('COMPANY_FIELD') as $k => $v) {
                $arCompanyFields[$k] = $v;
            }
        }

        /** @var \Bitrix\Main\ORM\Fields\ScalarField $obField */
        foreach (\Local\Core\Model\Data\CompanyTable::getMap() as $obField) {
            if ($obField instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                switch ($obField->getName()) {
                    case 'TYPE':
                    case 'NAME':
                        $this->arResult['FIELDS']['GENERAL_FIELDS'][$obField->getName()] = [
                            'TITLE' => $obField->getTitle(),
                            'CODE' => $obField->getName(),
                            'IS_REQUIRED' => true,
                            'VALUE' => $arCompanyFields[$obField->getName()] ?? ''
                        ];
                        break;
                    case 'COMPANY_INN':
                    case 'COMPANY_NAME_SHORT':
                    case 'COMPANY_NAME_FULL':
                    case 'COMPANY_OGRN':
                    case 'COMPANY_KPP':
                    case 'COMPANY_OKPO':
                    case 'COMPANY_OKTMO':
                    case 'COMPANY_DIRECTOR':
                    case 'COMPANY_ACCOUNTANT':
                        $this->arResult['FIELDS']['COMPANY']['BASE_FIELDS'][$obField->getName()] = [
                            'TITLE' => $obField->getTitle(),
                            'CODE' => $obField->getName(),
                            'IS_REQUIRED' => in_array($obField->getName(), ['COMPANY_INN', 'COMPANY_NAME_SHORT', 'COMPANY_NAME_FULL', 'COMPANY_OGRN']),
                            'VALUE' => $arCompanyFields[$obField->getName()] ?? ''
                        ];
                        break;
                    case 'COMPANY_ADDRESS_COUNTRY':
                    case 'COMPANY_ADDRESS_REGION':
                    case 'COMPANY_ADDRESS_AREA':
                    case 'COMPANY_ADDRESS_CITY':
                    case 'COMPANY_ADDRESS_ADDRESS':
                    case 'COMPANY_ADDRESS_OFFICE':
                    case 'COMPANY_ADDRESS_ZIP':
                        $this->arResult['FIELDS']['COMPANY']['ADDRESS'][$obField->getName()] = [
                            'TITLE' => $obField->getTitle(),
                            'CODE' => $obField->getName(),
                            'IS_REQUIRED' => in_array($obField->getName(),
                                ['COMPANY_ADDRESS_COUNTRY', 'COMPANY_ADDRESS_CITY', 'COMPANY_ADDRESS_ADDRESS', 'COMPANY_ADDRESS_OFFICE', 'COMPANY_ADDRESS_ZIP']),
                            'VALUE' => $arCompanyFields[$obField->getName()] ?? ''
                        ];
                        break;
                }
            }
        }

    }

    private function __tryUpdate()
    {
        if (
            !empty(\Bitrix\Main\Application::getInstance()
                ->getContext()
                ->getRequest()
                ->get('COMPANY_FIELD'))
            && check_bitrix_sessid()
        ) {
            $arCompanyFields = \Bitrix\Main\Application::getInstance()
                ->getContext()
                ->getRequest()
                ->get('COMPANY_FIELD');

            unset($arCompanyFields['ID'], $arCompanyFields['USER_OWN_ID']);

            $arCompanyFields = array_map(function ($v)
                {
                    return trim(strip_tags($v));
                }, $arCompanyFields);

            if ($this->arParams['COMPANY_ID'] > 0) {
                $obUpdateResult = \Local\Core\Model\Data\CompanyTable::update($this->arParams['COMPANY_ID'], $arCompanyFields);
                if ($obUpdateResult->isSuccess()) {
                    $this->arResult['STATUS'] = 'UPDATE_SUCCESS';
                } else {
                    $this->arResult['STATUS'] = 'ERROR';
                    $this->arResult['ERROR_TEXT'] = $obUpdateResult->getErrorMessages();
                }
            } else {
                $obAddResult = \Local\Core\Model\Data\CompanyTable::add($arCompanyFields);
                if ($obAddResult->isSuccess()) {
                    $this->arResult['STATUS'] = 'ADD_SUCCESS';
                    $this->arResult['ADD_ID'] = $obAddResult->getId();
                } else {
                    $this->arResult['STATUS'] = 'ERROR';
                    $this->arResult['ERROR_TEXT'] = $obAddResult->getErrorMessages();
                }
            }
        }
    }
}