<?

class PersonalSupportAdminComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        if( !$GLOBALS['USER']->IsAdmin() )
        {
            \Bitrix\Main\Loader::includeModule('iblock');
            \Bitrix\Iblock\Component\Tools::process404('', true, true, true, "");
        }
        else
        {
            $this->fillResult();
            $this->includeComponentTemplate();
        }
    }

    public function onPrepareComponentParams($arParams)
    {
        if( strlen(trim($arParams['SUPPORT_ID'])) == 0 )
        {
            $arParams['SUPPORT_ID'] = -1;
        }

        return $arParams;
    }

    protected function fillResult()
    {
        $arResult = [];

        $arUser = \Bitrix\Main\UserTable::getByPrimary($GLOBALS['USER']->GetID(), ['select' => ['NAME', 'LAST_NAME', 'EMAIL']])->fetch();
        $arUser['IMG'] = SITE_TEMPLATE_PATH.'/assets/img/user-image.png';

        $arResult['USER'] = $arUser;


        $rsSupports = \Local\Core\Model\Data\SupportTable::getList([
            'filter' => ['ACTIVE' => 'Y'],
            'order' => ['ID' => 'DESC'],
            'select' => [
                'ID',
                'DATE_CREATE',
                'ACTIVE',
                'LAST_COMMENT'
            ],
            'runtime' => [
                new \Bitrix\Main\ORM\Fields\Relations\OneToMany(
                    'LAST_COMMENT',
                    \Local\Core\Model\Data\SupportMessageTable::class,
                    'SUPPORT_DATA'
                )
            ]
        ]);
        while ($ob = $rsSupports->fetchObject())
        {
            $arItem = [
                'ID' => $ob->get('ID'),
                'DATE_CREATE' => $ob->get('DATE_CREATE'),
                'ACTIVE' => $ob->get('ACTIVE'),
            ];
            foreach ($ob->get('LAST_COMMENT') as $obComment)
            {
                $arItem['LAST_WRITER'] = $obComment->get('OWN');
            }

            $arResult['SUPPORT_LIST'][$arItem['ID']] = $arItem;
        }

        if( $this->arParams['SUPPORT_ID'] > 0 && in_array($this->arParams['SUPPORT_ID'], array_keys($arResult['SUPPORT_LIST'])) )
        {
            $arResult['SUPPORT_MSG'] = \Local\Core\Model\Data\SupportMessageTable::getList([
                'filter' => [
                    'SUPPORT_ID' => $this->arParams['SUPPORT_ID']
                ],
                'order' => ['DATE_CREATE' => 'ASC'],
                'select' => [
                    'ID',
                    'DATE_CREATE',
                    'ACTIVE',
                    'OWN',
                    'MSG'
                ]
            ])->fetchAll();
        }

        $this->arResult = $arResult;
    }
}