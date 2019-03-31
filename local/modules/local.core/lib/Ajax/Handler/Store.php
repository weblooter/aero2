<?php
namespace Local\Core\Ajax\Handler;


use Local\Core\Model\Data\StoreTable;

class Store
{
    public static function delete(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $arResult = [];

        if( !empty($args['store_id']) )
        {
            $intStoreId = $args['store_id'];

            $ar = StoreTable::getByPrimary($intStoreId, ['select' => ['*', 'COMPANY_DATA_' => 'COMPANY']])->fetch();
            if( !empty($ar['ID']) && $ar['COMPANY_DATA_USER_OWN_ID'] == $GLOBALS['USER']->GetId() )
            {
                StoreTable::delete($ar['ID']);
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