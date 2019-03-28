<?php

namespace Local\Core\Agent\Robofeed;


/**
 * Удаляет старые данные о конветации
 *
 * @package Local\Core\Agent\Robofeed\Converter
 */
class DeleteOldConvert extends \Local\Core\Agent\Base
{
    protected static function execute()
    {
        \Local\Core\Inner\Robofeed\Converter\Base::deleteOldCovert();
    }
}