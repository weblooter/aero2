<?

class PersonalAsideMenuComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->fillResult();
        $this->includeComponentTemplate();
    }

    private function fillResult()
    {
        $arResult = [
            'ITEMS' => [
                [
                    'LINK' => '/personal/',
                    'TEXT' => 'Рабочий стол',
                    'ICON_CLASS' => 'zmdi zmdi-view-dashboard',
                ]
            ]
        ];

        $arCompanyMenu = [];

        $obCache = \Bitrix\Main\Application::getInstance()->getCache();
        if(
            $obCache->startDataCache(
                60*60*24*7,
                    __METHOD__.__LINE__.'#'.$GLOBALS['USER']->GetID(),
                    \Local\Core\Inner\Cache::getComponentCachePath(['personal.asidemenu'], ['userId='.$GLOBALS['USER']->GetID()])
            )
        )
        {
            $arCompanyMenu = [
                'LINK' => \Local\Core\Inner\Route::getRouteTo('company', 'list'),
                'TEXT' => 'Компании',
                'ICON_CLASS' => 'zmdi zmdi-apps'
            ];


            if( $GLOBALS['USER']->IsAuthorized() )
            {
                $rsCompanyList = \Local\Core\Model\Data\CompanyTable::getList([
                    'filter' => [
                        'USER_OWN_ID' => $GLOBALS['USER']->GetID(),
                        'ACTIVE' => 'Y'
                    ],
                    'select' => ['ID', 'NAME']
                ]);
                $arCompanyIdList = [];
                while ($ar = $rsCompanyList->fetch())
                {
                    $arCompanyIdList[] = $ar['ID'];
                    $arCompanyMenu['CHILDS'][ $ar['ID'] ] = [
                        'LINK' => \Local\Core\Inner\Route::getRouteTo('company', 'detail', ['#COMPANY_ID#' => $ar['ID']]),
                        'TEXT' => $ar['NAME'],
                        'CHILDS' => [
                            '-1' => [
                                'LINK' => \Local\Core\Inner\Route::getRouteTo('store', 'list', ['#COMPANY_ID#' => $ar['ID']]),
                                'TEXT' => 'Магазины',
                                'ICON_CLASS' => 'zmdi zmdi-shopping-cart',
                                'CHILDS' => []
                            ],
                        ]
                    ];
                }

                $rsStoresList = \Local\Core\Model\Data\StoreTable::getList([
                    'filter' => [
                        'COMPANY_ID' => $arCompanyIdList,
                        'ACTIVE' => 'Y'
                    ],
                    'select' => ['ID', 'NAME', 'COMPANY_ID']
                ]);
                while ($ar = $rsStoresList->fetch())
                {
                    $arCompanyMenu['CHILDS'][ $ar['COMPANY_ID'] ]['CHILDS'][-1]['CHILDS'][ $ar['ID'] ] = [
                        'LINK' => \Local\Core\Inner\Route::getRouteTo('store', 'detail', ['#COMPANY_ID#' => $ar['COMPANY_ID'], '#STORE_ID#' => $ar['ID']]),
                        'TEXT' => $ar['NAME'],
                    ];
                }

                foreach ($arCompanyMenu['CHILDS'] as $intCompId => &$ar)
                {
                    $ar['CHILDS'][-1]['CHILDS'][] = [
                        'LINK' => \Local\Core\Inner\Route::getRouteTo('store', 'add', ['#COMPANY_ID#' => $intCompId]),
                        'TEXT' => 'Добавить',
                    ];
                    $ar['CHILDS'][] =[
                        'LINK' => \Local\Core\Inner\Route::getRouteTo('company', 'edit', ['#COMPANY_ID#' => $intCompId]),
                        'TEXT' => 'Изменить',
                    ];
                    $ar['CHILDS'][] = [
                        'LINK' => 'javascript:void(0)',
                        'TEXT' => 'Удалить',
                        'ONCLICK' => 'PersonalAsideMenuComponent.deleteCompany(\''.$intCompId.'\', \''.htmlspecialchars($ar['TEXT']).'\')'
                    ];
                }
                unset($ar);
                unset($arCompanyIdList);
            }

            $arCompanyMenu['CHILDS'][] = [
                'LINK' => \Local\Core\Inner\Route::getRouteTo('company', 'add'),
                'TEXT' => 'Добавить компанию'
            ];

            $obCache->endDataCache($arCompanyMenu);
        }
        else
        {
            $arCompanyMenu = $obCache->getVars();
        }

        $arTools = [
            [
                'LINK' => \Local\Core\Inner\Route::getRouteTo('tools', 'list'),
                'TEXT' => 'Инструменты',
                'ICON_CLASS' => 'zmdi zmdi-ungroup',
                'CHILDS' => [
                    [
                        'LINK' => \Local\Core\Inner\Route::getRouteTo('tools', 'converter'),
                        'TEXT' => 'Конвертер'
                    ]
                ]
            ]
        ];

        $arBalance = [
            [
                'LINK' => \Local\Core\Inner\Route::getRouteTo('balance', 'list'),
                'TEXT' => 'Баланс',
                'ICON_CLASS' => 'zmdi zmdi-money',
                'CHILDS' => [
                    [
                        'LINK' => \Local\Core\Inner\Route::getRouteTo('balance', 'top-up', ['#HANDLER#' => '']),
                        'TEXT' => 'Пополнить баланс'
                    ],
                    [
                        'LINK' => \Local\Core\Inner\Route::getRouteTo('balance', 'list'),
                        'TEXT' => 'Посмотреть историю'
                    ]
                ]
            ]
        ];

        $arResult['ITEMS'] = array_merge($arResult['ITEMS'], [$arCompanyMenu], $arTools, $arBalance);

        $arResult['ITEMS'] = $this->markActiveChain($arResult['ITEMS']);

        $this->arResult = $arResult;
    }

    public static function createLvlItem($arLvl, $intLvlDepth = 0)
    {
        ?>
        <?if( !empty($arLvl['CHILDS']) ):?>
            <li class="navigation__sub <?=( $arLvl['ACTIVE'] ) ? 'navigation__sub--active navigation__active' : ''?>">
                <a href="<?=$arLvl['LINK']?>">
                    <?=( !empty( $arLvl['ICON_CLASS'] ) ? '<i class="'.$arLvl['ICON_CLASS'].'"></i> ' : '' )?><?=$arLvl['TEXT']?>
                </a>
                <ul>
                    <?foreach ($arLvl['CHILDS'] as $arSubLvl):?>
                        <?self::createLvlItem($arSubLvl, ++$intLvlDepth)?>
                    <?endforeach;?>
                </ul>
            </li>
        <?else:?>
            <li class="<?=( $arLvl['ACTIVE'] ) ? 'navigation__active' : ''?>">
                <a href="<?=$arLvl['LINK']?>" <?=( !empty( $arLvl['ONCLICK'] ) ) ? 'onclick="'.$arLvl['ONCLICK'].'"' : ''?> >
                    <?=( !empty( $arLvl['ICON_CLASS'] ) ? '<i class="'.$arLvl['ICON_CLASS'].'"></i> ' : '' )?><?=$arLvl['TEXT']?>
                </a>
            </li>
        <?endif;?>
        <?
    }

    protected function markActiveChain($arLvl, &$backBoolSelected = false)
    {
        $strCurPageDirectory = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestedPageDirectory().'/';
        if( preg_match('/^\/personal\/company\/([0-9]+)\/store\/([0-9]+)\//', $strCurPageDirectory, $matches) )
        {
            $strCurPageDirectory = $matches[0];
        }

        foreach ($arLvl as &$arLvlItem)
        {
            if( !empty( $arLvlItem['CHILDS'] ) )
            {
                $currentBoolSelected = false;
                $arLvlItem['CHILDS'] = $this->markActiveChain($arLvlItem['CHILDS'], $currentBoolSelected);
                if(
                    $currentBoolSelected
                    || $strCurPageDirectory == $arLvlItem['LINK']
                )
                {
                    $backBoolSelected = true;
                    $arLvlItem['ACTIVE'] = true;
                }
            }
            else
            {
                $strLink = $arLvlItem['LINK'];
                if( stripos($strLink, '?') !== false )
                {
                    $strLink = strstr($strLink, '?', true);
                }
                if( $strCurPageDirectory == $strLink )
                {
                    $arLvlItem['ACTIVE'] = true;
                    $backBoolSelected = true;
                }
            }
        }
        unset($arLvlItem);

        return $arLvl;
    }
}