<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class Location extends Base
{

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        ob_start();
        $GLOBALS["APPLICATION"]->IncludeComponent("bitrix:sale.location.selector.search", "", Array(
                "COMPONENT_TEMPLATE" => ".default",
                "ID" => '',
                "CODE" => $this->getValue(),
                "INPUT_NAME" => $this->getCode(),
                "PROVIDE_LINK_BY" => "code",
                "JSCONTROL_GLOBAL_ID" => "",
                "JS_CALLBACK" => "",
                "FILTER_BY_SITE" => "Y",
                "SHOW_DEFAULT_LOCATIONS" => "Y",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "FILTER_SITE_ID" => "s1",
                "INITIALIZE_BY_GLOBAL_EVENT" => "",
                "SUPPRESS_ERRORS" => "N"
            ));
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        $location = [];

        if( !empty($this->getValue()) )
        {

            try
            {
                $resLocation = \Bitrix\Sale\Location\LocationTable::getList([
                    'filter' => [
                        '=CODE' => $this->getValue(),
                        '=PARENTS.NAME.LANGUAGE_ID' => LANGUAGE_ID,
                        '=PARENTS.TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
                    ],
                    'select' => [
                        'I_NAME_RU' => 'PARENTS.NAME.NAME',
                    ],
                    'order' => [
                        'PARENTS.DEPTH_LEVEL' => 'asc'
                    ]
                ]);

                while( $locationItem = $resLocation->fetch() )
                {
                    $location[] = $locationItem["I_NAME_RU"];
                }
            }
            catch( \Exception $e )
            {

            }
        }

        return implode(" / ", $location);
    }
}
