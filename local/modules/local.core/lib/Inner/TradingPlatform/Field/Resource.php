<?php

namespace Local\Core\Inner\TradingPlatform\Field;


class Resource extends AbstractField
{
    const TYPE_SOURCE = 'SOURCE';
    const TYPE_SIMPLE = 'SIMPLE';
    const TYPE_SELECT = 'SELECT';
    const TYPE_LOGIC = 'LOGIC';
    const TYPE_IGNORE = 'IGNORE';

    /**
     * Получить заголовоки типов
     *
     * @return array
     */
    private function getTypesTitles()
    {
        return [
            self::TYPE_SOURCE => 'Источник данных',
            self::TYPE_SIMPLE => 'Свое значение',
            self::TYPE_SELECT => 'Выбрать из списка',
            self::TYPE_LOGIC => 'Условия',
            self::TYPE_IGNORE => 'Игнорировать поле',
        ];
    }


    /** @var array $arAllowTypeList Список доступных типов данных */
    protected $arAllowTypeList = [
        self::TYPE_SOURCE
    ];

    /**
     * Задает список доступных типов данных.<br/>
     * Задавать массив из списка констант с префиксом <b>TYPE_</b>
     *
     * @param $ar
     *
     * @return $this
     */
    public function setAllowTypeList($ar)
    {
        $this->arAllowTypeList = $ar;
        return $this;
    }

    /**
     * Получить список доступных типов данных
     *
     * @return array
     */
    private function getAllowTypeList()
    {
        return $this->arAllowTypeList;
    }

    /** @var integer $intStoreId ID магазина */
    protected $intStoreId;

    /**
     * Задать ID магазина
     *
     * @param $intStoreId
     *
     * @return $this
     */
    public function setStoreId($intStoreId)
    {
        $this->intStoreId = $intStoreId;
        return $this;
    }

    /**
     * Получить ID магазина
     *
     * @return int
     */
    protected function getStoreId()
    {
        return $this->intStoreId;
    }


    /** @inheritDoc */
    protected function execute()
    {
        $this->createBeginSelect();

        if (!empty($this->getValue()['TYPE'])) {
            switch ($this->getValue()['TYPE']) {
                case self::TYPE_SOURCE:
                    break;
                case self::TYPE_SIMPLE:
                    $this->initSimpleBranch();
                    break;
                case self::TYPE_SELECT:
                    $this->initSelectBranch();
                    break;
                case self::TYPE_LOGIC:
                    break;
                case self::TYPE_IGNORE:
                    break;
            }
        }
        $this->addToRender((new Infoblock())->setValue(print_r($this->getValue(), true))
            ->getRender());
    }

    /**
     * Добавить в рендер список выбора типа данных
     */
    private function createBeginSelect()
    {
        $arOptions = [];
        foreach ($this->getAllowTypeList() as $v) {
            $arOptions[$v] = $this->getTypesTitles()[$v];
        }
        if ($this->getIsRequired()) {
            unset($arOptions[self::TYPE_IGNORE]);
        }

        $obBeginSelect = (new Select())->setName($this->getName().'[TYPE]')
            ->setOptions($arOptions)
            ->setDefaultOption('-- Выберите тип данных --')
            ->setValue($this->getValue()['TYPE'] ?? null)
            ->setEvent([
                'onchange' => [
                    'LocalCoreTradingPlatform.refreshRow(\''.$this->getRowHash().'\')'
                ]
            ]);

        $this->addToRender($obBeginSelect->getRender());
    }


    /* ************* */
    /* SIMPLE BRANCH */
    /* ************* */

    /** @var AbstractField $_fieldSimpleField Поле, которое будет учавствовать в сценарии SIMPLE */
    protected $_fieldSimpleField;

    /**
     * Задает поле, которое будет учавствовать в SIMPLE сценарии.<br/>
     * Поле должно быть либо text, либо textarea.<br/>
     * Задавать название и значение у поля смысла не имеет, оно перебивается при добавлении в рендер.
     *
     * @param AbstractField $obSimpleField
     *
     * @return $this
     * @throws \Exception
     */
    public function setSimpleField(AbstractField $obSimpleField)
    {
        if (!($obSimpleField instanceof InputText) && !($obSimpleField instanceof Textarea)) {
            throw new \Exception('Поле должно быть text или textarea');
        }

        $this->_fieldSimpleField = $obSimpleField;
        return $this;
    }

    /**
     * Возвращает заданное поле для сценария SIMPLE
     *
     * @return InputText | Textarea
     */
    protected function getSimpleField()
    {
        return $this->_fieldSimpleField;
    }

    /**
     * Запускает сценарий ветки SIMPLE
     */
    private function initSimpleBranch()
    {
        if ($this->getSimpleField() instanceof AbstractField) {
            $obSimpleField = $this->getSimpleField()
                ->setName($this->getName().'[VALUE]')
                ->setValue($this->getValue()['VALUE']);

            $this->addToRender($obSimpleField->getRender());
        }
    }

    /* ************* */
    /* SELECT BRANCH */
    /* ************* */

    /** @var AbstractField $_fieldSimpleField Поле, которое будет учавствовать в сценарии SELECT */
    protected $_fieldSelectField;

    /**
     * Задает поле, которое учавствует в сценарции SELECT.<br/>
     * Задавать название и значение у поля смысла не имеет, оно перебивается при добавлении в рендер.
     *
     * @param Select $obSelectField
     *
     * @return $this
     */
    public function setSelectField(Select $obSelectField)
    {
        $this->_fieldSelectField = $obSelectField;
        return $this;
    }

    /**
     * Возвращает заданное поле для сценария SELECT
     *
     * @return Select
     */
    public function getSelectField()
    {
        return $this->_fieldSelectField;
    }

    /**
     * Запускает сценарий ветки SELECT
     */
    private function initSelectBranch()
    {
        if ($this->getSelectField() instanceof Select) {
            $obSelectField = $this->getSelectField()
                ->setName($this->getName().'[VALUE]')
                ->setValue($this->getValue()['VALUE']);

            $this->addToRender($obSelectField->getRender());
        }
    }
}