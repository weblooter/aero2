<?php

namespace Local\Core\Inner\BxModified;

/**
 * Модифицированный класс \CFile
 *
 * @see \CFile
 * @inheritdoc
 * @package Local\Core\Inner\BxModified
 */
class CFile extends \CFile
{
    /**
     * Сохраняет файл под модулем local.core
     *
     * @param array  $arFile Массив файла \CFile::MakeFileArray(). MODULE_ID дописывается автоматом.
     * @param string $strSavePath Путь сохранения. /local.core/ дописывается автоматом. Если пуст - сохранит в /local.core/tmp/
     * @param int    $intOldFileId ID старого файла, который необходимо удалить, если такой есть.
     *
     * @see \CFile::MakeFileArray()
     *
     * @return int Метод возвращает числовой идентификатор сохранённого и зарегистрированного в системе файла.
     */
    public static function saveFile(array $arFile, string $strSavePath = '', $intOldFileId = 0)
    {
        $arFile['MODULE_ID'] = 'local.core';
        if( $intOldFileId > 0 )
        {
            $arFile['del'] = 'Y';
            $arFile['old_file'] = $intOldFileId;
        }

        $strNewSavePath = '';
        if( empty( trim( $strSavePath ) ) )
        {
            $strNewSavePath = ['local.core', 'tmp'];
        }
        else
        {
            $strNewSavePath = explode('/', $strSavePath);
            $strNewSavePath = array_diff($strNewSavePath, ['local.core'], ['']);
            $strNewSavePath = array_merge(['local.core'], $strNewSavePath);
            $strNewSavePath = array_map('trim', $strNewSavePath);
        }

        $strNewSavePath = '/'.implode('/', $strNewSavePath).'/';

        return parent::SaveFile( $arFile, $strNewSavePath );
    }

    /**
     * Проверяте расширение у загружаемого файла
     *
     * @param array|string $mixFile Загружаемый файл, структурой как \CFile::MakeFileArray(), или название файла
     * @param string $strExtension Расшине файла, к примеру <b>.xml</b>
     *
     * @see \CFile::MakeFileArray()
     *
     * @return bool
     */
    public static function checkExtension($mixFile, $strExtension)
    {
        $strExtension = preg_quote($strExtension);
        if( is_array( $mixFile ) )
        {
            return ( preg_match( '/('.$strExtension.')$/', trim( $mixFile[ 'name' ] ) ) === 1 );
        }
        else
        {
            return ( preg_match( '/('.$strExtension.')$/', trim( $mixFile ) ) === 1 );
        }
    }
}