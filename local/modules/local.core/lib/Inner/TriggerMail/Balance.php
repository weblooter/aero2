<?

namespace Local\Core\Inner\TriggerMail;


class Balance
{
    public static function balanceTopUpped($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_BALANCE_TOP_UPPED",
            "LID" => "s1",
            "C_FIELDS" => array(
                'EMAIL' => $arFields['EMAIL'],
                'SUMM_FORMAT' => $arFields['SUMM_FORMAT'],
                'TOTAL_SUMM_FORMAT' => $arFields['TOTAL_SUMM_FORMAT'],
                'NOTE' => $arFields['NOTE']
            )
        ));
    }

    public static function balancePayedFromAccount($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_BALANCE_PAYED_FROM_ACCOUNT",
            "LID" => "s1",
            "C_FIELDS" => array(
                'EMAIL' => $arFields['EMAIL'],
                'SUMM_FORMAT' => $arFields['SUMM_FORMAT'],
                'TOTAL_SUMM_FORMAT' => $arFields['TOTAL_SUMM_FORMAT'],
                'NOTE' => $arFields['NOTE']
            )
        ));
    }
}