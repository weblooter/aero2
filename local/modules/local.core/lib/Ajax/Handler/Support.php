<?php

namespace Local\Core\Ajax\Handler;

use Bitrix\Main\UserTable;
use Local\Core\Inner\Route;

class Support
{
    /**
     * Создание обращение и сохранение сообщения
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
    public static function addMessage(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $aResult = [];

        try
        {
            if( $request->getPost('SUPPORT_ID') < 0 )
            {
                throw new \Exception('ID обращения не указан.');
            }

            if( empty( trim($request->getPost('MSG')) ) )
            {
                throw new \Exception('Для отправки сообщения необходимо ввести текст.');
            }

            $intSupportId = $request->getPost('SUPPORT_ID');
            $boolSupportIsMy = null;

            if( $intSupportId < 1 )
            {
                $rs = \Local\Core\Model\Data\SupportTable::add([
                    'USER_ID' => $GLOBALS['USER']->GetID(),
                ]);
                if( !$rs->isSuccess() )
                {
                    throw new \Exception('Сейчас невозможно создать обращение, попробуйте позже.');
                }
                else
                {
                    $intSupportId = $rs->getId();
                    $boolSupportIsMy = true;
                }
            }

            if( is_null($boolSupportIsMy) )
            {
                $arSupport = \Local\Core\Model\Data\SupportTable::getByPrimary($intSupportId, ['select' => ['USER_ID', 'ACTIVE']])->fetch();
                $boolSupportIsMy = ( $arSupport['USER_ID'] == $GLOBALS['USER']->GetID() );
            }

            if( !$boolSupportIsMy )
            {
                throw new \Exception('Сейчас невозможно создать обращение, попробуйте позже.');
            }

            $strMess = htmlspecialchars( trim($request->getPost('MSG')) );
            $strMess = str_replace(["\n"], ['<br/>'], $strMess);

            $rs = \Local\Core\Model\Data\SupportMessageTable::add([
                'SUPPORT_ID' => $intSupportId,
                'OWN' => 'US',
                'MSG' => $strMess
            ]);

            if( !$rs->isSuccess() )
            {
                throw new \Exception('Сейчас невозможно создать обращение, попробуйте позже.');
            }

            if( $arSupport['ACTIVE'] == 'N' )
            {
                \Local\Core\Model\Data\SupportTable::update($intSupportId, ['ACTIVE' => 'Y']);
            }

            $aResult['result'] = 'success';
            $aResult['SUPPORT_ID'] = $intSupportId;

            \Local\Core\Inner\TriggerMail\Support::addNewMessage([
                'EMAIL' => \Bitrix\Main\Config\Configuration::getInstance()->get('mail')['smtp']['login'] ?? 'info@robofeed.ru',
                'MSG' => $strMess,
                'TASK_ID' => $intSupportId,
                'TASK_LINK' => Route::getRouteTo('support-admin', 'detail', ['#SUPPORT_ID#' => $intSupportId])
            ]);
        }
        catch (\Exception $e)
        {
            $aResult['result'] = 'error';
            $aResult['error_text'] = $e->getMessage();
        }


        $response->setContentJson($aResult);
    }

    /**
     * addMessage(), только для админа
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
    public static function addMessageAdmin(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $aResult = [];

        try
        {
            if( !$GLOBALS['USER']->IsAdmin() )
            {
                throw new \Exception('Данная функция Вам недоступна.');
            }

            if( $request->getPost('SUPPORT_ID') < 0 )
            {
                throw new \Exception('ID обращения не указан.');
            }

            if( empty( trim($request->getPost('MSG')) ) )
            {
                throw new \Exception('Для отправки сообщения необходимо ввести текст.');
            }

            $intSupportId = $request->getPost('SUPPORT_ID');
            $strMess = htmlspecialchars( trim($request->getPost('MSG')) );
            $strMess = str_replace(["\n"], ['<br/>'], $strMess);

            $rs = \Local\Core\Model\Data\SupportMessageTable::add([
                'SUPPORT_ID' => $intSupportId,
                'OWN' => 'AD',
                'MSG' => $strMess,
                'ACTIVE' => 'N'
            ]);

            if( !$rs->isSuccess() )
            {
                throw new \Exception('Сейчас невозможно создать обращение, попробуйте позже.');
            }

            $aResult['result'] = 'success';
            $aResult['SUPPORT_ID'] = $intSupportId;

            $arSupport = \Local\Core\Model\Data\SupportTable::getByPrimary($intSupportId, ['select' => ['USER_ID']])->fetch();
            if( !empty( $arSupport ) )
            {
                $arUser = \Bitrix\Main\UserTable::getByPrimary($arSupport['USER_ID'], ['select' => ['EMAIL']])->fetch();

                if( !empty( $arUser['EMAIL'] ) )
                {
                    \Local\Core\Inner\TriggerMail\Support::addNewMessage([
                        'EMAIL' => $arUser['EMAIL'],
                        'MSG' => $strMess,
                        'TASK_ID' => $intSupportId,
                        'TASK_LINK' => Route::getRouteTo('support', 'detail', ['#SUPPORT_ID#' => $intSupportId])
                    ]);
                }
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
     * Закрывает обращение
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
    public static function closeTask(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $aResult = [];

        try
        {
            if( $request->getPost('SUPPORT_ID') < 0 )
            {
                throw new \Exception('ID обращения не указан.');
            }

            $intSupportId = $request->getPost('SUPPORT_ID');

            $arSupport = \Local\Core\Model\Data\SupportTable::getByPrimary($intSupportId, ['select' => ['USER_ID']])->fetch();

            if( empty($arSupport) )
            {
                throw new \Exception('Сейчас невозможно закрыть обращение, попробуйте позже.');
            }

            $boolSupportIsMy = ( $arSupport['USER_ID'] == $GLOBALS['USER']->GetID() );

            if( !$boolSupportIsMy )
            {
                throw new \Exception('Сейчас невозможно закрыть обращение, попробуйте позже.');
            }

            $rs = \Local\Core\Model\Data\SupportTable::update($intSupportId, ['ACTIVE' => 'N']);

            if( !$rs->isSuccess() )
            {
                throw new \Exception('Сейчас невозможно закрыть обращение, попробуйте позже.');
            }

            $aResult['result'] = 'success';
            $aResult['SUPPORT_ID'] = $intSupportId;

            \Local\Core\Inner\TriggerMail\Support::taskClosed([
                'EMAIL' => $GLOBALS['USER']->GetEmail(),
                'TASK_ID' => $intSupportId,
                'TASK_LINK' => Route::getRouteTo('support', 'detail', ['#SUPPORT_ID#' => $intSupportId])
            ]);
        }
        catch (\Exception $e)
        {
            $aResult['result'] = 'error';
            $aResult['error_text'] = $e->getMessage();
        }


        $response->setContentJson($aResult);
    }
}