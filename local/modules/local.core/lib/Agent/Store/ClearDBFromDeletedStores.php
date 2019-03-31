<?php

namespace Local\Core\Agent\Store;


/**
 * Агент запускет процесс очистки базы от удаленных магазинов
 *
 * @package Local\Core\Agent\Store
 */
class ClearDBFromDeletedStores extends \Local\Core\Agent\Base
{
    protected static function execute()
    {
        \Local\Core\Inner\Store\Cleaner::clearDBFromDeletedStores();
    }
}