<?php

namespace Local\Core\Ajax\Handler;


use Local\Core\Model\Data\StoreTable;
use Local\Core\Model\Data\StoreTariffChangeLogTable;

class Store
{
    public static function delete(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $arResult = [];

        switch (\Local\Core\Inner\Store\Base::checkUserAccess($args['store_id'], $GLOBALS['USER']->GetId())) {
            case \Local\Core\Inner\Store\Base::ACCESS_STORE_IS_MINE:
                $intStoreId = $args['store_id'];
                $ar = StoreTable::getByPrimary($intStoreId, ['select' => ['*', 'COMPANY_DATA_' => 'COMPANY']])
                    ->fetch();
                if (!empty($ar['ID']) && $ar['COMPANY_DATA_USER_OWN_ID'] == $GLOBALS['USER']->GetId()) {
                    StoreTable::delete($ar['ID']);
                    $arResult['result'] = 'SUCCESS';
                }
                break;

            default:
                $arResult['result'] = 'error';
                $arResult['error_text'] = 'Не удалось найти магазин или у Вас нет на него прав';
                break;
        }

        $response->setContentJson($arResult);
    }

    public static function changeTariff(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $arResult = [];

        try {
            switch (\Local\Core\Inner\Store\Base::checkUserAccess($args['store_id'])) {
                case \Local\Core\Inner\Store\Base::ACCESS_STORE_IS_MINE:

                    $obRes = \Local\Core\Inner\Store\Base::changeStoreTariff($args['store_id'], $args['tariff_code']);
                    if ($obRes->isSuccess()) {
                        $arResult['result'] = 'SUCCESS';
                    } else {
                        $arResult['result'] = 'error';
                        $arResult['error_text'] = implode('<br/>', $obRes->getErrorMessages());
                    }

                    break;

                default:
                    throw new \Exception('Не удалось найти магазин');
                    break;
            }
        } catch (\Exception $e) {
            $arResult['result'] = 'error';
            $arResult['error_text'] = $e->getMessage();
        }

        $response->setContentJson($arResult);
    }
}