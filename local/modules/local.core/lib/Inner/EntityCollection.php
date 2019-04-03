<?php

namespace Local\Core\Inner;

use Bitrix\Sale;
use Bitrix\Main;

/**
 * Class EntityCollection
 * @package Local\Core\Inner
 */
abstract class EntityCollection extends CollectionBase
{
    private $index = -1;

    protected $isClone = false;

    protected $anyItemDeleted = false;


    /**
     * EntityCollection constructor.
     */
    protected function __construct()
    {

    }

    /**
     * @param CollectableEntity $item
     * @param null              $name
     * @param null              $oldValue
     * @param null              $value
     *
     * @return Sale\Result
     */
    public function onItemModify(CollectableEntity $item, $name = null, $oldValue = null, $value = null)
    {
        return new Main\Result();
    }

    /**
     * @internal
     *
     * @param $index
     *
     * @return mixed
     * @throws Main\ArgumentOutOfRangeException
     */
    public function deleteItem($index)
    {
        if (!isset($this->collection[$index])) {
            throw new Main\ArgumentOutOfRangeException("collection item index wrong");
        }

        $oldItem = $this->collection[$index];

        unset($this->collection[$index]);
        $this->setAnyItemDeleted(true);

        return $oldItem;
    }

    /**
     * @param CollectableEntity $item
     *
     * @return CollectableEntity
     * @throws Main\ArgumentTypeException
     */
    protected function addItem(CollectableEntity $item)
    {
        $index = $this->createIndex();
        $item->setInternalIndex($index);

        $this->collection[$index] = $item;

        return $item;
    }

    /**
     * @return int
     */
    protected function createIndex()
    {
        $this->index++;
        return $this->index;
    }

    public function clearCollection()
    {
        /** @var CollectableEntity $item */
        foreach ($this->collection as $item) {
            $item->delete();
        }
    }


    /**
     * @param $id
     *
     * @return CollectableEntity|bool
     * @throws Main\ArgumentNullException
     */
    public function getItemById($id)
    {
        if (intval($id) <= 0) {
            throw new Main\ArgumentNullException('id');
        }

        $index = $this->getIndexById($id);
        if ($index === null) {
            return null;
        }

        if (isset($this->collection[$index])) {
            return $this->collection[$index];
        }

        return null;
    }


    /**
     * @param $id
     *
     * @return bool|int|null
     * @throws Main\ArgumentNullException
     */
    public function getIndexById($id)
    {
        if (intval($id) <= 0) {
            throw new Main\ArgumentNullException('id');
        }

        /** @var CollectableEntity $item */
        foreach ($this->collection as $item) {
            if ($item->getId() > 0 && $id == $item->getId()) {
                return $item->getInternalIndex();
            }
        }
        return null;
    }

    /**
     * @param $index
     *
     * @return CollectableEntity|null
     * @throws Main\ArgumentNullException
     */
    public function getItemByIndex($index)
    {
        if (intval($index) < 0) {
            throw new Main\ArgumentNullException('id');
        }

        /** @var CollectableEntity $item */
        foreach ($this->collection as $item) {
            if ($item->getInternalIndex() == $index) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @return Entity
     */
    abstract protected function getEntityParent();

    /**
     * @return bool
     */
    public function isClone()
    {
        return $this->isClone;
    }

    /**
     * @return bool
     */
    public function isAnyItemDeleted()
    {
        return $this->anyItemDeleted;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function setAnyItemDeleted($value)
    {
        return $this->anyItemDeleted = ($value === true);
    }

    /**
     * @internal
     *
     * @param \SplObjectStorage $cloneEntity
     *
     * @return EntityCollection
     */
    public function createClone(\SplObjectStorage $cloneEntity)
    {
        if ($this->isClone() && $cloneEntity->contains($this)) {
            return $cloneEntity[$this];
        }

        $entityClone = clone $this;
        $entityClone->isClone = true;

        if (!$cloneEntity->contains($this)) {
            $cloneEntity[$this] = $entityClone;
        }

        /**
         * @var int key
         * @var CollectableEntity $entity
         */
        foreach ($entityClone->collection as $key => $entity) {
            if (!$cloneEntity->contains($entity)) {
                $cloneEntity[$entity] = $entity->createClone($cloneEntity);
            }

            $entityClone->collection[$key] = $cloneEntity[$entity];
        }

        return $entityClone;
    }

    public function getArrayValuesField($field)
    {
        if (!($field = trim($field))) {
            throw new Main\ArgumentNullException('Не указан код поля');
        }

        $has_getter = false;
        if (func_num_args() > 1 && is_callable(func_get_arg(1))) { // по идее нужна еще проверка на соотвествие интерфейсу геттера, но этот интерфейс пока не определен у нас
            $has_getter = true;
            $getter = func_get_arg(1);
        }

        $array = [];
        foreach ($this as $item) {
            if ($has_getter) {
                $array[$item->getId()] = $getter($item->getField($field));
            } else {
                $array[$item->getId()] = $item->getField($field);
            }
        }

        return $array;
    }

    public function toArray()
    {
        $array = [];
        foreach ($this as $item) {
            $array[] = $item->toArray();
        }

        return $array;
    }
}
