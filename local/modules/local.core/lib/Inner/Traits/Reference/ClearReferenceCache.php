<?php

namespace Local\Core\Inner\Traits\Reference;


/**
 * Трейт для Model\Reference для чистки кэша
 *
 * @package Local\Core\Inner\Traits\Reference
 */
trait ClearReferenceCache
{
    public static function clearReferenceCache()
    {
        \Local\Core\Inner\Cache::deleteCache([
            'Model',
            'Reference',
            array_slice(explode('\\', static::class), -1)
        ]);
    }
}