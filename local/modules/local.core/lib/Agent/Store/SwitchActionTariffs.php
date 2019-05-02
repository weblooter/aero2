<?php

namespace Local\Core\Agent\Store;

/**
 * Агент переключает у магазинов истекшиеся тарифы
 *
 * @package Local\Core\Agent\Store
 */
class SwitchActionTariffs extends \Local\Core\Agent\Base
{
    protected static function execute()
    {
        \Local\Core\Inner\Store\SwitchActionTariffs::execute();
    }
}