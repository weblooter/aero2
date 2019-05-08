<?php

namespace Local\Core\Ajax\Handler;

class SystemUser
{

    /**
     * Попытка автризации
     *
     * @param \Bitrix\Main\HttpRequest                  $request
     * @param \Local\Core\Inner\BxModified\HttpResponse $response
     * @param                                           $args
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\ArgumentTypeException
     */
    public static function tryAuth(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $aResult = [];
        try
        {
            if( !\Local\Core\Inner\Client\Recaptcha::validate($request->getPost('g-recaptcha-response')) )
            {
                throw new \Exception('Вы не прошли проверку на робота. Попробуйте позже.');
            }

            $strLogin = trim($request->getPost('login'));
            $strPassword = trim($request->getPost('password'));

            if(
                empty($strLogin)
                || empty($strPassword)
            )
            {
                throw new \Exception('Для авторизации необходимо заполнить E-mail и пароль');
            }

            $arAuthResult = $GLOBALS['USER']->Login($strLogin, $strPassword, "N");
            if( $arAuthResult === true )
            {
                $aResult['result'] = 'success';
            }
            else
            {
                throw new \Exception($arAuthResult['MESSAGE']);
            }

        }
        catch (\Exception $e)
        {
            $aResult['result'] = 'error';
            $aResult['error_text'] = $e->getMessage();
        }

        $response->setContentJson($aResult);
    }

    /**
     * Попытка регистрации
     *
     * @param \Bitrix\Main\HttpRequest                  $request
     * @param \Local\Core\Inner\BxModified\HttpResponse $response
     * @param                                           $args
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\ArgumentTypeException
     */
    public static function tryReg(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $aResult = array();

        try
        {
            if( !\Local\Core\Inner\Client\Recaptcha::validate($request->getPost('g-recaptcha-response')) )
            {
                throw new \Exception('Вы не прошли проверку на робота. Попробуйте позже.');
            }

            if(
                empty(trim($request->getPost('email')))
                || empty(trim($request->getPost('password')))
                || empty(trim($request->getPost('repPassword')))
                || empty(trim($request->getPost('name')))
                || empty(trim($request->getPost('lastName')))
            )
            {
                throw new \Exception('Для регистрации необходимо заполнить все поля.');
            }

            if( !filter_var(trim($request->getPost('email')), FILTER_VALIDATE_EMAIL) )
            {
                throw new \Exception('E-mail не похож на почтовый адрес.');
            }

            if( strlen($request->getPost('password')) < 6 || strlen($request->getPost('repPassword')) < 6 )
            {
                throw new \Exception('Длина паролей не может быть меньше 6 символов.');
            }

            if( $request->getPost('password') != $request->getPost('repPassword') )
            {
                throw new \Exception('Пароли не совпадают.');
            }

            $rsUserSearch = \Bitrix\Main\UserTable::getList([
                'filter' => [
                    'EMAIL' => trim($request->getPost('email'))
                ],
                'select' => ['ID']
            ]);

            if( $rsUserSearch->getSelectedRowsCount() > 0 )
            {
                throw new \Exception('Пользователь с таким E-mail уже существует.');
            }

            $user = new \CUser;
            $arFields = Array(
                "EMAIL"             => trim($request->getPost('email')),
                "LOGIN"             => trim($request->getPost('email')),
                "ACTIVE"            => "Y",
                "GROUP_ID"          => array(11, 2),
                "PASSWORD"          => trim($request->getPost('password')),
                "CONFIRM_PASSWORD"  => trim($request->getPost('repPassword')),
                "NAME"  => trim($request->getPost('name')),
                "LAST_NAME"  => trim($request->getPost('lastName')),
            );

            $ID = $user->Add($arFields);
            if( $ID > 0 )
            {
                $GLOBALS['USER']->Authorize($ID);
                $aResult['result'] = "success";
            }
            else
            {
                throw new \Exception($user->LAST_ERROR);
            }

        }
        catch (\Exception $e)
        {
            $aResult['result'] = 'error';
            $aResult['error_text'] = $e->getMessage();
        }

        $response->setContentJson($aResult);
    }

    /**
     * Попытка восстановления пароля
     *
     * @param \Bitrix\Main\HttpRequest                  $request
     * @param \Local\Core\Inner\BxModified\HttpResponse $response
     * @param                                           $args
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\ArgumentTypeException
     */
    public static function tryRestorePassword(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $aResult = [];
        try
        {
            if( !\Local\Core\Inner\Client\Recaptcha::validate($request->getPost('g-recaptcha-response')) )
            {
                throw new \Exception('Вы не прошли проверку на робота. Попробуйте позже.');
            }

            $strEmail = trim($request->getPost('email'));

            if(
            empty($strEmail)
            )
            {
                throw new \Exception('Для восстановления пароля необходимо заполнить E-mail.');
            }

            if( !filter_var($strEmail, FILTER_VALIDATE_EMAIL) )
            {
                throw new \Exception('E-mail не похож на почтовый адрес.');
            }

            $rsUser = \Bitrix\Main\UserTable::getList([
                'filter' => [
                    'EMAIL' => $strEmail
                ],
                'select' => ['ID']
            ]);
            if( $rsUser->getSelectedRowsCount() < 1 )
            {
                throw new \Exception('Пользователя с таким E-mail не существует.');
            }
            $arUser = $rsUser->fetch();

            $strPassword = rand(111111111111, 999999999999);

            $obUser = new \CUser();
            $res = $obUser->Update($arUser['ID'], [
                'PASSWORD' => $strPassword,
                'CONFIRM_PASSWORD' => $strPassword,
            ]);

            if( $res )
            {
                \Local\Core\Inner\TriggerMail\User::passwordRestored([
                    'EMAIL' => $strEmail,
                    'NEW_PASSWORD' => $strPassword
                ]);
                $aResult['result'] = 'success';
            }
            else
            {
                throw new \Exception($obUser->LAST_ERROR);
            }

        }
        catch (\Exception $e)
        {
            $aResult['result'] = 'error';
            $aResult['error_text'] = $e->getMessage();
        }

        $response->setContentJson($aResult);
    }
}