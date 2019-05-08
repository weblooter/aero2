<?php


namespace Local\Core\Inner\Client;


/**
 * Клиент рекаптчи гугла
 *
 * @package Local\Core\Inner\Client
 */
class Recaptcha
{
    /**
     * Производит валидацию рекаптчи
     *
     * @param string $strRequest Полученая строка, которую нужно сверить
     *
     * @return bool
     */
    static function validate($strRequest)
    {
        $boolRet = false;

        $obHttpClient = new \Bitrix\Main\Web\HttpClient;
        $obHttpClient->post( 'https://www.google.com/recaptcha/api/siteverify', [
            'secret' => \Bitrix\Main\Config\Configuration::getInstance()->get('recaptcha')['secret_key'],
            'response' => $strRequest
        ]);

        if( $obHttpClient->getStatus() == 200 )
        {
            $ar = json_decode($obHttpClient->getResult(), true);

            if( $ar['success'] > 0)
            {
                $boolRet = true;
            }
        }

        return $boolRet;
    }
}