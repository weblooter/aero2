<?php

namespace Local\Core\Agent\Store;

/**
 * Агент старые удаляет диактививаронные магазины
 *
 * @package Local\Core\Agent\Store
 */
class DeleteDeactivatedStores extends \Local\Core\Agent\Base
{
    protected static function execute()
    {
        \Local\Core\Inner\Store\Cleaner::deleteDeactivatedStores();
    }
}