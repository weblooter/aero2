<?php

class PersonalSiteListComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->_checkCompanyAccess($this->arParams['COMPANY_ID'], $GLOBALS['USER']->GetID());

        $this->includeComponentTemplate();
    }
}