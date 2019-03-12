<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class CustomStepDataTree extends Base
{
    private $strHtml = '';
    private $codeFormated = null;
    private $idLinePrefix = 'CustomStepConditionLayer_';

    private $isAjax = false;
    private $ajaxData = null;
    private $isMultiple = false;

    private $isCustomParam = false;

    /**
     * {@inheritdoc}
     */
    public function getRowHtml()
    {

        $this->codeFormated = preg_replace( '/[^a-zA-Z0-9]/', '-', $this->getCode() );
        $this->checkAjax();

        if ( $this->isAjax )
        {
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
        }
        else
        {
            $this->strHtml = '<tr class="CustomStepConditionLayer_'.$this->codeFormated.'">';
        }

        $this->strHtml .= "<style>
        .condition-block{
            padding: 10px;
            border: 1px solid #dedede;
            border-radius: 3px;
            margin: 10px 0px;
            position: relative;
        }
        .condition-block-title{
            font-weight: bold;
            margin: 0px 0px 3px 0px;
        }
        .condition-block-value{
            font-size: 0px;
        }
        .condition-block-value select,
        .condition-block-value input,
        .condition-block-value textarea{
            display: inline-block;
            vertical-align: top;
            margin: 0px 10px 0px 0px;
        }
        .condition-block-remove{
            position: absolute;
            bottom: 10px;
            right: 10px;
        }
        </style>";

        if ( $this->isCustomParam )
        {
            $this->strHtml .= '
            <td class="adm-detail-content-cell-l">
                <table>
                    <tr>
                        <td>'.( $this->isRequired() ? '<b>' : '' ).$this->getTitle().( $this->isRequired() ? '</b>' : '' ).'</td>
                        <td><input type="text" name="'.$this->getCode().'[tag_name]" value="'.$this->fields[ 'VALUE' ][ 'tag_name' ].'" placeholder="Название тега" /></td>
                    </tr>
                </table>'
                              .'</td><td>';
            $this->fields[ 'CODE' ] = $this->fields[ 'CODE' ].'[tag_value]';
            $this->fields[ 'VALUE' ] = $this->fields[ 'VALUE' ][ 'tag_value' ];
        }
        else
        {
            $this->strHtml .= '<td class="adm-detail-content-cell-l">'
                              .( $this->isRequired() ? '<b>' : '' ).$this->getTitle().( $this->isRequired() ? '</b>' : '' )
                              .'</td><td>';
        }

        try
        {
            $this->showTypeSelect();

            if ( !empty( $this->getValue()[ 'TYPE' ] ) )
            {
                $methodName = 'showTree'.mb_strtoupper( preg_replace( '/[^a-zA-Z]/', '',
                        $this->getValue()[ 'TYPE' ] ) );
                if ( method_exists( $this, $methodName ) )
                {
                    $this->$methodName();
                }
                else
                {
                    throw new \Exception( 'Метод '.__CLASS__.'::'.$methodName.'() не найден!' );
                }
            }

        }
        catch ( \Exception $e )
        {
            ob_start( function ( $buffer ) {
                $this->strHtml .= $buffer;
            } );
            \CAdminMessage::ShowOldStyleError( $e->getMessage() );
            ob_end_flush();
        }

        ob_start( function ( $buffer ) {
            $this->strHtml .= $buffer;
        } );
        ob_end_flush();

        $this->strHtml .= '</td>';
        if ( $this->isAjax )
        {
            echo $this->strHtml;
            die();
        }
        else
        {
            $this->strHtml .= '</tr>';
        }

        return $this->strHtml;
    }

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        return $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return $this->getValue();
    }


    /* ****** */
    /* CUSTOM */
    /* ****** */
    private function checkAjax()
    {

        $obRequest = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        if ( $obRequest->isAjaxRequest() == false )
        {
            return;
        }

        if ( $obRequest->get( 'BLOCK_NAME' ) != $this->idLinePrefix.$this->codeFormated )
        {
            return;
        }

        $this->isAjax = true;
        $arPostData = $obRequest->getPostList()->toArray();
        $this->ajaxData = $arPostData[ 'FORM_DATA' ];

        if ( $obRequest->get( 'ACTION' ) )
        {

            switch ( $obRequest->get( 'ACTION' ) )
            {

                case "getReloadBlock":

                    $arrV = $this->ajaxData;
                    $ar = explode( '][', $this->fields[ "CODE" ] );
                    $ar[ sizeof( $ar ) - 1 ] = str_replace( ']', '', $ar[ sizeof( $ar ) - 1 ] );
                    foreach ( $ar as $val )
                    {
                        $arrV = $arrV[ $val ];
                    }
                    $this->setValue( $arrV );

                    break;

                case "addConditionBlock":

                    $arValues = $this->ajaxData;
                    $ar = explode( '][', $this->fields[ "CODE" ] );
                    $ar[ sizeof( $ar ) - 1 ] = str_replace( ']', '', $ar[ sizeof( $ar ) - 1 ] );
                    foreach ( $ar as $val )
                    {
                        $arValues = $arValues[ $val ];
                    }

                    $arValues[ "CONDITIONS" ][] = [
                        "CONDITION" => [],
                        "TYPE" => "",
                        "RESOURCE" => "",
                        "VALUE" => "",
                    ];
                    $this->setValue( $arValues );
                    break;

                case "removeConditionBlock":

                    $arValues = $this->ajaxData;
                    $ar = explode( '][', $this->fields[ "CODE" ] );
                    $ar[ sizeof( $ar ) - 1 ] = str_replace( ']', '', $ar[ sizeof( $ar ) - 1 ] );
                    foreach ( $ar as $val )
                    {
                        $arValues = $arValues[ $val ];
                    }

                    $key = $obRequest->get( "KEY" );
                    if ( isset( $arValues[ "CONDITIONS" ][ $key ] ) )
                    {
                        unset( $arValues[ "CONDITIONS" ][ $key ] );
                        $arValues[ "CONDITIONS" ] = array_values( $arValues[ "CONDITIONS" ] );
                    }

                    $this->setValue( $arValues );
                    break;
            }
        }
    }

    public function setMultiple( $bool )
    {
        $this->isMultiple = $bool;
        return $this;
    }

    public function setIsCustomParams( $bool )
    {
        $this->isCustomParam = $bool;
        return $this;
    }

    private function SelectBoxFromArray( $code, $ar, $val, $defaultText = '', $setReload = true, $isMultiple = false )
    {
        if ( !$isMultiple )
        {
            if ( $setReload )
            {
                $this->strHtml .= str_replace(
                                      '<select ',
                                      '<select onChange="wblReloadCustomStepCondition.reloadBlock(\''.$this->codeFormated.'\')" ',
                                      SelectBoxFromArray( $code,
                                          ['reference' => array_values( $ar ), 'reference_id' => array_keys( $ar )],
                                          $val, $defaultText )
                                  ).' ';
            }
            else
            {
                $this->strHtml .= SelectBoxFromArray( $code,
                        ['reference' => array_values( $ar ), 'reference_id' => array_keys( $ar )], $val,
                        $defaultText ).' ';
            }
        }
        else
        {
            if ( $setReload )
            {
                $this->strHtml .= str_replace(
                                      '<select ',
                                      '<select onChange="wblReloadCustomStepCondition.reloadBlock(\''.$this->codeFormated.'\')" style="width: auto;" ',
                                      SelectBoxMFromArray( $code.'[]',
                                          ['reference' => array_values( $ar ), 'reference_id' => array_keys( $ar )],
                                          $val, $defaultText )
                                  ).' ';
            }
            else
            {
                $this->strHtml .= str_replace(
                                      '<select ',
                                      '<select style="width: auto;" ',
                                      SelectBoxMFromArray( $code.'[]',
                                          ['reference' => array_values( $ar ), 'reference_id' => array_keys( $ar )],
                                          $val, $defaultText )
                                  ).' ';
            }
        }
    }

    public function getResourceIblockFieldsAndProps( $iblockCode = 'catalog' )
    {
        $arOptions = [];
        foreach ( \Bitrix\Iblock\ElementTable::getMap() as $key => $ob )
        {
            switch ( $key )
            {
                case 'MODIFIED_BY':
                case 'CREATED_BY':
                case 'IBLOCK_ID':
                case 'PREVIEW_TEXT_TYPE':
                case 'DETAIL_TEXT_TYPE':
                case 'SEARCHABLE_CONTENT':
                case 'WF_STATUS_ID':
                case 'WF_PARENT_ELEMENT_ID':
                case 'WF_NEW':
                case 'WF_LOCKED_BY':
                case 'WF_DATE_LOCK':
                case 'WF_COMMENTS':
                case 'IN_SECTIONS':
                case 'TMP_ID':
                case 'IBLOCK':
                case 'WF_PARENT_ELEMENT':
                case 'IBLOCK_SECTION':
                case 'MODIFIED_BY_USER':
                case 'CREATED_BY_USER':
                case 'WF_LOCKED_BY_USER':
                    continue;
                    break;

                default:
                    $arOptions[ $key ] = '['.$key.'] '.$ob->getTitle();
                    break;
            }
        }

        $arOptions[ 'DETAIL_PAGE_URL' ] = '[DETAIL_PAGE_URL] Ссылка на детальную страницу';
        $arOptions[ '1' ] = '-- Свойства --';

        $rs = \CIBlockProperty::GetList(
            ['SORT' => 'ASC', 'ID' => 'ASC'],
            ['IBLOCK_ID' => \Local\Core\Assistant\Iblock\Iblock::getIdByCode( 'catalog', $iblockCode )]
        );
        while ( $arItem = $rs->Fetch() )
        {
            $arOptions[ 'PROPERTY_'.$arItem[ 'CODE' ].'_VALUE' ] = '['.$arItem[ 'CODE' ].'] '.$arItem[ 'NAME' ];
        }

        if ( $iblockCode == 'offers' )
        {
            $arOptions[ '2' ] = '-- Товар --';
            $arOptions[ 'CATALOG_PRICE' ] = 'Цена в регионе, рос. руб.';
            $arOptions[ 'CATALOG_PRICE_WITH_DISCOUNT' ] = 'Цена в регионе с учетом скидок, рос. руб.';
            $arOptions[ 'CATALOG_WEIGHT' ] = 'Вес, г';
            $arOptions[ 'CATALOG_WIDTH' ] = 'Ширина, мм';
            $arOptions[ 'CATALOG_HEIGHT' ] = 'Высота, мм';
            $arOptions[ 'CATALOG_LENGTH' ] = 'Высота, мм';
            $arOptions[ 'CATALOG_VOLUME' ] = 'Габариты, м3';
            $arOptions[ 'CATALOG_AVAILABLE' ] = 'Доступность в регионе';
        }


        $arOptions[ '4' ] = '-- Общие параметры --';
        $arOptions[ 'OTHER_REGION_ID' ] = 'Ид региона';
        $arOptions[ 'OTHER_REGION_CODE' ] = 'Код региона';
        $arOptions[ 'OTHER_REGION_NAME' ] = 'Название региона';
        $arOptions[ 'OTHER_REGION_NAME_1' ] = 'Название региона (родительный падеж)';
        $arOptions[ 'OTHER_REGION_NAME_2' ] = 'Название региона (предложенный падеж)';
        $arOptions[ 'OTHER_REGION_NAME_3' ] = 'Название региона (творительный падеж)';
        $arOptions[ 'OTHER_REGION_NAME_TRANSLITE' ] = 'Название региона (транслит)';
        $arOptions[ 'OTHER_REGION_NAME_TRANSLITE_1' ] = 'Название региона (родительный падеж, транслит)';
        $arOptions[ 'OTHER_REGION_NAME_TRANSLITE_2' ] = 'Название региона (предложенный падеж, транслит)';
        $arOptions[ 'OTHER_REGION_NAME_TRANSLITE_3' ] = 'Название региона (творительный падеж, транслит)';

        return $arOptions;
    }


    /* ****** */
    /* Логика последовательного вывода данных */
    /* ****** */
    /**
     * Вывод селекта выбора типа данных
     */
    private function showTypeSelect()
    {
        $this->SelectBoxFromArray(
            $this->getCode().'[TYPE]',
            [
                'SIMPLE' => 'Свое значение',
                'RESOURCE' => 'Источник данных',
                'LOGIC_CONDITION' => 'Сложное условие'
            ],
            $this->getValue()[ 'TYPE' ] ?? '',
            "-- Выберите тип значения --"
        );
    }

    /**
     * Выбран тип "Свое значение"
     */
    private function showTreeSIMPLE()
    {
        $value = htmlentities( $this->getValue()[ 'VALUE' ], ENT_QUOTES, "UTF-8" );
        $value = htmlspecialcharsbx( $value, ENT_QUOTES );

        $this->strHtml .= ' <textarea name="'.$this->getCode().'[VALUE]" id="'.$this->getCode().'[VALUE]" style="vertical-align: bottom;" cols="53" >'.$value.'</textarea>';
    }

    /**
     * Выбран тип "Источник данных"
     */
    private function showTreeRESOURCE()
    {
        $this->SelectBoxFromArray(
            $this->getCode().'[RESOURCE]',
            [
                'CATALOG' => 'Каталог',
                'OFFER' => 'Торговое предложение'
            ],
            $this->getValue()[ 'RESOURCE' ] ?? '',
            "-- Выберите источник данных --"
        );

        if ( !empty( $this->getValue()[ 'RESOURCE' ] ) )
        {
            switch ( $this->getValue()[ 'RESOURCE' ] )
            {

                case 'CATALOG': // Каталог
                    if ( !\Bitrix\Main\Loader::includeModule( 'iblock' ) )
                    {
                        throw new \Exception( 'Не удалось подключить модуль iblock' );
                    }

                    $this->SelectBoxFromArray(
                        $this->getCode().'[VALUE]',
                        $this->getResourceIblockFieldsAndProps( 'catalog' ),
                        $this->getValue()[ 'VALUE' ] ?? '',
                        "-- Выберите значение --",
                        false,
                        $this->isMultiple
                    );

                    break;

                case 'OFFER': // Торг. предложение

                    if ( !\Bitrix\Main\Loader::includeModule( 'iblock' ) )
                    {
                        throw new \Exception( 'Не удалось подключить модуль iblock' );
                    }

                    $this->SelectBoxFromArray(
                        $this->getCode().'[VALUE]',
                        $this->getResourceIblockFieldsAndProps( 'offers' ),
                        $this->getValue()[ 'VALUE' ] ?? '',
                        "-- Выберите значение --",
                        false,
                        $this->isMultiple
                    );

                    break;

                default:
                    throw new \Exception( 'Логика для ресурса "'.$this->getValue()[ 'RESOURCE' ].'" не описана' );
                    break;
            }
        }
    }

    private function showTreeLOGICCONDITION()
    {
        if ( !\Bitrix\Main\Loader::includeModule( 'iblock' ) )
        {
            throw new \Exception( 'Не удалось подключить модуль iblock' );
        }


        $this->strHtml .= "<div>";

        $arValues = $this->getValue();

        if ( !isset( $arValues[ "CONDITIONS" ] ) || empty( $arValues[ "CONDITIONS" ] ) )
        {
            $arValues[ "CONDITIONS" ] = [
                [
                    "CONDITION" => [],
                    "TYPE" => "",
                    "RESOURCE" => "",
                    "VALUE" => "",
                ]
            ];
        }

        foreach ( $arValues[ "CONDITIONS" ] as $key => $arValue )
        {

            $this->strHtml .= "<div class='condition-block'>";

            $inputCode = $this->getCode()."[CONDITIONS][".$key."]";
            if ( !empty( $arValue[ "CONDITION" ] ) && is_array( $arValue[ "CONDITION" ] ) )
            {
                $obCondValue = new \CCatalogCondTree();
                $obCondValue->Init(
                    BT_COND_MODE_GENERATE,
                    BT_COND_BUILD_CATALOG
                );
                $arValue[ "CONDITION" ] = $obCondValue->Parse( $arValue[ "CONDITION" ] );
                unset( $obCondValue );
            }

            $this->strHtml .= "<div class='condition-block-title'>".( ( $key == 0 ) ? "Если" : "Или если" ).":</div>";
            $obCond = new \CCatalogCondTree();
            $boolCond = $obCond->Init( BT_COND_MODE_DEFAULT, BT_COND_BUILD_CATALOG, [
                "FORM_NAME" => "post_form",
                "CONT_ID" => $inputCode."[CONDITION]",
                "JS_NAME" => "s".md5( $inputCode ),
                "PREFIX" => $inputCode."[CONDITION]",
            ] );
            $this->strHtml .= $obCond->Show( $arValue[ "CONDITION" ] );
            $this->strHtml .= "<div id='".$inputCode."[CONDITION]' style='position: relative; z-index: 1; margin-bottom:10px'></div>";
            unset( $obCond );

            $this->strHtml .= "<div class='condition-block-title'>То:</div>";
            $this->strHtml .= "<div class='condition-block-value'>";
            $this->strHtml .= $this->getResource( $inputCode, $arValue );
            $this->strHtml .= "</div>";

            if ( $key != 0 )
            {
                $this->strHtml .= "<div class='condition-block-remove'><a 
                                            href='javascipt:void(0)' 
                                            id='add_condition' 
                                            class='adm-btn adm-btn-delete' 
                                            onclick='wblReloadCustomStepCondition.removeConditionBlock(\"".$this->codeFormated."\", \"".$key."\")'
                                        >Удалить условие</a></div>";
            }

            $this->strHtml .= "</div>";
        }

        $this->strHtml .= "<div class='condition-block-add'><a 
                                        href='javascipt:void(0)' 
                                        id='add_condition' 
                                        class='adm-btn adm-btn-save' 
                                        onclick='wblReloadCustomStepCondition.addConditionBlock(\"".$this->codeFormated."\")'
                                    >Добавить условие</a></div>";

        $this->strHtml .= "<div class='condition-block'>";
        $this->strHtml .= "<div class='condition-block-title'>Иначе:</div>";
        $this->strHtml .= "<div class='condition-block-value'>";
        $this->strHtml .= $this->getResource( $this->getCode()."[ELSE]", $arValues[ "ELSE" ] );
        $this->strHtml .= "</div>";
        $this->strHtml .= "</div>";


        $this->strHtml .= "</div>";
    }

    private function getResource( $inputCode, $inputValue )
    {

        if ( empty( $inputValue ) )
        {
            $inputValue = [
                "CONDITION" => [],
                "TYPE" => "",
                "RESOURCE" => "",
                "VALUE" => "",
            ];
        }

        $this->SelectBoxFromArray(
            $inputCode.'[TYPE]', [
            'SIMPLE' => 'Свое значение',
            'RESOURCE' => 'Источник данных'
        ],
            $inputValue[ 'TYPE' ] ?? '',
            "-- Выберите тип значения --"
        );

        if ( !empty( $inputValue[ 'TYPE' ] ) )
        {
            switch ( $inputValue[ 'TYPE' ] )
            {
                case "SIMPLE":

                    $this->strHtml .= '&nbsp;<textarea name="'.$inputCode.'[VALUE]" style="vertical-align: bottom;" cols="53" >'.$inputValue[ 'VALUE' ].'</textarea>';
                    break;

                case "RESOURCE":

                    $this->SelectBoxFromArray(
                        $inputCode.'[RESOURCE]', [
                        'CATALOG' => 'Каталог',
                        'OFFER' => 'Торговое предложение'
                    ],
                        $inputValue[ 'RESOURCE' ] ?? '',
                        "-- Выберите источник данных --"
                    );

                    if ( !empty( $inputValue[ 'RESOURCE' ] ) )
                    {
                        switch ( $inputValue[ 'RESOURCE' ] )
                        {
                            // Каталог
                            case 'CATALOG':
                                $this->SelectBoxFromArray(
                                    $inputCode.'[VALUE]',
                                    $this->getResourceIblockFieldsAndProps( 'catalog' ),
                                    $inputValue[ 'VALUE' ] ?? '',
                                    "-- Выберите значение --",
                                    true,
                                    $this->isMultiple
                                );
                                break;
                            // Торг. предложение
                            case 'OFFER':
                                $this->SelectBoxFromArray(
                                    $inputCode.'[VALUE]',
                                    $this->getResourceIblockFieldsAndProps( 'offers' ),
                                    $inputValue[ 'VALUE' ] ?? '',
                                    "-- Выберите значение --",
                                    true,
                                    $this->isMultiple
                                );
                                break;
                        }
                    }
                    break;
            }
        }
    }
}