<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Робофид.ру - Справочники");
$APPLICATION->SetPageProperty("TITLE", "Робофид.ру - Справочники");
$APPLICATION->SetPageProperty("description", "Робофид.ру - справочники по валютам, странами и единицам измерений для Робофид XML");
?>
    <h1>Справочники</h1>
    <ul>
        <li><a href="/development/references/#measure">Справочник единиц измерений</a></li>
        <li><a href="/development/references/#currency">Справочник валют</a></li>
        <li><a href="/development/references/#country">Справочник стран</a></li>
    </ul>

    <a name="measure"></a>
    <h3>Справочник единиц измерений</h3>
    <table class="table table-striped">
        <thead>
        <tr>
            <?
            foreach( \Local\Core\Model\Reference\MeasureTable::getMap() as $obFields )
            {
                if( $obFields instanceof \Bitrix\Main\ORM\Fields\ScalarField)
                {
                    switch($obFields->getColumnName())
                    {
                        case 'NAME':
                        case 'CODE':
                        case 'GROUP':
                            ?><th><?=$obFields->getTitle()?></th><?
                            break;
                    }
                }
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?
        $rs = \Local\Core\Model\Reference\MeasureTable::getList([
            'order' => ['GROUP' => 'ASC', 'SORT' => 'ASC'],
            'select' => ['NAME', 'CODE', 'GROUP']
        ]);
        while($ar = $rs->fetch())
        {
            ?>
            <tr>
                <td><?=$ar['NAME']?></td>
                <td><?=$ar['CODE']?></td>
                <td><?=\Local\Core\Model\Reference\MeasureTable::getEnumFieldHtmlValues('GROUP')[ $ar['GROUP'] ]?></td>
            </tr>
            <?
        }
        ?>
        </tbody>
    </table>

    <a name="currency"></a>
    <h3>Справочник валют</h3>
    <table class="table table-striped">
        <thead>
        <tr>
            <?
            foreach( \Local\Core\Model\Reference\CurrencyTable::getMap() as $obFields )
            {
                if( $obFields instanceof \Bitrix\Main\ORM\Fields\ScalarField)
                {
                    switch($obFields->getColumnName())
                    {
                        case 'NAME':
                        case 'CODE':
                        case 'NUMBER_OF_CURRENCY':
                            ?><th><?=$obFields->getTitle()?></th><?
                            break;
                    }
                }
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?
        $rs = \Local\Core\Model\Reference\CurrencyTable::getList([
            'order' => ['SORT' => 'ASC', 'NAME' => 'ASC'],
            'select' => ['NAME', 'CODE', 'NUMBER_OF_CURRENCY']
        ]);
        while($ar = $rs->fetch())
        {
            ?>
            <tr>
                <td><?=$ar['NAME']?></td>
                <td><?=$ar['CODE']?></td>
                <td><?=$ar['NUMBER_OF_CURRENCY']?></td>
            </tr>
            <?
        }
        ?>
        </tbody>
    </table>

    <a name="country"></a>
    <h3>Справочник стран</h3>
    <table class="table table-striped">
        <thead>
        <tr>
            <?
            foreach( \Local\Core\Model\Reference\CountryTable::getMap() as $obFields )
            {
                if( $obFields instanceof \Bitrix\Main\ORM\Fields\ScalarField)
                {
                    switch($obFields->getColumnName())
                    {
                        case 'NAME':
                        case 'CODE':
                            ?><th><?=$obFields->getTitle()?></th><?
                            break;
                    }
                }
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?
        $rs = \Local\Core\Model\Reference\CountryTable::getList([
            'order' => ['SORT' => 'ASC', 'NAME' => 'ASC'],
            'select' => ['NAME', 'CODE']
        ]);
        while($ar = $rs->fetch())
        {
            ?>
            <tr>
                <td><?=$ar['NAME']?></td>
                <td><?=$ar['CODE']?></td>
            </tr>
            <?
        }
        ?>
        </tbody>
    </table>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>