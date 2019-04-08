<?php

namespace Local\Core\Inner\BxModified;

/**
 * Модифицированный класс \CFile
 *
 * @see     \CFile
 * @inheritdoc
 * @package Local\Core\Inner\BxModified
 */
class CFile extends \CFile
{
    /**
     * Сохраняет файл под модулем local.core
     *
     * @param array  $arFile       Массив файла \CFile::MakeFileArray(). MODULE_ID дописывается автоматом.
     * @param string $strSavePath  Путь сохранения. /local.core/ дописывается автоматом. Если пуст - сохранит в
     *                             /local.core/tmp/
     * @param int    $intOldFileId ID старого файла, который необходимо удалить, если такой есть.
     *
     * @return int Метод возвращает числовой идентификатор сохранённого и зарегистрированного в системе файла.
     * @see \CFile::MakeFileArray()
     *
     */
    public static function saveFile(array $arFile, string $strSavePath = '', $intOldFileId = 0)
    {
        $arFile['MODULE_ID'] = 'local.core';
        if ($intOldFileId > 0) {
            $arFile['del'] = 'Y';
            $arFile['old_file'] = $intOldFileId;
        }

        $strNewSavePath = static::makeLocalCorePath($strSavePath);
        $strNewSavePath = str_replace('/upload/', '/', $strNewSavePath);

        return parent::SaveFile($arFile, $strNewSavePath);
    }

    /**
     * Формирует путь сохранения от upload/local.core/
     *
     * @param string $strPath          Путь
     * @param bool   $boolDocumentRoot Выдать с DOCUMENT_ROOT
     * @param bool   $boolMkdir        Создать директорию
     *
     * @return string
     */
    public static function makeLocalCorePath(string $strPath = '', bool $boolDocumentRoot = false, bool $boolMkdir = false)
    {
        if (empty(trim($strPath))) {
            $strNewSavePath = ['local.core', 'tmp'];
        } else {
            $strNewSavePath = explode('/', $strPath);
            $strNewSavePath = array_diff($strNewSavePath, ['local.core'], ['']);
            $strNewSavePath = array_merge(['local.core'], $strNewSavePath);
            $strNewSavePath = array_map('trim', $strNewSavePath);
        }

        $strNewSavePath = '/upload/'.implode('/', $strNewSavePath).'/';

        if ($boolMkdir) {
            if (
            !file_exists(\Bitrix\Main\Application::getInstance()
                             ->getContext()
                             ->getServer()
                             ->getDocumentRoot().$strNewSavePath)
            ) {
                mkdir(\Bitrix\Main\Application::getInstance()
                          ->getContext()
                          ->getServer()
                          ->getDocumentRoot().$strNewSavePath, 0777, true);
            }
        }

        if ($boolDocumentRoot) {
            $strNewSavePath = \Bitrix\Main\Application::getInstance()
                                  ->getContext()
                                  ->getServer()
                                  ->getDocumentRoot().$strNewSavePath;
        }

        return $strNewSavePath;
    }

    /**
     * Проверяте расширение у загружаемого файла
     *
     * @param array|string $mixFile      Загружаемый файл, структурой как \CFile::MakeFileArray(), или название файла
     * @param string       $strExtension Расшине файла, к примеру <b>.xml</b>
     *
     * @return bool
     * @see \CFile::MakeFileArray()
     *
     */
    public static function checkExtension($mixFile, $strExtension)
    {
        $strExtension = preg_quote($strExtension);
        if (is_array($mixFile)) {
            return (preg_match('/('.$strExtension.')$/', trim($mixFile['name'])) === 1);
        } else {
            return (preg_match('/('.$strExtension.')$/', trim($mixFile)) === 1);
        }
    }
}