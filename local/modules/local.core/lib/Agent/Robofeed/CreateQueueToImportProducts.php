<?php

namespace Local\Core\Agent\Robofeed;

/**
 * Создает очередь на актуализацию товаров в магазинах по робофиду
 *
 * @package Local\Core\Agent\Robofeed
 */
class CreateQueueToImportProducts extends \Local\Core\Agent\Base
{
    protected static function execute()
    {
        \Local\Core\Inner\Robofeed\ImportData::createQueueToImportProducts();
    }
}