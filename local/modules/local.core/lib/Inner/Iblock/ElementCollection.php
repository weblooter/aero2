<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 15.11.18
 * Time: 12:46
 */

namespace Local\Core\Inner\Iblock;

use Local\Core\Inner\EntityCollection;

class ElementCollection extends EntityCollection
{
    protected $navPageCount;
    protected $navPageNomer;
    protected $navPageSize;
    protected $navShowAll;
    protected $navRecordCount;

    /**
     * Создать коллекцию. Фабричный метод.
     * @return ElementCollection
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
     * @param Element $item
     * @return \Local\Core\Inner\CollectableEntity
     * @throws \Bitrix\Main\ArgumentTypeException
     */
    public function addItem(Element $item)
    {
        return parent::addItem($item);
    }

    /**
     * Установить параметры постраничной навигации
     * @param \CDBResult $result
     */
    public function setPageNavParams(\CDBResult $result)
    {
        if((int)$result->NavPageNomer)
        {
            $this->setNavPageCount($result->NavPageCount);
            $this->setNavPageNomer($result->NavPageNomer);
            $this->setNavPageSize($result->NavPageSize);
            $this->setNavShowAll($result->NavShowAll);
            $this->setNavRecordCount($result->NavRecordCount);
        }
    }

    /**
     * Установить количество страниц для текущх параметров постаричной навигации
     * @internal
     * @param $nav_page_count
     */
    protected function setNavPageCount($nav_page_count)
    {
        $this->navPageCount = (int)$nav_page_count;
    }

    /**
     * Установить номер текущей станицы
     * @internal
     * @param $nav_page_nomer
     */
    protected function setNavPageNomer($nav_page_nomer)
    {
        $this->navPageNomer = (int)$nav_page_nomer;
    }

    /**
     * Установить количество элементов на странице
     * @internal
     * @param $nav_page_size
     */
    protected function setNavPageSize($nav_page_size)
    {
        $this->navPageSize = (int)$nav_page_size;
    }

    /**
     * Установить флаг отображения всех элементов
     * @internal
     * @param $nav_show_all
     */
    protected function setNavShowAll($nav_show_all)
    {
        $this->navShowAll = (bool)$nav_show_all;
    }

    /**
     * Установить общее количсетво элементов
     * @internal
     * @param $nav_record_count
     */
    protected function setNavRecordCount($nav_record_count)
    {
        $this->navRecordCount = (int)$nav_record_count;
    }

    /**
     * @return int
     */
    public function getNavPageCount()
    {
        return $this->navPageCount;
    }

    /**
     * @return int
     */
    public function getNavPageNomer()
    {
        return $this->navPageNomer;
    }

    /**
     * @return int
     */
    public function getNavPageSize()
    {
        return $this->navPageSize;
    }

    /**
     * @return int
     */
    public function getNavShowAll()
    {
        return $this->navShowAll;
    }

    /**
     * @return int
     */
    public function getNavRecordCount()
    {
        return $this->navRecordCount;
    }
}
