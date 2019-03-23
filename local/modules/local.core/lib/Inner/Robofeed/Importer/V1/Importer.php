<?php
namespace Local\Core\Inner\Robofeed\Importer\V1;


class Importer extends \Local\Core\Inner\Robofeed\Importer\AbstractImporter
{
    /** @inheritdoc */
    public static function getVersion()
    {
        return 1;
    }

    /** @deprecated  */
    public function run()
    {
    }

    public function importCategories($arFields)
    {
        dump($arFields);
    }

    public function importOffer($arFields)
    {
        dump($arFields);
    }

    private function importParams($arFields)
    {
        dump($arFields);
    }
}