<?php

namespace Local\Core\Ajax\Handler;


use Local\Core\Model\Data\TradingPlatformTable;

class TradingPlatform
{
    public static function delete(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $arResult = [];
        switch (\Local\Core\Inner\TradingPlatform\Base::checkUserAccess($args['tp_id'], $GLOBALS['USER']->GetId())) {
            case \Local\Core\Inner\TradingPlatform\Base::ACCESS_TP_IS_MINE:
                $intTpId = $args['tp_id'];
                TradingPlatformTable::delete($intTpId);
                $arResult['result'] = 'SUCCESS';
                break;

            default:
                $arResult['result'] = 'error';
                $arResult['error_text'] = 'Не удалось найти торговую площадку или у Вас нет на нее прав';
                break;
        }

        $response->setContentJson($arResult);
    }

    public static function refreshRow(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $arResult = [];

        $arRequest = $request->getPostList()->toArray();
        $arRequest = array_merge($arRequest['TP_DATA'], $arRequest);

        $obTp = ( new \Local\Core\Inner\TradingPlatform\TradingPlatform );
        try
        {
            $obHandler = $obTp->getHandler($arRequest['HANDLER']);
            $obHandler->fillTradingPlatformData($arRequest);

            foreach ($obHandler->getFields() as $obField)
            {
                if( $obField instanceof \Local\Core\Inner\TradingPlatform\Field\AbstractField && $obField->getRowHash() == $arRequest['LOCAL_CORE_REFRESH_ROW'])
                {
                    $arResult['ROW_HTML'] = $obField->getRow($obField->getRender());
                }
            }
        }
        catch (\Local\Core\Inner\TradingPlatform\Exceptions\TradingPlatformNotFoundException $e)
        {
            $arResult['result'] = 'error';
            $arResult['error_text'] = 'Не удалось загрузить ТП';
        }
        catch (\Local\Core\Inner\TradingPlatform\Exceptions\HandlerNotFoundException $e)
        {
            $arResult['result'] = 'error';
            $arResult['error_text'] = 'Не удалось загрузить обработчик';
        }
        catch (\Throwable $e)
        {
            $arResult['result'] = 'error';
            $arResult['error_text'] = $e->getMessage();
        }

        $response->setContentJson($arResult);
    }

    public static function refreshForm(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $arResult = [];

        $arRequest = $request->getPostList()->toArray();
        $arRequest = array_merge($arRequest['TP_DATA'], $arRequest);

        $obTp = ( new \Local\Core\Inner\TradingPlatform\TradingPlatform );
        try
        {
            $obHandler = $obTp->getHandler($arRequest['HANDLER']);
            $obHandler->fillTradingPlatformData($arRequest);

            $arResult['FORM_HTML'] = '';

            foreach ($obHandler->getFields() as $obField)
            {
                if( $obField instanceof \Local\Core\Inner\TradingPlatform\Field\AbstractField )
                {
                    $arResult['FORM_HTML'] .= $obField->getRow($obField->getRender());
                }
            }
        }
        catch (\Local\Core\Inner\TradingPlatform\Exceptions\TradingPlatformNotFoundException $e)
        {
            $arResult['result'] = 'error';
            $arResult['error_text'] = 'Не удалось загрузить ТП';
        }
        catch (\Local\Core\Inner\TradingPlatform\Exceptions\HandlerNotFoundException $e)
        {
            $arResult['result'] = 'error';
            $arResult['error_text'] = 'Не удалось загрузить обработчик';
        }
        catch (\Throwable $e)
        {
            $arResult['result'] = 'error';
            $arResult['error_text'] = $e->getMessage();
        }

        $response->setContentJson($arResult);
    }
}