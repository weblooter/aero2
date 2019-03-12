<?
if ( !empty( $arResult[ 'COMPANY' ][ 'COMPANY_NAME_SHORT' ] ) )
{
    $GLOBALS[ 'APPLICATION' ]->SetTitle( $arResult[ 'COMPANY' ][ 'COMPANY_NAME_SHORT' ] );
    $GLOBALS[ 'APPLICATION' ]->SetPageProperty( 'title', $arResult[ 'COMPANY' ][ 'COMPANY_NAME_SHORT' ] );
    $GLOBALS[ 'APPLICATION' ]->AddChainItem( $arResult[ 'COMPANY' ][ 'COMPANY_NAME_SHORT' ] );
}