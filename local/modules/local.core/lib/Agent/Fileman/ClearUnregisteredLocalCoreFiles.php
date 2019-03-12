<?php
namespace Local\Core\Agent\Fileman;

/**
 * Агент для чистки файловой структуры, прикрепленных к модулю local.core, но не зарегистрированных нигде
 * @package Local\Core\Agent\File
 */
class ClearUnregisteredLocalCoreFiles extends \Local\Core\Agent\Base
{
    protected static function execute()
    {
        \Local\Core\Inner\Fileman\Cleaner::clearUnregisteredLocalCoreFiles();
    }
}