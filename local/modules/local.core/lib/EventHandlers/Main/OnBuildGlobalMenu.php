<?php

namespace Local\Core\EventHandlers\Main;

class OnBuildGlobalMenu
{
    public static function addGlobalMenu(&$arGlobalMenu, &$arModuleMenu)
    {
        if(!isset($arGlobalMenu['global_menu_local_core'])){
            $arGlobalMenu['global_menu_local_core'] = array(
                'menu_id' => 'global_menu_local_core',
                'text' => '\ssssssLocal\Core',
                'title' => '\ssssssLocal\Core',
                'sort' => 1000,
                'items_id' => 'global_menu_local_core_items',
            );
        }
    }
}