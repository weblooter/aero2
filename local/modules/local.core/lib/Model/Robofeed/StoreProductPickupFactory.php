<?

namespace Local\Core\Model\Robofeed;

use \Bitrix\Main\ORM\Fields;

/**
 * Фабрика товаров
 *
 * @package Local\Core\Model\Robofeed
 */
class StoreProductPickupFactory
{
    public static function factory($intVersion)
    {
        switch ($intVersion) {
            case '1':
                return new \Local\Core\Model\Robofeed\V1\StoreProductPickupTable();
                break;
        }
    }
}