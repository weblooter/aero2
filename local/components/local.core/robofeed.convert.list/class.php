<?

class RobofeedConvertListComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public $intMaxUploadFileSizeMb;

    public function executeComponent()
    {
        if( !$GLOBALS['USER']->IsAuthorized() )
        {
            echo 'Необходимо авторизоваться';
        }
        else
        {

            $this->__checkDownloadQuery();

            $this->__fillResult();

            $this->includeComponentTemplate();
        }
    }

    private function __fillResult()
    {
        $this->arResult = [];

        $rsConvert = \Local\Core\Model\Robofeed\ConvertTable::getList(
            [
                'filter' => [
                    'USER_ID' => $GLOBALS['USER']->GetId()
                ],
                'order' => ['DATE_MODIFIED' => 'DESC'],
                'select' => ['DATE_MODIFIED', 'ORIGINAL_FILE_NAME', 'HANDLER', 'STATUS', 'EXPORT_FILE_ID', 'ERROR_MESSAGE', 'VALID_ERROR_MESSAGE']
            ]
        );
        while( $ar = $rsConvert->fetch() )
        {
            $this->arResult['ITEMS'][] = $ar;
        }

        $this->arResult['STATUS'] = \Local\Core\Model\Robofeed\ConvertTable::getEnumFieldHtmlValues('STATUS');
        $this->arResult['HANDLER'] = \Local\Core\Model\Robofeed\ConvertTable::getEnumFieldHtmlValues('HANDLER');
    }

    private function __checkDownloadQuery()
    {
        if( !empty( \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('getFile') ) )
        {
            if( file_exists(\Bitrix\Main\Application::getDocumentRoot().\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('getFile')) )
            {
                $GLOBALS['APPLICATION']->RestartBuffer();
                $file = \Bitrix\Main\Application::getDocumentRoot().\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('getFile');
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: '.filesize($file));
                ob_clean();
                flush();
                readfile($file);
                die();
            }
        }
    }
}