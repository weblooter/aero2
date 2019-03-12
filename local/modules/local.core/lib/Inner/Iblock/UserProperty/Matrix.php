<?php

namespace Local\Core\Inner\Iblock\UserProperty;

class Matrix
{
    public static function OnBeforeSave( $arUserField, $value )
    {
        $arSave = null;
        foreach ( $value as $k => $v )
        {
            $v = array_map( 'trim', $v );
            $v = array_filter( $v );
            if ( count( $v ) > 0 )
            {
                $arSave[] = $v;
            }
        }

        $value = is_array( $arSave ) ? json_encode( $arSave ) : null;
        return $value;

    }

    public static function GetDBColumnType( $arUserField )
    {
        global $DB;
        switch ( strtolower( $DB->type ) )
        {
            case "mysql":
                return "text";
            case "oracle":
                return "varchar2(2000 char)";
            case "mssql":
                return "varchar(2000)";
        }
    }

    public static function GetFilterData( $arUserField, $arHtmlControl )
    {
        return array(
            "id" => $arHtmlControl[ "ID" ],
            "name" => $arHtmlControl[ "NAME" ],
            "type" => "number",
            "filterable" => ""
        );
    }

    public static function OnSearchIndex( $arUserField )
    {
        return null;
    }

    public static function GetFilterHTML( $arUserField, $arHtmlControl )
    {

        throw new \Exception( 'Add code' );

    }

    public static function GetAdminListViewHTML( $arUserField, $arHtmlControl )
    {
        $result = "<table data-idasf='js_radio_variants'>";

        if ( !empty( $arHtmlControl[ 'VALUE' ] ) )
        {
            $val = json_decode( htmlspecialcharsback( $arHtmlControl[ 'VALUE' ] ), true );
            foreach ( $val ?? [] as $key => $value )
            {
                $result .= "<tr class='js-variant'>";
                $result .= "<td>".strip_tags( $value[ 0 ] )."</td>";
                $result .= "<td>".strip_tags( $value[ 1 ] )."</td>";
                $result .= "</tr>";
            }
        }

        $result .= "</table>";
        return $result;

    }

    public static function GetEditFormHTML( $arUserField, $arHtmlControl )
    {
        $result = "
        <script>
			BX.ready(function (){
				
                BX.bindDelegate(document.body, 'click', {className: 'js-add-radio-variant' }, function(e){
                        
                    var table = BX('js_radio_variants'),
                        tbody = BX.findChild(table, {tag: 'tbody'}, true);
                            
					
					var rows = BX.findChildren(table, {
							tag: 'tr',
							'className': 'js-variant'
						}, true),
						cntVariants = rows.length;

					var trField = BX.create('TR', {
						attrs: {
							className: 'js-variant'
						},
						children: [
							BX.create('TD', {
								children: [
									BX.create('input', {
										props: {
											type: 'text',
											name: '".$arUserField[ 'FIELD_NAME' ]."[' + (cntVariants + 1) + '][0]',
											value: ''
										}
									})
								]
							}),
							BX.create('TD', {
								children: [
									BX.create('input', {
										props: {
											type: 'text',
											name: '".$arUserField[ 'FIELD_NAME' ]."[' + (cntVariants + 1) + '][1]',
											value: ''
										}
									})
								]
							}),
						]
					});

					BX.append(trField, tbody);
                });
			});
			
			function ufFilterRulesSwitch(element, direction){
			    var row = element.parentNode.parentNode.rowIndex;
			    var current = element.parentNode.parentNode;
			    if (direction === 'up') {
	                if (element.parentNode.parentNode.previousSibling === null){
	                    return;
	                }
	                var change = element.parentNode.parentNode.previousSibling;
	                row--;
                }
                if (direction === 'down') {
	                if (element.parentNode.parentNode.nextSibling === null){
	                    return;
	                }
	                var change = element.parentNode.parentNode.nextSibling;
	                row++;
                }
                currentName0 = current.getElementsByTagName('input').item(0).getAttribute('name');
                ccurrentName1 = current.getElementsByTagName('input').item(1).getAttribute('name');
                changeName0 = change.getElementsByTagName('input').item(0).getAttribute('name');
                changeName1 = change.getElementsByTagName('input').item(1).getAttribute('name');
                
                current.getElementsByTagName('input').item(0).setAttribute('name', changeName0);
                current.getElementsByTagName('input').item(1).setAttribute('name', changeName1);
                change.getElementsByTagName('input').item(0).setAttribute('name', currentName0);
                change.getElementsByTagName('input').item(1).setAttribute('name', ccurrentName1);
                if (direction == 'up'){
                    change.parentNode.insertBefore(current, change);
                }
                if (direction == 'down'){
                    current.parentNode.insertBefore(change, current);
                }
                
			}
        </script>
		";

        $values = self::extract( $arUserField[ 'VALUE' ] );

        $result .= "<table id='js_radio_variants'>";

        $lastKey = 0;
        if ( !empty( $values ) )
        {
            foreach ( $values as $key => $value )
            {
                $result .= "<tr class='js-variant' data-row=\"$lastKey\">";
                $result .= "<td><big onclick='ufFilterRulesSwitch(this, \"up\");'> ↑ </big><input type='text' name='".$arUserField[ 'FIELD_NAME' ]."[".$lastKey."][0]' value='".htmlspecialchars( $value[ 0 ] )."'/></td>";
                $result .= "<td><input type='text' name='".$arUserField[ 'FIELD_NAME' ]."[".$lastKey."][1]' value='".htmlspecialchars( $value[ 1 ] )."'/><big onclick='ufFilterRulesSwitch(this, \"down\");'> ↓ </big></td>";
                $result .= "</tr>";

                $lastKey++;
            }
        }

        $result .= "<tr class='js-variant'>";
        $result .= "<td><input type='text' name='".$arUserField[ 'FIELD_NAME' ]."[".$lastKey."][0]' value=''/></td>";
        $result .= "<td><input type='text' name='".$arUserField[ 'FIELD_NAME' ]."[".$lastKey."][1]' value=''/></td>";
        $result .= "</tr>";

        $result .= "</table>";

        $result .= "<input type='button'  value='Добавить' class='js-add-radio-variant'/>";

        return $result;
    }

    public static function extract( $value )
    {
        $value = json_decode( $value, true );
        return ( is_array( $value ) ) ? $value : [];
    }
}
