<?

class RobofeedConvertFormComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public $intMaxUploadFileSizeMb;

    public function executeComponent()
    {
        if (!$GLOBALS['USER']->IsAuthorized()) {
            echo 'Необходимо авторизоваться';
        } else {
            $this->intMaxUploadFileSizeMb = \Bitrix\Main\Config\Configuration::getInstance()
                                                ->get('robofeed')['convert']['upload_file_max_size'] ?? 100;

            if (
                !empty(\Bitrix\Main\Application::getInstance()
                    ->getContext()
                    ->getRequest()
                    ->getPost('CONVERT'))
                && !empty(\Bitrix\Main\Application::getInstance()
                    ->getContext()
                    ->getRequest()
                    ->getFile('CONVERT'))
                && check_bitrix_sessid()
            ) {
                $this->__tryAdd();
            }

            $this->__fillResult();

            $this->includeComponentTemplate();
        }
    }

    private function __tryAdd()
    {

        $arConvertFile = array_combine(array_keys(\Bitrix\Main\Application::getInstance()
            ->getContext()
            ->getRequest()
            ->getFile('CONVERT')), array_column(\Bitrix\Main\Application::getInstance()
            ->getContext()
            ->getRequest()
            ->getFile('CONVERT'), 'FILE'));

        $arPostFields = \Bitrix\Main\Application::getInstance()
            ->getContext()
            ->getRequest()
            ->getPost('CONVERT');

        $arAddFields = [];

        try {
            if (!in_array($arPostFields['HANDLER'], \Local\Core\Model\Robofeed\ConvertTable::getEnumFieldValues('HANDLER'))) {
                throw new \Exception('Нет такого исходного формата файла');
            }
            $arAddFields['HANDLER'] = $arPostFields['HANDLER'];

            switch ($arPostFields['HANDLER']) {
                case 'YML':
                    if (
                    !\Local\Core\Inner\BxModified\CFile::checkExtension($arConvertFile, '.xml')
                    ) {
                        throw new \Exception('Файл должен быть XML');
                    }
                    break;
            }

            if (
                round(($arConvertFile['size'] / 1000 / 1000), 3) > $this->intMaxUploadFileSizeMb
            ) {
                throw new \Exception('Максимальный размер файла - '.$this->intMaxUploadFileSizeMb.'Мб');
            }

            $intFileSave = \Local\Core\Inner\BxModified\CFile::saveFile($arConvertFile, '/robofeed/convert/original_file/');
            if ($intFileSave < 1) {
                throw new \Exception('Не удалось сохранить файл');
            }

            $arAddFields['ORIGINAL_FILE_ID'] = $intFileSave;
            $arAddFields['ORIGINAL_FILE_NAME'] = $arConvertFile['name'];
        } catch (\Exception $e) {
            $this->arResult['ADD_STATUS'] = 'ERROR';
            $this->arResult['ERROR_TEXT'][] = $e->getMessage();
        }

        if (is_null($this->arResult['ADD_STATUS'])) {
            /** @var \Bitrix\Main\ORM\Data\AddResult $obRes */
            $obRes = \Local\Core\Model\Robofeed\ConvertTable::add($arAddFields);
            if ($obRes->isSuccess()) {
                $this->arResult['ADD_STATUS'] = 'SUCCESS';
            } else {
                $this->arResult['ADD_STATUS'] = 'ERROR';
                $this->arResult['ERROR_TEXT'] = $obRes->getErrorMessages();
            }
        }

    }

    private function __fillResult()
    {
        $this->arResult['HANDLERS'] = \Local\Core\Model\Robofeed\ConvertTable::getEnumFieldHtmlValues('HANDLER');
    }
}