<?php

class PersonalSettingComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        if( !$GLOBALS['USER']->IsAuthorized() )
        {
            $this->_show404Page('Вы должны быть авторизованы.');
        }
        else
        {
            $this->checkRequest();
            $this->fillResult();

            $this->includeComponentTemplate();
        }
    }

    protected function checkRequest()
    {
        $obRequest = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        if( check_bitrix_sessid() && !empty( $obRequest->getPost('USER_DATA') ) )
        {
            $arResult = [];
            try
            {
                if( empty( trim($obRequest->getPost('USER_DATA')['OLD_PASS']) ) )
                {
                    throw new \Exception('Для сохранения данные необходимо ввести текущий пароль.');
                }

                if( !\Local\Core\Assistant\User::comparePassword($GLOBALS['USER']->GetID(), trim($obRequest->getPost('USER_DATA')['OLD_PASS'])) )
                {
                    throw new \Exception('Введенный пароль не совпадает с текущим паролем.');
                }

                $arUpdate = [
                    'NAME' => $obRequest->getPost('USER_DATA')['NAME'],
                    'LAST_NAME' => $obRequest->getPost('USER_DATA')['LAST_NAME'],
                    'EMAIL' => $obRequest->getPost('USER_DATA')['EMAIL'],
                    'LOGIN' => $obRequest->getPost('USER_DATA')['EMAIL'],
                ];

                if(
                    !empty( trim( $obRequest->getPost('USER_DATA')['NEW_PASS'] ) )
                    && !empty( trim( $obRequest->getPost('USER_DATA')['NEW_PASS_REP'] ) )
                )
                {
                    if( trim( $obRequest->getPost('USER_DATA')['NEW_PASS'] ) != trim( $obRequest->getPost('USER_DATA')['NEW_PASS_REP'] ) )
                    {
                        throw new \Exception('Новые пароли не совпадают.');
                    }

                    $arUpdate['PASSWORD'] = trim( $obRequest->getPost('USER_DATA')['NEW_PASS'] );
                    $arUpdate['CONFIRM_PASSWORD'] = trim( $obRequest->getPost('USER_DATA')['NEW_PASS_REP'] );
                }

                if( $obRequest->getFile('IMAGE') && $obRequest->getFile('IMAGE')['error'] == 0 )
                {
                    $arUpdate['PERSONAL_PHOTO'] = ( $obRequest->getFile('IMAGE') + ['del' => 'Y', 'MODULE_ID' => 'main'] );
                }

                $obUser = new \CUser();
                $obRes = $obUser->Update($GLOBALS['USER']->GetID(), $arUpdate);
                if( !$obUser )
                {
                    throw new \Exception( implode('<br/>', $obUser->LAST_ERROR) );
                }

                \Local\Core\Inner\Cache::deleteCache(['Other', 'UserAsideInfoInTemplate'], ['userId='.$GLOBALS['USER']->GetID()]);

                $arResult = ['STATUS' => 'SUCCESS'];
            }
            catch (\Exception $e)
            {
                $arResult = ['STATUS' => 'ERROR', 'ERROR_TEXT' => $e->getMessage()];
            }

            $this->arResult = $arResult;
        }
    }

    protected function fillResult()
    {
        $arResult = $this->arResult ?? [];

        $arUser = \Bitrix\Main\UserTable::getByPrimary($GLOBALS['USER']->GetID(), ['select' => ['NAME', 'LAST_NAME', 'EMAIL', 'PERSONAL_PHOTO']])->fetch();
        if( !empty( $arUser) )
        {
            $arResult += $arUser;
        }


        $this->arResult = $arResult;
    }
}