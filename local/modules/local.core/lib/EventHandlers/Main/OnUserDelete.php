<?php


namespace Local\Core\EventHandlers\Main;


class OnUserDelete
{

    /**
     * Удаление компаний пользователя.<br/>
     * Удаление логов баланса пользователя.<br/>
     *
     * @param $intUserId
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function deleteCompanies($intUserId)
    {
        /* ****************************** */
        /* Удаление компаний пользователя */
        /* ****************************** */

        $rsCompanies = \Local\Core\Model\Data\CompanyTable::getList([
            'filter' => ['USER_OWN_ID' => $intUserId],
            'select' => ['ID']
        ]);

        while ($ar = $rsCompanies->fetch())
        {
            \Local\Core\Model\Data\CompanyTable::delete($ar['ID']);
        }

        /* *********************************** */
        /* Удаление логов баланса пользователя */
        /* *********************************** */
        $rsBalance = \Local\Core\Model\Data\BalanceLogTable::getList([
            'filter' => ['USER_ID' => $intUserId],
            'select' => ['ID']
        ]);
        while ($ar = $rsBalance->fetch())
        {
            \Local\Core\Model\Data\BalanceLogTable::delete($ar['ID']);
        }
    }
}