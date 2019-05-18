<?

class PersonalSupportNotificationComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->fillResult();
        $this->includeComponentTemplate();
    }

    protected function fillResult()
    {
        $arResult = [];


        $rsSupports = \Local\Core\Model\Data\SupportTable::getList([
            'filter' => [
                'USER_ID' => $GLOBALS['USER']->GetID(),
                'ACTIVE' => 'Y'
            ],
            'order' => ['DATE_CREATE' => 'DESC'],
            'select' => [
                'ID',
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
                'ID' => $ob->get('ID')
            ];
            foreach ($ob->get('LAST_COMMENT') as $obComment)
            {
                $arItem['LAST_COMMENT']['OWN'] = $obComment->get('OWN');
                $arItem['LAST_COMMENT']['MSG'] = $obComment->get('MSG');
                $arItem['LAST_COMMENT']['ACTIVE'] = $obComment->get('ACTIVE');
                $arItem['LAST_COMMENT']['DATE_CREATE'] = $obComment->get('DATE_CREATE');
            }

            $arResult['ITEMS'][$arItem['ID']] = $arItem;
        }

        if( !empty( $arResult['ITEMS'] ) )
        {
            foreach ($arResult['ITEMS'] as $k => $v)
            {
                if( $v['LAST_COMMENT']['OWN'] != 'AD' || $v['LAST_COMMENT']['ACTIVE'] != 'N' )
                {
                    unset($arResult['ITEMS'][$k]);
                }
            }
        }

        $this->arResult = $arResult;
    }
}