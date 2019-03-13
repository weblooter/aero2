<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class Matrix2 extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
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
											name: '".$this->getCode()."[' + (cntVariants + 1) + '][0]',
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
											name: '".$this->getCode()."[' + (cntVariants + 1) + '][1]',
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
        </script>
		";

        $values = $this->getValue();

        $result .= "<table id='js_radio_variants'>";

        $lastKey = 0;
        if( !empty($values) )
        {
            foreach( $values as $key => $value )
            {
                $result .= "<tr class='js-variant'>";
                $result .= "<td><input type='text' name='".$this->getCode()."[".$lastKey."][0]' value='".htmlspecialchars($value[0])."'/></td>";
                $result .= "<td><input type='text' name='".$this->getCode()."[".$lastKey."][1]' value='".htmlspecialchars($value[1])."'/></td>";
                $result .= "</tr>";

                $lastKey++;
            }
        }

        $result .= "<tr class='js-variant'>";
        $result .= "<td><input type='text' name='".$this->getCode()."[".$lastKey."][0]' value=''/></td>";
        $result .= "<td><input type='text' name='".$this->getCode()."[".$lastKey."][1]' value=''/></td>";
        $result .= "</tr>";

        $result .= "</table>";

        $result .= "<input type='button'  value='Добавить' class='js-add-radio-variant'/>";

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        $result = "<table id='js_radio_variants'>";

        if( !empty($this->getValue()) )
        {
            foreach( $this->getValue() as $key => $value )
            {

                $result .= "<tr class='js-variant'>";
                $result .= "<td>".strip_tags($key)."</td>";
                $result .= "<td>".strip_tags($value)."</td>";
                $result .= "</tr>";
            }
        }

        $result .= "</table>";

        return $result;
    }


}
