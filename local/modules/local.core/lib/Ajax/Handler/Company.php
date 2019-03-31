<?php
namespace Local\Core\Ajax\Handler;


use Local\Core\Model\Data\CompanyTable;

class Company
{
    public static function delete(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $arResult = [];

        if( !empty($args['company_id']) )
        {
            $intCompanyId = $args['company_id'];

            $ar = CompanyTable::getByPrimary($intCompanyId, ['filter' => ['USER_OWN_ID' => $GLOBALS['USER']->GetId()], 'select' => ['ID']])->fetch();
            if( !empty($ar['ID']) )
            {
                CompanyTable::delete($ar['ID']);
                $arResult['result'] = 'SUCCESS';
            }
        }

        if( empty($arResult['result']) )
        {
            $arResult['result'] = 'error';
        }

        $response->setContentJson($arResult);
    }
}