<?

namespace Local\Core\Inner\Bill;

/**
 * Класс для работы со счетами
 *
 * @package Local\Core\Inner\Bill
 */
class Base
{

    /**
     * Метод создает ACCOUNT_NUMBER счета по его параметрам
     *
     * @param array $arFields Поля счета
     *
     * @return string
     */
    public static function createAccountNumber($arFields)
    {
        $strTimestamp = 0;
        if( $arFields['DATE_CREATE'] instanceof \Bitrix\Main\Type\DateTime )
        {
            $strTimestamp = $arFields['DATE_CREATE']->getTimestamp();
        }
        else
        {
            $strTimestamp = strtotime('now');
        }
        return date('Ymd', $strTimestamp).'_'.$arFields['COMPANY_ID'];
    }
}