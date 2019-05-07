<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$arTariffs = [];
$rs = \Local\Core\Model\Data\TariffTable::getList([
    'filter' => [
        'ACTIVE' => 'Y',
        'TYPE' => 'PUB',
        [
            'LOGIC' => 'OR',
            ['DATE_ACTIVE_TO' => false],
            ['>DATE_ACTIVE_TO' => new \Bitrix\Main\Type\DateTime]
        ],
        [
            'LOGIC' => 'OR',
            ['DATE_ACTIVE_FROM' => false],
            ['<DATE_ACTIVE_FROM' => new \Bitrix\Main\Type\DateTime]
        ]
    ],
    'order' => ['CODE' => 'ASC'],
    'select' => ['CODE', 'NAME']
]);
while ($ar = $rs->fetch())
{
    $arTariffs[ $ar['CODE'] ] = $ar['NAME'].' ['.$ar['CODE'].']';
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        'SHOW_TARIFFS' => [
            'PARENT' => 'BASE',
            'NAME' => 'Выводимые тарифы',
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'VALUES' => $arTariffs,
            'SIZE' => 15
        ]
    )
);
?>
