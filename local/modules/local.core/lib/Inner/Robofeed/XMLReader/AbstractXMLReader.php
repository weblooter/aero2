<?php

namespace Local\Core\Inner\Robofeed\XMLReader;


use Local\Core\Inner\Exception\FatalException, \Local\Core\Inner\Robofeed\Validator\AbstractValidator;

/**
 * Абстрактный класс ридера.<br/>
 * Пример вызова ридера:<br/>
 * <code>
 * $obReader = \Local\Core\Inner\Robofeed\XMLReader\Factory::factory(1);
 * $obReader->setScript(\Local\Core\Inner\Robofeed\XMLReader\AbstractXMLReader::SCRIPT_XSD_VALIDATE);
 * $obReader->setXmlPath( $_SERVER['DOCUMENT_ROOT'].'/example.xml' );
 * $obReaderResult = $obReader->run();
 * </code><br/>
 * Пример чтения версии робофида:<br/>
 * <code>
 * $obResult = \Local\Core\Inner\Robofeed\XMLReader\Factory::factory(1)
 * ->setXmlPath($_SERVER['DOCUMENT_ROOT'].'/example.xml')
 * ->getRobofeedVersion();
 * if( $obResult->isSuccess() )
 * {
 * dump($obResult->getData());
 * }
 * else
 * {
 * dump($obResult->getErrorMessages());
 * }
 * </code><br/>
 * Пример чтения даты создания робофида:<br/>
 * <code>
 * $obResult = \Local\Core\Inner\Robofeed\XMLReader\Factory::factory(1)
 * ->setXmlPath($_SERVER['DOCUMENT_ROOT'].'/example.xml')
 * ->getRobofeedLastModified();
 * if( $obResult->isSuccess() )
 * {
 * dump($obResult->getData());
 * }
 * else
 * {
 * dump($obResult->getErrorMessages());
 * }
 * </code><br/>
 *
 * @package Local\Core\Inner\Robofeed\XMLReader
 */
abstract class AbstractXMLReader
{
    use \Local\Core\Inner\Robofeed\Traites\AbstractClass;

    const SCRIPT_IMPORT = 'IMPORT';
    const SCRIPT_XSD_VALIDATE = 'XSD_VALIDATE';

    /** @var string $constScript Хранилище выбранного сценария */
    protected $constScript;

    /**
     * Задает сценарий работы ридера.
     *
     * @param $constScript
     *
     * @throws FatalException
     * @return $this
     */
    public function setScript($constScript)
    {
        switch( $constScript )
        {
            case self::SCRIPT_IMPORT:
            case self::SCRIPT_XSD_VALIDATE:
                $this->constScript = $constScript;
                break;
            default:
                throw new FatalException('Для ридера '.static::class.' задац сценарий "'.$constScript.'", которого нет в списке.');
                break;
        }
        return $this;
    }

    /** @var int $intImportProductLimit Лимит по кол-ву товаров в процессе импорта */
    protected $intImportProductLimit = 50;

    /**
     * Метод задает лимит по кол-ву товаров в процессе импорта
     *
     * @param int $intLimit
     *
     * @return $this
     */
    public function setImportProductLimit($intLimit)
    {
        $this->intImportProductLimit = $intLimit;
        return $this;
    }

    /** @var \SimpleXMLReader $obReader */
    protected $obReader;

    /** @var array $arSchema Массив карты схемы */
    protected $arSchema;

    /** @var \Bitrix\Main\Result $obResult Реситр резалта объекта */
    protected $obResult;

    /** @var array $arRootValues Регистр значений ROOT полей робофида */
    protected $arRootValues;

    /** @var array $arBodyValues Регистр значений BODY полей робофида */
    protected $arBodyValues;

    /** @var int $intMaxOffersErrorCountInValidation Максимальное кол-во ошибок в товарах в робофиде,
     * после достижения которых прекращается процесс валидации за ненадобностью.
     */
    protected $intMaxOffersErrorCountInValidation;

    /**
     * @var int $intOffersErrorCount Текущее кол-во ошибок среди товаров в робофиде.
     */
    protected $intOffersErrorCount = 0;

    /**
     * @var \Local\Core\Inner\Robofeed\Validator\AbstractValidator $obValidator Объект валидатора
     */
    protected $obValidator;

    /**
     * @var \Local\Core\Inner\Robofeed\Importer\AbstractImporter $obImport Объект импорта
     */
    protected $obImport;

    public function __construct()
    {
        try
        {
            $this->obReader = new \SimpleXMLReader();
            $this->obReader->registerCallback(
                '/robofeed/version',
                function($reader)
                    {
                        $this->callRobofeedVersion($reader);
                        return true;
                    }
            );
            $this->obReader->registerCallback(
                '/robofeed/lastModified',
                function($reader)
                    {
                        $this->callRobofeedLastModified($reader);
                        return true;
                    }
            );

            $this->arSchema = \Local\Core\Inner\Robofeed\Schema\Factory::factory(static::getVersion())
                ->getSchemaMap();
            $this->obValidator = \Local\Core\Inner\Robofeed\Validator\Factory::factory(static::getVersion());
            $this->obImporter = \Local\Core\Inner\Robofeed\Importer\Factory::factory(static::getVersion());
            $this->obResult = new \Bitrix\Main\Result();

            $this->intMaxOffersErrorCountInValidation = \Bitrix\Main\Config\Configuration::getInstance()
                                                            ->get('robofeed')['XMLReader']['max_offers_error_count_in_validation'] ?? 5;
        }
        catch( \Exception $e )
        {
            throw new FatalException('Во время создания ридера возникли проблемы: '.$e->getMessage());
        }
    }

    /**
     * Проверка версии робофида.
     *
     * @param \SimpleXMLReader $reader
     *
     * @return  bool
     * @throws \Bitrix\Main\SystemException
     * @throws FatalException
     */
    protected function callRobofeedVersion($reader)
    {
        $obElement = $reader->expandSimpleXml();

        /** @var \Local\Core\Inner\Robofeed\SchemaFields\ScalarField $obField */
        $obField = $this->arSchema['robofeed']['version'];
        if( !AbstractValidator::validateValue((string)$obElement, $obField, $this->obResult) )
        {
            throw new FatalException($this->obResult->getErrorMessages()[0]);
        }
        else
        {
            $value = $obField->getValidValue((string)$obElement);
            if( (string)$value != static::getVersion() )
            {
                throw new FatalException('Версия выбранной XSD схемы ('.static::getVersion().') и версия Robofeed XML ('.$value.') различаются!');
            }
            $this->arRootValues['version'] = $value;
        }

        return true;
    }

    /**
     * Проверка даты создания робофида.
     *
     * @param \SimpleXMLReader $reader
     *
     * @return  bool
     * @throws \Bitrix\Main\SystemException
     * @throws FatalException
     */
    protected function callRobofeedLastModified($reader)
    {
        $obElement = $reader->expandSimpleXml();

        /** @var \Local\Core\Inner\Robofeed\SchemaFields\ScalarField $obField */
        $obField = $this->arSchema['robofeed']['lastModified'];
        if( !AbstractValidator::validateValue((string)$obElement, $obField, $this->obResult) )
        {
            throw new FatalException($this->obResult->getErrorMessages()[0]);
        }
        else
        {
            $value = $obField->getValidValue((string)$obElement);
            $this->arRootValues['lastModified'] = $value;
        }

        return true;
    }


    /**
     * Получает версию робофида, извлекая ее из файла.
     *
     * @return string
     * @throws \Local\Core\Inner\Exception\FatalException
     */
    public function getRobofeedVersion()
    {
        $intVersion = null;

        $obResult = new \Bitrix\Main\Result();


        $this->checkFilledXmlPath();

        $obReader = new \SimpleXMLReader();
        $obReader->registerCallback(
            '/robofeed/version',
            function($reader) use ($obResult, &$intVersion)
                {
                    $obElement = $reader->expandSimpleXml();
                    /** @var \Local\Core\Inner\Robofeed\SchemaFields\ScalarField $obField */
                    $obField = $this->arSchema['robofeed']['version'];
                    if( AbstractValidator::validateValue((string)$obElement, $obField, $obResult) )
                    {
                        $intVersion = $obField->getValidValue((string)$obElement);
                    }
                }
        );
        $obReader->open($this->strXmlFilePath);
        $obReader->parse();
        $obReader->close();

        if( !$obResult->isSuccess() )
        {
            throw new FatalException(implode('<br/>', $obResult->getErrorMessages()));
        }

        if( is_null($intVersion) )
        {
            throw new FatalException('Не удалось определить версию Robofeed XML. Проверьте, что бы значение по пути robofeed->version было заполнено');
        }

        return $intVersion;
    }


    /**
     * Получает дату создания робофида, извлекая ее из файла.
     *
     * @return string
     * @throws \Local\Core\Inner\Exception\FatalException
     */
    public function getRobofeedLastModified()
    {
        $obResult = new \Bitrix\Main\Result();
        $strDate = null;

        $this->checkFilledXmlPath();

        $obReader = new \SimpleXMLReader();
        $obReader->registerCallback(
            '/robofeed/lastModified',
            function($reader) use ($obResult, &$strDate)
                {
                    $obElement = $reader->expandSimpleXml();
                    /** @var \Local\Core\Inner\Robofeed\SchemaFields\ScalarField $obField */
                    $obField = $this->arSchema['robofeed']['lastModified'];
                    if( AbstractValidator::validateValue((string)$obElement, $obField, $obResult) )
                    {
                        $strDate = $obField->getValidValue((string)$obElement);
                    }
                }
        );
        $obReader->open($this->strXmlFilePath);
        $obReader->parse();
        $obReader->close();


        if( !$obResult->isSuccess() )
        {
            throw new FatalException(implode('<br/>', $obResult->getErrorMessages()));
        }

        if( is_null($strDate) )
        {
            throw new FatalException('Не удалось определить дату создания Robofeed XML. Проверьте, что бы значение по пути robofeed->lastModified было заполнено');
        }

        return $strDate;
    }

    /**
     * Извлекает аттрибуты элемента.
     *
     * @param \SimpleXMLElement $obElem
     *
     * @return array
     */
    protected function getAttrs(\SimpleXMLElement $obElem)
    {
        $arAttrs = [];
        if( $obElem instanceof \SimpleXMLElement )
        {
            foreach( $obElem->attributes() as $k => $v )
            {
                $arAttrs[$k] = (string)$v;
            }
        }
        return $arAttrs;
    }

    abstract protected function getValidator();

    abstract protected function getImporter();
}