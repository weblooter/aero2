<?php

namespace Local\Core\Inner;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main;
use Local\Core\Inner\Iblock\ElementCollection;

Loc::loadMessages(__FILE__);

/**
 * Class File
 * @package Local\Core\Inner
 */
class File extends CollectableEntity
{
    protected $uploadDir;

    /**
     * Получить коллекцию файлов, ссылки на которые присутствуют в $collection
     *
     * @param ElementCollection $collection
     *
     * @return FileCollection
     * @throws Main\ArgumentException
     * @throws Main\ArgumentTypeException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getCollectionByElementCollection(ElementCollection $collection)
    {
        $file_ids = [];
        /** @var $item Iblock\Element */
        foreach ($collection as $item) {
            if ($file_id = (int)$item->getField('DETAIL_PICTURE')) {
                $file_ids[] = $file_id;
            }

            if ($file_id = (int)$item->getField('PREVIEW_PICTURE')) {
                $file_ids[] = $file_id;
            }

            if ($prop_collection = $item->getPropertyCollection()) {
                /** @var $property IblockPropertyValue */
                foreach ($prop_collection as $property) {
                    if ($property->getPropertyType() == 'F') {
                        if ($property->isMultiple()) {
                            $values = array_filter(array_map(function ($v)
                                    {
                                        return (int)$v;
                                    }, (array)$property->getField('VALUE')));

                            if ($values) {
                                $file_ids = array_merge($file_ids, $values);
                            }
                        } else {
                            if ($value = (int)$property->getField('VALUE')) {
                                $file_ids[] = $value;
                            }
                        }
                    }
                }
            }
        }

        return self::getList(['ID' => $file_ids]);
    }

    public static function getById(int $id)
    {
        $id = (int)$id;
        if ($id <= 0) {
            throw new Main\ArgumentNullException('Не указан ID элемента');
        }

        return self::getList(['ID' => $id]);
    }

    /**
     * @param array $filter
     *
     * @return FileCollection
     * @throws Main\ArgumentException
     * @throws Main\ArgumentTypeException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getList(array $filter = [])
    {
        $params = [];
        if (!empty($filter)) {
            $params = ['filter' => $filter];
        }

        /** @var $collection FileCollection */
        $collection = FileCollection::create();

        $rows = Main\FileTable::getList($params);

        while ($row = $rows->fetch()) {
            /** @var $item IblockElement */
            $item = self::create($row);

            $item->setCollection($collection);

            $collection->addItem($item);
        }

        return $collection;
    }

    /**
     * Фабричный метод создания объекта класса
     *
     * @param array $fields
     *
     * @return File|Image
     */
    protected static function create(array $fields = array())
    {
        $arContentType = substr($fields['CONTENT_TYPE'], 0, 5);

        if ($arContentType === 'image') {
            return new Image($fields);
        }

        return new self($fields);
    }

    /**
     * Product constructor.
     *
     * @param array $fields
     */
    protected function __construct(array $fields = array())
    {
        $this->uploadDir = Main\Config\Option::get('main', 'upload_dir', 'upload');

        parent::__construct($fields);
    }

    /**
     * @inheritdoc
     */
    public static function getAvailableFields()
    {
        return array(
            'ID',
            'TIMESTAMP_X',
            'MODULE_ID',
            'HEIGHT',
            'WIDTH',
            'FILE_SIZE',
            'CONTENT_TYPE',
            'SUBDIR',
            'FILE_NAME',
            'ORIGINAL_NAME',
            'DESCRIPTION',
            'HANDLER_ID',
            'EXTERNAL_ID',
        );
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->getField('ID');
    }

    public function getModule()
    {
        return $this->getField('MODULE_ID');
    }

    public function getTimestamp()
    {
        return $this->getField('TIMESTAMP_X');
    }

    public function getHeight()
    {
        return $this->getField('HEIGHT');
    }

    public function getWidth()
    {
        return $this->getField('WIDTH');
    }

    public function getFileSize()
    {
        return $this->getField('FILE_SIZE');
    }

    public function getContentType()
    {
        return $this->getField('CONTENT_TYPE');
    }

    public function getSubDir()
    {
        return $this->getField('SUBDIR');
    }

    public function getFileName()
    {
        return $this->getField('FILE_NAME');
    }

    public function getOriginalName()
    {
        return $this->getField('ORIGINAL_NAME');
    }

    public function getHandler()
    {
        return $this->getField('HANDLER_ID');
    }

    public function getExternalID()
    {
        return $this->getField('EXTERANAL_ID');
    }

    public function getSrc()
    {
        return '/'.$this->uploadDir.'/'.$this->getSubDir().'/'.$this->getFileName();
    }

    public function getHash()
    {
        return md5($this->getId());
    }
}