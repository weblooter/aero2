<?

class PersonalBalanceTopUpComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        if (!$GLOBALS['USER']->IsAuthorized()) {
            $this->_show404Page();
        }

        $this->fillResult();

        $this->includeComponentTemplate();
    }

    private function fillResult()
    {
        $arResult = [];

        if (
        !empty(\Bitrix\Main\Application::getInstance()
            ->getContext()
            ->getRequest()
            ->get('handler'))
        ) {
            $obHandler = \Local\Core\Inner\Payment\Factory::factory(\Bitrix\Main\Application::getInstance()
                ->getContext()
                ->getRequest()
                ->get('handler'));
            if ($obHandler instanceof \Local\Core\Inner\Payment\PaymentInterface) {
                $arResult['HANDLER'] = $obHandler;
            }
        }

        $this->arResult = $arResult;
    }
}