<?

class PersonalCompanyListComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult = $this->__getResult();

        $this->includeComponentTemplate();
    }

    public function onPrepareComponentParams( $arParams )
    {
        if ( $arParams[ 'ELEM_COUNT' ] < 1 )
        {
            $arParams[ 'ELEM_COUNT' ] = 10;
        }

        return $arParams;
    }

    private function __getResult()
    {
        $obCache = \Bitrix\Main\Application::getInstance()->getCache();
        $arResult = [];

        $nav = new \Bitrix\Main\UI\PageNavigation( "company-nav" );
        $nav->allowAllRecords( true )
            ->setPageSize( $this->arParams[ 'ELEM_COUNT' ] )
            ->initFromUri();

        if ( $obCache->startDataCache(
            ( 60 * 60 * 24 * 7 ),
            md5( __METHOD__.'_user_id='.$GLOBALS[ 'USER' ]->GetID().'_page='.$nav->getCurrentPage().'&offset='.$nav->getOffset() ),
            \Local\Core\Assistant\Cache::getComponentCachePath( 'personal.company.list',
                ['user_id='.$GLOBALS[ 'USER' ]->GetID(), 'page='.$nav->getCurrentPage().'&offset='.$nav->getOffset()]
            ) ) )
        {


            $rs = \Local\Core\Model\Data\CompanyTable::getList( [
                'filter' => [
                    'USER_OWN_ID' => $GLOBALS[ 'USER' ]->GetID()
                ],
                'order' => ['DATE_CREATE' => 'DESC'],
                'select' => [
                    'ID',
                    'ACTIVE',
                    'DATE_CREATE',
                    'VERIFIED',
                    'VERIFIED_NOTE',
                    'COMPANY_INN',
                    'COMPANY_NAME_SHORT',
                ],
                "count_total" => true,
                "offset" => $nav->getOffset(),
                "limit" => $nav->getLimit(),
            ] );
            if ( $rs->getSelectedRowsCount() < 1 )
            {
                $obCache->abortDataCache();
                $arResult[ 'ITEMS' ] = [];
            }
            else
            {
                $nav->setRecordCount( $rs->getCount() );

                while ( $ar = $rs->fetch() )
                {
                    $arResult[ 'ITEMS' ][ $ar[ 'ID' ] ] = $ar;
                }
                $arResult[ 'NAV_OBJ' ] = $nav;

                $obCache->endDataCache( $arResult );
            }
        }
        else
        {
            $arResult = $obCache->getVars();
        }

        return $arResult;
    }
}