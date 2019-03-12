<?
if ( !empty( $arResult[ 'FIELDS' ][ 'COMPANY_NAME_SHORT' ][ 'VALUE' ] ) )
{
    $GLOBALS[ 'APPLICATION' ]->SetTitle( 'Редактирование компании '.$arResult[ 'FIELDS' ][ 'COMPANY_NAME_SHORT' ][ 'VALUE' ] );
    $GLOBALS[ 'APPLICATION' ]->SetPageProperty( 'title',
        'Редактирование компании '.$arResult[ 'FIELDS' ][ 'COMPANY_NAME_SHORT' ][ 'VALUE' ] );
    $GLOBALS[ 'APPLICATION' ]->AddChainItem( 'Редактирование компании '.$arResult[ 'FIELDS' ][ 'COMPANY_NAME_SHORT' ][ 'VALUE' ] );
}