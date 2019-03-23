<?php

namespace Local\Core\Inner\Robofeed\Traites;


trait AbstractClass
{

    /**
     * @var string $strXmlFilePath Абсолютный путь к XML файлу
     */
    protected $strXmlFilePath;

    /**
     * Задает путь к XML файл
     *
     * @param $strXmlFilePath
     *
     * @throws \Local\Core\Inner\Exception\FatalException
     * @return $this
     */
    public function setXmlPath($strXmlFilePath)
    {
        if( !file_exists($strXmlFilePath) )
        {
            throw new \Local\Core\Inner\Exception\FatalException('XML файл по пути '.$strXmlFilePath.' не найден!');
        }

        $this->strXmlFilePath = $strXmlFilePath;
        return $this;
    }

    /**
     * @var integer $intStoreId ID мазагина, для которого импортируем робофид
     */
    protected $intStoreId;

    /**
     * Задает ID магазина, в который будет происходить импорт
     *
     * @param integer $intStoreId ID мазагина
     *
     * @return $this
     */
    public function setStoreId($intStoreId)
    {
        $this->intStoreId = $intStoreId;
        return $this;
    }


    /**
     * Проверяет заполненость пути XML и в случае ошибки кидает фатальную ошибку
     *
     * @throws \Local\Core\Inner\Exception\FatalException
     */
    protected function checkFilledXmlPath()
    {
        if( is_null($this->strXmlFilePath) )
        {
            throw new \Local\Core\Inner\Exception\FatalException('Для дальнейшей работы необходимо задать путь до XML');
        }
    }

    /**
     * Возвращает номер версии
     *
     * @return mixed
     */
    abstract static function getVersion();

    /**
     * Инициализирует сценарий
     *
     * @return \Bitrix\Main\Result
     * @throws \Local\Core\Inner\Exception\FatalException
     */
    abstract public function run();
}