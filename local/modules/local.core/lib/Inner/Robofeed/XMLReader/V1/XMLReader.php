<?php

namespace Local\Core\Inner\Robofeed\XMLReader\V1;


use Local\Core\Inner\Exception\FatalException;
use \Local\Core\Inner\Robofeed\Validator\AbstractValidator;

/**
 * Класс ридера версии 1
 *
 * Общеее описание читай у абстрактного класса \Local\Core\Inner\Robofeed\XMLReader\AbstractXMLReader
 * @see     \Local\Core\Inner\Robofeed\XMLReader\AbstractXMLReader
 *
 * @package Local\Core\Inner\Robofeed\XMLReader\V1
 */
class XMLReader extends \Local\Core\Inner\Robofeed\XMLReader\AbstractXMLReader
{
    private $intProductTotal = 0;
    private $intProductImportSuccess = 0;

    /**
     * @return \Local\Core\Inner\Robofeed\Validator\V1\Validator
     */
    protected function getValidator()
    {
        return $this->obValidator;
    }

    /**
     * @return \Local\Core\Inner\Robofeed\Importer\V1\Importer
     */
    protected function getImporter()
    {
        return $this->obImporter;
    }

    /** @inheritdoc */
    public static function getVersion()
    {
        return 1;
    }

    /** @inheritdoc */
    public function run()
    {
        $this->checkFilledXmlPath();

        if( $this->constScript == self::SCRIPT_IMPORT && is_null($this->intStoreId) )
        {
            throw new FatalException('Необходимо задать ID магазина для импорта');
        }

        $this->getImporter()
            ->setStoreId($this->intStoreId);

        $this->obReader->registerCallback(
            '/robofeed/defaultValues/offer',
            function($reader)
                {
                    return $this->callDefaultValuesOffer($reader);
                }
        );

        $this->obReader->registerCallback(
            '/robofeed/categories',
            function($reader)
                {
                    return $this->callCategories($reader);
                }
        );

        $this->obReader->registerCallback(
            '/robofeed/offers/offer',
            function($reader)
                {
                    $this->intProductTotal++;
                    return $this->callOffer($reader);
                }
        );


        $this->obReader->open($this->strXmlFilePath);
        try
        {
            $this->obReader->parse();
        }
        catch( \TypeError $e )
        {
            $this->obResult->addError(
                new \Bitrix\Main\Error(
                    'Во время проверки робофид XML произошла критическая ошибка. Убедитесь, что в Вашем робофиде XML нет ошибки, а теги закрыты. Самый быстрый способ найти ошибку - откройте робофид XML в браузере.'
                )
            );
        }
        $this->obReader->close();

        $this->obResult->setData(
            [
                'PRODUCT_TOTAL_COUNT' => $this->intProductTotal,
                'PRODUCT_SUCCESS_IMPORT' => $this->intProductImportSuccess,
            ]
        );

        return $this->obResult;
    }

    /**
     * @param \SimpleXMLReader $reader
     *
     * @return bool
     */
    protected function callDefaultValuesOffer($reader)
    {
        /**
         * @var \SimpleXMLElement                                   $obXml
         * @var \Local\Core\Inner\Robofeed\SchemaFields\ScalarField $obField
         */
        $obXml = $reader->expandSimpleXml();

        $obDefaultValuesOfferValidateResult = $this->getValidator()
            ->validateDefaultValuesOffer($obXml, $this->arSchema['robofeed']['defaultValues']['offer']);
        $this->arBodyValues['defaultValues']['offer'] = $obDefaultValuesOfferValidateResult->getData();
        if( !$obDefaultValuesOfferValidateResult->isSuccess() )
        {
            $this->obResult->addErrors($obDefaultValuesOfferValidateResult->getErrors());
        }

        return true;
    }

    /**
     * @param \SimpleXMLReader $reader
     *
     * @return bool
     */
    protected function callCategories($reader)
    {
        $obXml = $reader->expandSimpleXml();

        $obCategoriesValidateResult = $this->getValidator()
            ->validateCategories($obXml, $this->arSchema['robofeed']['categories']['category']);

        $this->arBodyValues['categories']['category'] = $obCategoriesValidateResult->getData();
        if( !$obCategoriesValidateResult->isSuccess() )
        {
            $this->obResult->addErrors($obCategoriesValidateResult->getErrors());
        }

        if( $this->constScript == self::SCRIPT_IMPORT )
        {
            $this->getImporter()
                ->importCategories($this->arBodyValues['categories']['category']);
        }

        return true;
    }

    /**
     * @param \SimpleXMLReader $reader
     *
     * @return bool
     */
    protected function callOffer($reader)
    {
        /**
         * @var \SimpleXMLElement                                   $obXml
         * @var \Local\Core\Inner\Robofeed\SchemaFields\ScalarField $obField
         */
        $obXml = $reader->expandSimpleXml();

        $obOfferValidateResult = $this->getValidator()
            ->validateOffer(
                $obXml,
                $this->arSchema['robofeed']['offers']['offer'],
                $this->arBodyValues['defaultValues']['offer'],
                $this->arBodyValues['categories']['category']
            );

        $arOfferFields = $obOfferValidateResult->getData();

        if( !$obOfferValidateResult->isSuccess() )
        {
            $this->obResult->addError(new \Bitrix\Main\Error('У товара с ID "'.$arOfferFields['@attr']['id'].'" выявлены следующие ошибки:'));
            $this->obResult->addErrors($obOfferValidateResult->getErrors());
            $this->intOffersErrorCount++;
        }


        switch( $this->constScript )
        {
            case self::SCRIPT_XSD_VALIDATE:
                if( $this->intOffersErrorCount >= $this->intMaxOffersErrorCountInValidation )
                {
                    $this->obResult->addError(new \Bitrix\Main\Error('Дальнейшая проверка товаров в Robofeed XML прекращена. Исправьте ошибки и попробуйте еще раз.'));
                    return false;
                }
                else
                {
                    return true;
                }
                break;

            case self::SCRIPT_IMPORT:

                if( $obOfferValidateResult->isSuccess() )
                {

                    if(
                    $this->getImporter()
                        ->importOffer($arOfferFields)
                    )
                    {
                        $this->intProductImportSuccess++;
                    }
                }

                return true;

                break;
        }
    }
}