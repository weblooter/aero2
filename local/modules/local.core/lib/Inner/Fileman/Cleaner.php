<?php

namespace Local\Core\Inner\Fileman;


use Local\Core\Inner\BxModified\CFile;

/**
 * Класс для чистки файловой структуры
 *
 * @package Local\Core\Inner\Fileman
 */
class Cleaner
{
    private static $__arFinedRegisteredFiles = [];

    /**
     * Перечень ORM классов, в которых есть файлы.<br/>
     * Учавствует в \Local\Core\Inner\Fileman\Cleaner::__checkAndClearOrmFiles()
     *
     * @see \Local\Core\Inner\Fileman\Cleaner::__checkAndClearOrmFiles()
     *
     * @var array $__arOrmWithFiles
     */
    private static $__arOrmWithFiles = [
        \Local\Core\Model\Data\StoreTable::class
    ];

    /**
     * Чистит файлы в /upload/local.core/ .<br/>
     * Сравнивает те, что есть в b_file под модулем local.core с тема, что записаны в таблицах ORM local.core.<br/>
     * Так же уебывает не зарегистрированные ни там ни там.
     */
    public static function clearUnregisteredLocalCoreFiles()
    {
        // Соберем зарегистрированные файлы
        self::$__arFinedRegisteredFiles = self::__getFilesFromCFile();

        // Проверим файловую структуру с b_file и удалим левые файлы
        self::__checkAndClearDirFiles(
            \Bitrix\Main\Application::getDocumentRoot().'/'.\Bitrix\Main\Config\Option::get(
                'main',
                'upload_dir',
                'upload'
            )
        );

        // Пройдемся по ORM файлам
        self::$__arFinedRegisteredFiles = array_keys(self::$__arFinedRegisteredFiles);
        self::__checkAndClearOrmFiles();

    }

    /**
     * Собирает файлый в CFile с фильтром по local.core.<br/>
     * Выдает ассоциативный массив <b>FILE_ID => md5(CFile::GetPath())</b>
     *
     * @return array
     */
    private static function __getFilesFromCFile()
    {
        $rs = CFile::GetList(
            [],
            ['MODULE_ID' => 'local.core']
        );
        $arReturn = [];
        while( $ar = $rs->Fetch() )
        {
            $arReturn[$ar['ID']] = md5($ar['SUBDIR'].$ar['FILE_NAME']);
        }
        return $arReturn;
    }

    /**
     * Рекурсивно проходится и чистит файлы, которые не числятся в файловой системе.
     *
     * @param string $strDirPath Путь, в котором читам файлы
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    private static function __checkAndClearDirFiles(string $strDirPath)
    {
        $arFiles = glob($strDirPath.'/*');
        if( !empty($arFiles) )
        {
            foreach( $arFiles as $strFile )
            {
                if( is_dir($strFile) )
                {
                    self::__checkAndClearDirFiles($strFile);
                }
                else
                {
                    $hashFile = md5(
                        str_replace(
                            \Bitrix\Main\Application::getDocumentRoot().'/'.\Bitrix\Main\Config\Option::get(
                                'main',
                                'upload_dir',
                                'upload'
                            ),
                            '',
                            $strFile
                        )
                    );
                    if(
                    !in_array(
                        $hashFile,
                        self::$__arFinedRegisteredFiles
                    )
                    )
                    {
                        /*
                         * Файл есть в файловой структуре, но не занесем в базу. Удаляем
                         */
                        unlink($strFile);
                    }
                }
            }
        }
    }

    /**
     * Проходится по заявленным ORM моделям в \Local\Core\Inner\Fileman\Cleaner::$__arOrmWithFiles и сверяет их файлы.
     *
     * @see \Local\Core\Inner\Fileman\Cleaner::$__arOrmWithFiles
     */
    private static function __checkAndClearOrmFiles()
    {
        foreach( self::$__arOrmWithFiles as $strClassName )
        {
            if(
            method_exists(
                $strClassName,
                'getOrmFiles'
            )
            )
            {
                /** @var \Bitrix\Main\ORM\Query\Result $obResult */
                $obResult = $strClassName::getOrmFiles();
                while( $ar = $obResult->fetch() )
                {
                    if( !empty($ar) )
                    {
                        $arFilesIdList = array_values($ar);

                        self::$__arFinedRegisteredFiles = array_diff(
                            self::$__arFinedRegisteredFiles,
                            $arFilesIdList
                        );
                    }
                }
            }
        }

        if( !empty(self::$__arFinedRegisteredFiles) )
        {

            self::$__arFinedRegisteredFiles = new \ArrayIterator(self::$__arFinedRegisteredFiles);
            while( self::$__arFinedRegisteredFiles->valid() )
            {
                CFile::Delete(self::$__arFinedRegisteredFiles->current());
                self::$__arFinedRegisteredFiles->next();
            }
        }
    }
}