<?

namespace Local\Core\Model\Robofeed;

use \Bitrix\Main\ORM\Fields;

/**
 * Фабрика категорий
 *
 * @package Local\Core\Model\Robofeed
 */
class StoreCategoryFactory
{
    public static function factory($intVersion)
    {
        switch($intVersion)
        {
            case '1':
                return new \Local\Core\Model\Robofeed\V1\StoreCategoryTable();
                break;
        }
    }
}