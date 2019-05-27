<?php
if( !empty( $arResult['ID'] ) && !empty( $arResult['IBLOCK_ID'] ) )
{
    \CModule::IncludeModule('iblock');
    $rsElem = \Bitrix\Iblock\ElementTable::getByPrimary($arResult['ID'], ['select' => ['PREVIEW_PICTURE']]);
    if( $rsElem->getSelectedRowsCount() > 0 )
    {
        $ar = $rsElem->fetch();
        if( $ar['PREVIEW_PICTURE'] > 0 )
        {
            $arResult['PREVIEW_PICTURE'] = \CFile::ResizeImageGet($ar['PREVIEW_PICTURE'], ['width' => 9999, 'height' => 400], BX_RESIZE_IMAGE_EXACT, false, false, false, 75);
            $arResult['PREVIEW_PICTURE'] = $arResult['PREVIEW_PICTURE']['src'];
        }
    }
}