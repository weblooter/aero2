<?php

namespace Local\Core\Inner\Robofeed\Converter;


use Local\Core\Inner\Exception\FatalException;

class YML
{
    private $strFilePath;
    private $isYML = null;
    private $strWriteTo;

    public function addToWrite($str)
    {
        $f = fopen($this->strWriteTo, 'a');
        fwrite($f, $str);
        fclose($f);
    }

    public function setFilePath($strFilePath)
    {
        $this->strFilePath = $strFilePath;
        return $this;
    }

    private $tagCategories;
    private $tagOffers;

    /**
     * @return int
     * @throws FatalException
     */
    public function execute()
    {

        if (is_null($this->strFilePath)) {
            throw new FatalException('Необходимо задать путь до файла');
        }
        $this->strWriteTo = \Local\Core\Inner\BxModified\CFile::makeLocalCorePath('', true, true).sha1($this->strFilePath).'.xml';
        if (file_exists($this->strWriteTo)) {
            unlink($this->strWriteTo);
        }

        $this->begin();

        $intFileId = \Local\Core\Inner\BxModified\CFile::saveFile(\CFile::MakeFileArray($this->strWriteTo), '/robofeed/convert/export_file/');
        unlink($this->strWriteTo);
        if ($intFileId < 1) {
            throw new FatalException('Не удалось сохранить сконвертрованный файл');
        }
        return $intFileId;
    }

    private function begin()
    {
        $obFile = fopen($this->strFilePath, 'r');
        $strFileCont = str_replace(["\r\n", "\r", "\n"], '', fread($obFile, 300));
        if (
        preg_match(<<<DOCHERE
/(\<[\s]{0,}yml\_catalog(.*?)date=(\'|\")([\d]{4,4}\-[\d]{2,2}\-[\d]{2,2}\s[\d]{2,2}\:[\d]{2,2})(\'|\"))/x
DOCHERE
            , $strFileCont, $matches)
        ) {
            $this->isYML = true;
        } else {
            throw new FatalException('Файл не является YML файлом - в теге "yml_catalog" не указан аттрибут "date", формат даты отличается от "YYYY-MM-DD hh:mm" или не удалось найти тег "yml_catalog" в первых 300 символах файла.');
        }

        $this->addToWrite('<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<robofeed>'.PHP_EOL.'<version>1</version>'.PHP_EOL.'<lastModified>'.date('Y-m-d H:i:s',
                strtotime($matches[sizeof($matches) - 2])).'</lastModified>'.PHP_EOL);

        $obReader = new \SimpleXMLReader();

        $obReader->registerCallback('/yml_catalog/shop/categories/category', function ($reader)
            {
                return $this->extractCategories($reader);
            });

        $obReader->registerCallback('/yml_catalog/shop/offers/offer', function ($reader)
            {
                return $this->extractOffer($reader);
            });

        $obReader->open($this->strFilePath);
        $obReader->parse();
        $obReader->close();

        if ($this->tagOffers == 'opened') {
            $this->addToWrite('</offers>'.PHP_EOL);
            $this->tagOffers = 'closed';
        }
        $this->addToWrite('</robofeed>');
    }

    /**
     * @param \SimpleXMLReader $reader
     *
     * @return bool
     * @throws FatalException
     */
    private function extractCategories($reader)
    {
        if (!$this->isYML) {
            throw new FatalException('Это не YML');
        }

        if (is_null($this->tagCategories)) {
            $this->addToWrite('<categories>'.PHP_EOL);
            $this->tagCategories = 'opened';
        }

        /** @var \SimpleXMLElement $obElement */
        $obElement = $reader->expandSimpleXml();

        $arAttr = [];
        foreach ($obElement->attributes() as $k => $v) {
            $arAttr[$k] = (string)$v;
        }

        $str = '<category id="'.$arAttr['id'].'" '.(!is_null($arAttr['parentId']) ? 'parentId="'.$arAttr['parentId'].'"' : '').'>'.htmlspecialchars((string)$obElement).'</category>'.PHP_EOL;
        $this->addToWrite($str);

        return true;
    }

    /**
     * @param \SimpleXMLReader $reader
     *
     * @return bool
     * @throws FatalException
     */
    private function extractOffer($reader)
    {
        if (!$this->isYML) {
            throw new FatalException('Это не YML');
        }

        if ($this->tagCategories == 'opened') {
            $this->addToWrite('</categories>'.PHP_EOL);
            $this->tagCategories = 'closed';
        }

        if (is_null($this->tagOffers)) {
            $this->addToWrite('<offers>'.PHP_EOL);
            $this->tagOffers = 'opened';
        }

        /** @var \SimpleXMLElement $obElement */
        $obElement = $reader->expandSimpleXml();

        $arAttr = [];
        foreach ($obElement->attributes() as $k => $v) {
            $arAttr[$k] = substr((string)$v, 0, 9);
        }

        $str = '<offer id="'.$arAttr['id'].'" '.(!is_null($arAttr['group_id']) ? 'group_id="'.$arAttr['group_id'].'"' : '').'>'.PHP_EOL;

        $str .= '<inStock>'.(!empty($arAttr['available']) ? $arAttr['available'] : 'true').'</inStock>'.PHP_EOL;

        if (trim($arAttr['type']) == substr('vendor.model', 0, 9)) {
            $str .= '<fullName>'.substr(htmlspecialchars((string)$obElement->model), 0, 255).'</fullName>'.PHP_EOL;
            $str .= '<simpleName>'.substr(htmlspecialchars((string)$obElement->model), 0, 255).'</simpleName>'.PHP_EOL;
        } else {
            $str .= '<fullName>'.substr(htmlspecialchars((string)$obElement->name), 0, 255).'</fullName>'.PHP_EOL;
            $str .= '<simpleName>'.substr(htmlspecialchars((string)$obElement->name), 0, 255).'</simpleName>'.PHP_EOL;
        }

        $str .= '<manufacturer>'.htmlspecialchars(substr((string)$obElement->vendor, 0, 255)).'</manufacturer>'.PHP_EOL;
        $str .= '<manufacturerCode>'.htmlspecialchars(substr((string)$obElement->vendorCode, 0, 255)).'</manufacturerCode>'.PHP_EOL;

        if (!empty(htmlspecialchars((string)$obElement->vendorCode))) {
            $str .= '<article>'.htmlspecialchars(substr((string)$obElement->vendorCode, 0, 255)).'</article>'.PHP_EOL;
        } else {
            $str .= '<article>'.substr($arAttr['id'], 0, 9).'</article>'.PHP_EOL;
        }

        $str .= '<url>'.htmlspecialchars(substr((string)$obElement->url, 0, 255)).'</url>'.PHP_EOL;
        $str .= '<price>'.substr((string)$obElement->price, 0, 11).'</price>'.PHP_EOL;
        $str .= '<oldPrice>'.substr((string)$obElement->oldprice, 0, 11).'</oldPrice>'.PHP_EOL;
        $str .= '<currencyCode>';
        switch ((string)$obElement->currencyId) {
            case 'RUR':
                $str .= 'RUB';
                break;
            case 'USD':
            case 'EUR':
            case 'UAH':
            case 'KZT':
            case 'BYN':
                $str .= (string)$obElement->currencyId;
                break;
        }
        $str .= '</currencyCode>'.PHP_EOL;
        $str .= '<categoryId>'.substr((string)$obElement->categoryId, 0, 9).'</categoryId>'.PHP_EOL;

        foreach ($obElement->picture as $obPicture) {
            $str .= '<image>'.substr(htmlspecialchars((string)$obPicture), 0, 255).'</image>'.PHP_EOL;
        }

        $str .= '<salesNotes>'.substr(htmlspecialchars((string)$obElement->{'sales_notes'}), 0, 50).'</salesNotes>'.PHP_EOL;

        $str .= '<quantity>1</quantity>'.PHP_EOL.'<unitOfMeasure>PCS</unitOfMeasure>'.PHP_EOL;
        $str .= '<minQuantity>'.(!empty((string)$obElement->{'min-quantity'}) ? substr((string)$obElement->{'min-quantity'}, 0, 9) : 1).'</minQuantity>'.PHP_EOL;

        $str .= '<description>'.htmlspecialchars(substr(strip_tags((string)$obElement->description), 0, 3000)).'</description>'.PHP_EOL;
        if (!empty((string)$obElement->adult)) {
            $str .= '<isSex>'.(string)$obElement->adult.'</isSex>'.PHP_EOL;
        }
        $str .= '<manufacturerWarranty>'.(!empty((string)$obElement->{'manufacturer_warranty'}) ? (string)$obElement->{'manufacturer_warranty'} : 'true').'</manufacturerWarranty>'.PHP_EOL;
        if (!empty((string)$obElement->{'downloadable'})) {
            $str .= '<isSoftware>'.(string)$obElement->{'downloadable'}.'</isSoftware>'.PHP_EOL;
        }

        if (!empty((string)$obElement->weight)) {
            $str .= '<weight>'.substr((string)$obElement->weight, 0, 9).'</weight>'.PHP_EOL;
            $str .= '<weightUnitCode>KGM</weightUnitCode>'.PHP_EOL;
        }

        if (!empty((string)$obElement->dimensions)) {
            $ardimensions = explode('/', (string)$obElement->dimensions);
            $ardimensions = array_map('trim', $ardimensions);

            $str .= '<length>'.substr($ardimensions[0], 0, 9).'</length>'.PHP_EOL;
            $str .= '<lengthUnitCode>CMT</lengthUnitCode>'.PHP_EOL;
            $str .= '<width>'.substr($ardimensions[1], 0, 9).'</width>'.PHP_EOL;
            $str .= '<widthUnitCode>CMT</widthUnitCode>'.PHP_EOL;
            $str .= '<height>'.substr($ardimensions[], 0, 9).'</height>'.PHP_EOL;
            $str .= '<heightUnitCode>CMT</heightUnitCode>'.PHP_EOL;
        }

        foreach ($obElement->param as $obParam) {
            if (empty(trim((string)$obParam))) {
                continue;
            }
            $name = $obParam->attributes();
            $name = substr(htmlspecialchars(trim((string)$name['name'])), 0, 100);
            $code = substr(strtoupper(\Cutil::translit($name, "ru", array("replace_space" => "_", "replace_other" => "_"))), 0, 50);
            $str .= '<param name="'.$name.'" code="'.$code.'">'.substr(htmlspecialchars((string)$obParam), 0, 255).'</param>'.PHP_EOL;
        }

        if (!empty((string)$obElement->delivery)) {
            $strOption = '';
            foreach ($obElement->{'delivery-options'}->option as $obOption) {
                $strDays = (string)$obOption->attributes()['days'];
                $strDays = explode('-', $strDays);
                if (sizeof($strDays) < 2) {
                    $strDays[1] = $strDays[0];
                }

                $strOption .= '<option';
                $strOption .= ' priceFrom="'.substr((string)$obOption->attributes()['cost'], 0, 9).'"';
                $strOption .= ' priceTo="'.substr((string)$obOption->attributes()['cost'], 0, 9).'"';
                $strOption .= ' currencyCode="RUB"';
                $strOption .= ' daysFrom="'.substr($strDays[1], 0, 2).'"';
                $strOption .= ' daysTo="'.substr($strDays[2], 0, 2).'"';
                $strOption .= ' orderBefore="'.substr((string)$obOption->attributes()['order-before'], 0, 2).'"';
                $strOption .= ' deliveryRegion="all"';
                $strOption .= '></option>'.PHP_EOL;
            }

            if (!empty($strOption)) {
                $str .= '<delivery available="true">'.PHP_EOL;
                $str .= $strOption;
                $str .= '</delivery>'.PHP_EOL;
            } else {
                $str .= '<delivery available="false"></delivery>'.PHP_EOL;
            }
        } else {
            $str .= '<delivery available="false"></delivery>'.PHP_EOL;
        }

        if (!empty((string)$obElement->pickup)) {

            $strOption = '';
            foreach ($obElement->{'pickup-options'}->option as $obOption) {
                $strDays = (string)$obOption->attributes()['days'];
                $strDays = explode('-', $strDays);
                if (sizeof($strDays) < 2) {
                    $strDays[1] = $strDays[0];
                }

                $strOption .= '<option';
                $strOption .= ' price="'.substr((string)$obOption->attributes()['cost'], 0, 9).'"';
                $strOption .= ' currencyCode="RUB"';
                $strOption .= ' supplyFrom="'.substr($strDays[1], 0, 2).'"';
                $strOption .= ' supplyTo="'.substr($strDays[2], 0, 2).'"';
                $strOption .= ' orderBefore="'.substr((string)$obOption->attributes()['order-before'], 0, 2).'"';
                $strOption .= '></option>'.PHP_EOL;
            }

            if (!empty($strOption)) {
                $str .= '<pickup available="true">'.PHP_EOL;
                $str .= $strOption;
                $str .= '</pickup>'.PHP_EOL;
            } else {
                $str .= '<pickup available="false"></pickup>'.PHP_EOL;
            }
        } else {
            $str .= '<pickup available="false"></pickup>'.PHP_EOL;
        }

        if (!empty((string)$obElement->{'country_of_origin'})) {
            $countyCode = '';
            switch (mb_strtoupper(trim((string)$obElement->{'country_of_origin'}))) {
                case 'АВСТРАЛИЯ':
                    $countyCode = 'AUS';
                    break;
                case 'АВСТРИЯ':
                    $countyCode = 'AUT';
                    break;
                case 'АЗЕРБАЙДЖАН':
                    $countyCode = 'AZE';
                    break;
                case 'АЛБАНИЯ':
                    $countyCode = 'ALB';
                    break;
                case 'АЛЖИР':
                    $countyCode = 'DZA';
                    break;
                case 'АМЕРИКАНСКИЕ ВИРГИНСКИЕ ОСТРОВА':
                    $countyCode = 'VIR';
                    break;
                case 'АНГИЛЬЯ':
                    $countyCode = 'AIA';
                    break;
                case 'АНГОЛА':
                    $countyCode = 'AGO';
                    break;
                case 'АНДОРРА':
                    $countyCode = 'AND';
                    break;
                case 'АНТИГУА И БАРБУ':
                    $countyCode = 'ATG';
                    break;
                case 'ГЕРМАНИЯ':
                    $countyCode = 'DEU';
                    break;
                case 'ГИБРАЛТАР':
                    $countyCode = 'GIB';
                    break;
                case 'ГОНДУРАС':
                    $countyCode = 'HND';
                    break;
                case 'ГОНКОНГ':
                    $countyCode = 'HKG';
                    break;
                case 'ГРЕНАДА':
                    $countyCode = 'GRD';
                    break;
                case 'ГРЕНЛАНДИЯ':
                    $countyCode = 'GRL';
                    break;
                case 'ГРЕЦИЯ':
                    $countyCode = 'GRC';
                    break;
                case 'ГРУЗИЯ':
                    $countyCode = 'GEO';
                    break;
                case 'ДАНИЯ':
                    $countyCode = 'DNK';
                    break;
                case 'ДЕМОКРАТИЧЕСКАЯ РЕСПУБЛИКА КОНГО':
                    $countyCode = 'COD';
                    break;
                case 'ДЖИБУТИ':
                    $countyCode = 'DJI';
                    break;
                case 'ДОМИНИКА':
                    $countyCode = 'DMA';
                    break;
                case 'ДОМИНИКАНСКАЯ РЕСПУБЛИКА':
                    $countyCode = 'DOM';
                    break;
                case 'ЕГИПЕТ':
                    $countyCode = 'EGY';
                    break;
                case 'ЗАМБИЯ':
                    $countyCode = 'ZMB';
                    break;
                case 'ЗИМБАБВЕ':
                    $countyCode = 'ZWE';
                    break;
                case 'ЙЕМЕН':
                    $countyCode = 'YEM';
                    break;
                case 'ИЗРАИЛЬ':
                    $countyCode = 'ISR';
                    break;
                case 'ИНДИЯ':
                    $countyCode = 'IND';
                    break;
                case 'ИНДОНЕЗИЯ':
                    $countyCode = 'IDN';
                    break;
                case 'ИОРДАНИЯ':
                    $countyCode = 'JOR';
                    break;
                case 'ИРАК':
                    $countyCode = 'IRQ';
                    break;
                case 'ИРАН':
                    $countyCode = 'IRN';
                    break;
                case 'ИРЛАНДИЯ':
                    $countyCode = 'IRL';
                    break;
                case 'ИСЛАНДИЯ':
                    $countyCode = 'ISL';
                    break;
                case 'ИСПАНИЯ':
                    $countyCode = 'ESP';
                    break;
                case 'ИТАЛИЯ':
                    $countyCode = 'ITA';
                    break;
                case 'КАБО-ВЕРДЕ':
                    $countyCode = 'CPV';
                    break;
                case 'КАЗАХСТАН':
                    $countyCode = 'KAZ';
                    break;
                case 'КАЙМАНОВЫ ОСТРОВА':
                    $countyCode = 'CYM';
                    break;
                case 'КАМБОДЖА':
                    $countyCode = 'KHM';
                    break;
                case 'КАМЕРУН':
                    $countyCode = 'CMR';
                    break;
                case 'КАНАДА':
                    $countyCode = 'CAN';
                    break;
                case 'КАТАР':
                    $countyCode = 'QAT';
                    break;
                case 'КЕНИЯ':
                    $countyCode = 'KEN';
                    break;
                case 'КИПР':
                    $countyCode = 'CYP';
                    break;
                case 'КИРГИЗИЯ':
                    $countyCode = 'KGZ';
                    break;
                case 'КИРИБАТИ':
                    $countyCode = 'KIR';
                    break;
                case 'КИТАЙ':
                    $countyCode = 'CHN';
                    break;
                case 'КОЛУМБИЯ':
                    $countyCode = 'COL';
                    break;
                case 'КОМОРСКИЕ ОСТРОВА':
                    $countyCode = 'COM';
                    break;
                case 'КОСТА-РИКА':
                    $countyCode = 'CRI';
                    break;
                case 'КОТ-Д’ИВУАР':
                    $countyCode = 'CIV';
                    break;
                case 'КУБА':
                    $countyCode = 'CUB';
                    break;
                case 'КУВЕЙТ':
                    $countyCode = 'KWT';
                    break;
                case 'ЛАОС':
                    $countyCode = 'LAO';
                    break;
                case 'ЛАТВИЯ':
                    $countyCode = 'LVA';
                    break;
                case 'ЛЕСОТО':
                    $countyCode = 'LSO';
                    break;
                case 'ЛИБЕРИЯ':
                    $countyCode = 'LBR';
                    break;
                case 'ЛИВАН':
                    $countyCode = 'LBN';
                    break;
                case 'ЛИВИЯ':
                    $countyCode = 'LBY';
                    break;
                case 'ЛИТВА':
                    $countyCode = 'LTU';
                    break;
                case 'ЛИХТЕНШТЕЙН':
                    $countyCode = 'LIE';
                    break;
                case 'ЛЮКСЕМБУРГ':
                    $countyCode = 'LUX';
                    break;
                case 'МАВРИКИЙ':
                    $countyCode = 'MUS';
                    break;
                case 'МАВРИТАНИЯ':
                    $countyCode = 'MRT';
                    break;
                case 'МАДАГАСКАР':
                    $countyCode = 'MDG';
                    break;
                case 'МАЙОТТА':
                    $countyCode = 'MYT';
                    break;
                case 'МАКАО':
                    $countyCode = 'MAC';
                    break;
                case 'МАКЕДОНИЯ':
                    $countyCode = 'MKD';
                    break;
                case 'МАЛАВИ':
                    $countyCode = 'MWI';
                    break;
                case 'МАЛАЙЗИЯ':
                    $countyCode = 'MYS';
                    break;
                case 'МАЛИ':
                    $countyCode = 'MLI';
                    break;
                case 'МАЛЬДИВЫ':
                    $countyCode = 'MDV';
                    break;
                case 'МАЛЬТА':
                    $countyCode = 'MLT';
                    break;
                case 'МАРОККО':
                    $countyCode = 'MAR';
                    break;
                case 'МАРШАЛЛОВЫ ОСТРОВА':
                    $countyCode = 'MHL';
                    break;
                case 'МЕКСИКА':
                    $countyCode = 'MEX';
                    break;
                case 'МОЗАМБИК':
                    $countyCode = 'MOZ';
                    break;
                case 'МОЛДОВА':
                    $countyCode = 'MDA';
                    break;
                case 'МОНАКО':
                    $countyCode = 'MCO';
                    break;
                case 'МОНГОЛИЯ':
                    $countyCode = 'MNG';
                    break;
                case 'МЬЯНМА':
                    $countyCode = 'MMR';
                    break;
                case 'НАМИБИЯ':
                    $countyCode = 'NAM';
                    break;
                case 'НАУРУ':
                    $countyCode = 'NRU';
                    break;
                case 'НЕПАЛ':
                    $countyCode = 'NPL';
                    break;
                case 'НИГЕР':
                    $countyCode = 'NER';
                    break;
                case 'НИГЕРИЯ':
                    $countyCode = 'NGA';
                    break;
                case 'НИДЕРЛАНДЫ':
                    $countyCode = 'NLD';
                    break;
                case 'НИКАРАГУА':
                    $countyCode = 'NIC';
                    break;
                case 'НОВАЯ ЗЕЛАНДИЯ':
                    $countyCode = 'NZL';
                    break;
                case 'НОВАЯ КАЛЕДОНИЯ':
                    $countyCode = 'NCL';
                    break;
                case 'НОРВЕГИЯ':
                    $countyCode = 'NOR';
                    break;
                case 'ОБЪЕДИНЁННЫЕ АРАБСКИЕ ЭМИРАТЫ':
                    $countyCode = 'ARE';
                    break;
                case 'ОМАН':
                    $countyCode = 'OMN';
                    break;
                case 'ОСТРОВА КУКА':
                    $countyCode = 'COK';
                    break;
                case 'ПАКИСТАН':
                    $countyCode = 'PAK';
                    break;
                case 'ПАЛАУ':
                    $countyCode = 'PLW';
                    break;
                case 'ПАНАМА':
                    $countyCode = 'PAN';
                    break;
                case 'ПАПУА - НОВАЯ ГВИНЕЯ':
                    $countyCode = 'PNG';
                    break;
                case 'ПАРАГВАЙ':
                    $countyCode = 'PRY';
                    break;
                case 'ПЕРУ':
                    $countyCode = 'PER';
                    break;
                case 'ПОЛЬША':
                    $countyCode = 'POL';
                    break;
                case 'ПОРТУГАЛИЯ':
                    $countyCode = 'PRT';
                    break;
                case 'РЕСПУБЛИКА КОНГО':
                    $countyCode = 'COG';
                    break;
                case 'РЕЮНЬОН':
                    $countyCode = 'REU';
                    break;
                case 'РОССИЯ':
                    $countyCode = 'RUS';
                    break;
                case 'РУАНДА':
                    $countyCode = 'RWA';
                    break;
                case 'РУМЫНИЯ':
                    $countyCode = 'ROU';
                    break;
                case 'САМОА':
                    $countyCode = 'WSM';
                    break;
                case 'САН-МАРИНО':
                    $countyCode = 'SMR';
                    break;
                case 'САН-ТОМЕ И ПРИНСИПИ':
                    $countyCode = 'STP';
                    break;
                case 'САУДОВСКАЯ АРАВИЯ':
                    $countyCode = 'SAU';
                    break;
                case 'СВАЗИЛЕНД':
                    $countyCode = 'SWZ';
                    break;
                case 'СЕВЕРНАЯ КОРЕЯ':
                    $countyCode = 'PRK';
                    break;
                case 'СЕЙШЕЛЬСКИЕ ОСТРОВА':
                    $countyCode = 'SYC';
                    break;
                case 'СЕНЕГАЛ':
                    $countyCode = 'SEN';
                    break;
                case 'СЕНТ-ВИНСЕНТ И ГРЕНАДИНЫ':
                    $countyCode = 'VCT';
                    break;
                case 'СЕНТ-КИТС И НЕВИС':
                    $countyCode = 'KNA';
                    break;
                case 'СЕНТ-ЛЮСИЯ':
                    $countyCode = 'LCA';
                    break;
                case 'СЕРБИЯ':
                    $countyCode = 'SRB';
                    break;
                case 'СИНГАПУР':
                    $countyCode = 'SGP';
                    break;
                case 'СИРИЯ':
                    $countyCode = 'SYR';
                    break;
                case 'СЛОВАКИЯ':
                    $countyCode = 'SVK';
                    break;
                case 'СЛОВЕНИЯ':
                    $countyCode = 'SVN';
                    break;
                case 'СОМАЛИ':
                    $countyCode = 'SOM';
                    break;
                case 'СУДАН':
                    $countyCode = 'SDN';
                    break;
                case 'СУРИНАМ':
                    $countyCode = 'SUR';
                    break;
                case 'США':
                    $countyCode = 'USA';
                    break;
                case 'СЬЕРРА-ЛЕОНЕ':
                    $countyCode = 'SLE';
                    break;
                case 'ТАДЖИКИСТАН':
                    $countyCode = 'TJK';
                    break;
                case 'ТАИЛАНД':
                    $countyCode = 'THA';
                    break;
                case 'ТАНЗАНИЯ':
                    $countyCode = 'TZA';
                    break;
                case 'ТЁРКС И КАЙКОС':
                    $countyCode = 'TCA';
                    break;
                case 'ТОГО':
                    $countyCode = 'TGO';
                    break;
                case 'ТОНГА':
                    $countyCode = 'TON';
                    break;
                case 'ТРИНИДАД И ТОБАГО':
                    $countyCode = 'TTO';
                    break;
                case 'ТУВАЛУ':
                    $countyCode = 'TUV';
                    break;
                case 'ТУНИС':
                    $countyCode = 'TUN';
                    break;
                case 'ТУРКМЕНИСТАН':
                    $countyCode = 'TKM';
                    break;
                case 'ТУРЦИЯ':
                    $countyCode = 'TUR';
                    break;
                case 'УГАНДА':
                    $countyCode = 'UGA';
                    break;
                case 'УЗБЕКИСТАН':
                    $countyCode = 'UZB';
                    break;
                case 'УКРАИНА':
                    $countyCode = 'UKR';
                    break;
                case 'УРУГВАЙ':
                    $countyCode = 'URY';
                    break;
                case 'ФЕДЕРАТИВНЫЕ ШТАТЫ МИКРОНЕЗИИ':
                    $countyCode = 'FSM';
                    break;
                case 'ФИДЖИ':
                    $countyCode = 'FJI';
                    break;
                case 'ФИЛИППИНЫ':
                    $countyCode = 'PHL';
                    break;
                case 'ФИНЛЯНДИЯ':
                    $countyCode = 'FIN';
                    break;
                case 'ФРАНЦИЯ':
                    $countyCode = 'FRA';
                    break;
                case 'ФРАНЦУЗСКАЯ ГВИАНА':
                    $countyCode = 'GUF';
                    break;
                case 'ФРАНЦУЗСКАЯ ПОЛИНЕЗИЯ':
                    $countyCode = 'PYF';
                    break;
                case 'ХОРВАТИЯ':
                    $countyCode = 'HRV';
                    break;
                case 'ЧАД':
                    $countyCode = 'TCD';
                    break;
                case 'ЧЕРНОГОРИЯ':
                    $countyCode = 'MNE';
                    break;
                case 'ЧЕХИЯ':
                    $countyCode = 'CZE';
                    break;
                case 'ЧИЛИ':
                    $countyCode = 'CHL';
                    break;
                case 'ШВЕЙЦАРИЯ':
                    $countyCode = 'CHE';
                    break;
                case 'ШВЕЦИЯ':
                    $countyCode = 'SWE';
                    break;
                case 'ШРИ-ЛАНКА':
                    $countyCode = 'LKA';
                    break;
                case 'ЭКВАДОР':
                    $countyCode = 'ECU';
                    break;
                case 'ЭКВАТОРИАЛЬНАЯ ГВИНЕЯ':
                    $countyCode = 'GNQ';
                    break;
                case 'ЭРИТРЕЯ':
                    $countyCode = 'ERI';
                    break;
                case 'ЭСТОНИЯ':
                    $countyCode = 'EST';
                    break;
                case 'ЭФИОПИЯ':
                    $countyCode = 'ETH';
                    break;
                case 'ЮАР':
                    $countyCode = 'ZAF';
                    break;
                case 'ЮЖНАЯ КОРЕЯ':
                    $countyCode = 'KOR';
                    break;
                case 'ЯМАЙКА':
                    $countyCode = 'JAM';
                    break;
                case 'ЯПОНИЯ':
                    $countyCode = 'JPN';
                    break;
            }
            if (!empty($countyCode)) {
                $str .= '<countryOfProductionCode>'.$countyCode.'</countryOfProductionCode>';
            }
        }

        $str .= '</offer>'.PHP_EOL;
        $this->addToWrite($str);
        return true;
    }
}