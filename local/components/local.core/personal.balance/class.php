<?

class PersonalBalanceComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        if( !$GLOBALS['USER']->IsAuthorized() )
            $this->_show404Page();

        $this->fillResult();
        $this->includeComponentTemplate();
    }

    private function fillResult()
    {
        $arResult = [];

        $arResult['BALANCE_LOG'] = \Local\Core\Model\Data\BalanceLogTable::getList([
            'filter' => [
                'USER_ID' => $GLOBALS['USER']->GetId()
            ],
            'order' => [
                'DATE_CREATE' => 'DESC'
            ],
            'select' => [
                'DATE_CREATE',
                'OPERATION',
                'NOTE'
            ],
            'limit' => 10,
            'offset' => 0
        ])->fetchAll();

        $this->arResult = $arResult;
    }
}