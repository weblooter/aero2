<?

class PersonalCompanyListComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult = $this->__getResult();

        $this->includeComponentTemplate();
    }

    private function __getResult()
    {
        $obCache = \Bitrix\Main\Application::getInstance()
            ->getCache();
        $arResult = [];

        if (
        $obCache->startDataCache((60 * 60 * 24 * 7),
            md5(__METHOD__.'_user_id='.$GLOBALS['USER']->GetID()),
            \Local\Core\Inner\Cache::getComponentCachePath(['personal.company.list'], [
                'user_id='.$GLOBALS['USER']->GetID()
            ]))
        ) {


            $rs = \Local\Core\Model\Data\CompanyTable::getList([
                'filter' => [
                    'USER_OWN_ID' => $GLOBALS['USER']->GetID()
                ],
                'order' => ['DATE_CREATE' => 'DESC'],
                'select' => [
                    'ID',
                    'NAME',
                    'DATE_CREATE',
                    'COMPANY_NAME_SHORT',
                    'COMPANY_INN',
                ],
                "count_total" => true
            ]);
            if ($rs->getSelectedRowsCount() < 1) {
                $obCache->abortDataCache();
                $arResult['ITEMS'] = [];
            } else {

                while ($ar = $rs->fetch()) {
                    $arResult['ITEMS'][$ar['ID']] = $ar;
                }

                $obCache->endDataCache($arResult);
            }
        } else {
            $arResult = $obCache->getVars();
        }

        return $arResult;
    }
}