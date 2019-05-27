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
            $arResult['PREVIEW_PICTURE'] = \CFile::GetPath($ar['PREVIEW_PICTURE']);
        }
    }
}