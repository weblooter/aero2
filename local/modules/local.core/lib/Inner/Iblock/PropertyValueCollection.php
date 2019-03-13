<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 15.11.18
 * Time: 12:46
 */

namespace Local\Core\Inner\Iblock;

use Local\Core\Inner\EntityCollection;

/**
 * Class IblockPropertyValueCollection
 * @package Local\Core\Inner
 */
class PropertyValueCollection extends EntityCollection
{
    /**
     * Фабричный метод создания коллекции
     * @return PropertyValueCollection
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Получить сущность родителя
     * @return \Local\Core\Inner\Entity|null
     */
    protected function getEntityParent()
    {
        return null;
    }

    /**
     * Добавить элемент в колекцию
     *
     * @param PropertyValue $item
     *
     * @return \Local\Core\Inner\CollectableEntity
     * @throws \Bitrix\Main\ArgumentTypeException
     */
    public function addItem(PropertyValue $item)
    {
        return parent::addItem($item);
    }
}
