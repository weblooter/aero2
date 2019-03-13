<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 15.11.18
 * Time: 12:46
 */

namespace Local\Core\Inner;

class FileCollection extends EntityCollection
{
    /**
     * @return FileCollection
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @return Entity|null
     */
    protected function getEntityParent()
    {
        return null;
    }

    /**
     * @param File $item
     *
     * @return CollectableEntity
     * @throws \Bitrix\Main\ArgumentTypeException
     */
    public function addItem(File $item)
    {
        return parent::addItem($item);
    }
}
