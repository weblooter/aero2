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
        file_put_contents($this->strWriteTo, $str, FILE_APPEND);
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

        if( is_null($this->strFilePath) )
        {
            throw new FatalException('Необходимо задать путь до файла');
        }
        $this->strWriteTo = \Local\Core\Inner\BxModified\CFile::makeLocalCorePath('', true, true).sha1($this->strFilePath).'.xml';
        if( file_exists($this->strWriteTo) )
        {
            unlink($this->strWriteTo);
        }

        $this->begin();

        $intFileId = \Local\Core\Inner\BxModified\CFile::saveFile(\CFile::MakeFileArray($this->strWriteTo), '/robofeed/convert/export_file/');
        if( $intFileId < 1 )
        {
            throw new FatalException('Не удалось сохранить сконвертрованный файл');
        }
        copy($this->strWriteTo, $_SERVER['DOCUMENT_ROOT'].'/tmp2.xml');
//        unlink($this->strWriteTo);
        return $intFileId;
    }

    private function begin()
    {
        $obFile = fopen($this->strFilePath, 'r');
        $strFileCont = fread($obFile, 300);
        if(
        preg_match(
            <<<DOCHERE
/(yml\_catalog(.*?)date=(\'|\")([\d]{4,4}\-[\d]{2,2}\-[\d]{2,2}\s[\d]{2,2}\:[\d]{2,2})(\'|\"))/ux
DOCHERE
            ,
            $strFileCont,
            $matches
        )
        )
        {
            $this->isYML = true;
        }
        else
        {
            throw new FatalException('Файл не является YML файлом - в теге "yml_catalog" не указан аттрибут "date" или не удалось найти тег "yml_catalog" в первых 300 символах файла. Так же проверьте, что бы в начале файла не было <u>&lt;!DOCTYPE yml_catalog SYSTEM &quot;shops.dtd&quot;&gt;</u>');
        }

        $this->addToWrite('<?xml version="1.0" encoding="UTF-8"?><robofeed><version>1</version><lastModified>'.date('Y-m-d H:i:s', strtotime($matches[sizeof($matches) - 2])).'</lastModified>');

        $obReader = new \SimpleXMLReader();

        $obReader->registerCallback(
            '/yml_catalog/shop/categories/category',
            function($reader)
                {
                    return $this->extractCategories($reader);
                }
        );

        $obReader->registerCallback(
            '/yml_catalog/shop/offers/offer',
            function($reader)
                {
                    return $this->extractOffer($reader);
                }
        );

        $obReader->open($this->strFilePath);
        $obReader->parse();
        $obReader->close();

        if( $this->tagOffers == 'opened' )
        {
            $this->addToWrite('</offers>');
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
        if( !$this->isYML )
        {
            throw new FatalException('Это не YML');
        }

        if( is_null($this->tagCategories) )
        {
            $this->addToWrite('<categories>');
            $this->tagCategories = 'opened';
        }

        /** @var \SimpleXMLElement $obElement */
        $obElement = $reader->expandSimpleXml();

        $arAttr = [];
        foreach( $obElement->attributes() as $k => $v )
        {
            $arAttr[$k] = (string)$v;
        }

        $str = '<category id="'.$arAttr['id'].'" '.( !is_null($arAttr['parentId']) ? 'parentId="'.$arAttr['parentId'].'"' : '' ).'>'.(string)$obElement.'</category>';
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
        if( !$this->isYML )
        {
            throw new FatalException('Это не YML');
        }

        if( $this->tagCategories == 'opened' )
        {
            $this->addToWrite('</categories>');
            $this->tagCategories = 'closed';
        }

        if( is_null($this->tagOffers) )
        {
            $this->addToWrite('<offers>');
            $this->tagOffers = 'opened';
        }

        /** @var \SimpleXMLElement $obElement */
        $obElement = $reader->expandSimpleXml();

        $arAttr = [];
        foreach( $obElement->attributes() as $k => $v )
        {
            $arAttr[$k] = substr((string)$v, 0, 9);
        }

        $str = '<offer id="'.$arAttr['id'].'" '.( !is_null($arAttr['group_id']) ? 'group_id="'.$arAttr['group_id'].'"' : '' ).'>';

        $str .= '<inStock>'.( !empty($arAttr['available']) ? $arAttr['available'] : 'true' ).'</inStock>';

        if( trim($arAttr['type']) == substr('vendor.model', 0, 9) )
        {
            $str .= '<fullName>'.substr(htmlspecialchars((string)$obElement->model), 0, 255).'</fullName>';
            $str .= '<simpleName>'.substr(htmlspecialchars((string)$obElement->model), 0, 255).'</simpleName>';
        }
        else
        {
            $str .= '<fullName>'.substr(htmlspecialchars((string)$obElement->name), 0, 255).'</fullName>';
            $str .= '<simpleName>'.substr(htmlspecialchars((string)$obElement->name), 0, 255).'</simpleName>';
        }

        $str .= '<manufacturer>'.htmlspecialchars(substr((string)$obElement->vendor, 0, 255)).'</manufacturer>';
        $str .= '<manufacturerCode>'.htmlspecialchars(substr((string)$obElement->vendorCode, 0, 255)).'</manufacturerCode>';

        if( !empty(htmlspecialchars((string)$obElement->vendorCode)) )
        {
            $str .= '<article>'.htmlspecialchars(substr((string)$obElement->vendorCode, 0, 255)).'</article>';
        }
        else
        {
            $str .= '<article>'.substr($arAttr['id'], 0, 9).'</article>';
        }

        $str .= '<url>'.htmlspecialchars(substr((string)$obElement->url, 0, 255)).'</url>';
        $str .= '<price>'.substr((string)$obElement->price, 0, 11).'</price>';
        $str .= '<oldPrice>'.substr((string)$obElement->oldprice, 0, 11).'</oldPrice>';
        $str .= '<currencyCode>';
        switch( (string)$obElement->currencyId )
        {
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
        $str .= '</currencyCode>';
        $str .= '<categoryId>'.substr((string)$obElement->categoryId, 0, 9).'</categoryId>';

        foreach( $obElement->picture as $obPicture )
        {
            $str .= '<image>'.substr(htmlspecialchars((string)$obPicture), 0, 255).'</image>';
        }

        $str .= '<salesNotes>'.substr(htmlspecialchars((string)$obElement->{'sales_notes'}), 0, 50).'</salesNotes>';

        $str .= '<quantity>1</quantity><unitOfMeasure>PCS</unitOfMeasure>';
        $str .= '<minQuantity>'.( !empty((string)$obElement->{'min-quantity'}) ? substr((string)$obElement->{'min-quantity'}, 0, 9) : 1 ).'</minQuantity>';

        $str .= '<description>'.htmlspecialchars(substr(strip_tags((string)$obElement->description), 0, 3000)).'</description>';
        if( !empty((string)$obElement->adult) )
        {
            $str .= '<isSex>'.(string)$obElement->adult.'</isSex>';
        }
        $str .= '<manufacturerWarranty>'.( !empty((string)$obElement->{'manufacturer_warranty'}) ? (string)$obElement->{'manufacturer_warranty'} : 'true' ).'</manufacturerWarranty>';
        if( !empty((string)$obElement->{'downloadable'}) )
        {
            $str .= '<isSoftware>'.(string)$obElement->{'downloadable'}.'</isSoftware>';
        }

        if( !empty((string)$obElement->weight) )
        {
            $str .= '<weight>'.substr((string)$obElement->weight, 0, 9).'</weight>';
            $str .= '<weightUnitCode>KGM</weightUnitCode>';
        }

        if( !empty((string)$obElement->dimensions) )
        {
            $ardimensions = explode('/', (string)$obElement->dimensions);
            $ardimensions = array_map('trim', $ardimensions);

            $str .= '<length>'.substr($ardimensions[0], 0, 9).'</length>';
            $str .= '<lengthUnitCode>CMT</lengthUnitCode>';
            $str .= '<width>'.substr($ardimensions[1], 0, 9).'</width>';
            $str .= '<widthUnitCode>CMT</widthUnitCode>';
            $str .= '<height>'.substr($ardimensions[], 0, 9).'</height>';
            $str .= '<heightUnitCode>CMT</heightUnitCode>';
        }

        foreach( $obElement->param as $obParam )
        {
            $name = $obParam->attributes();
            $name = substr(htmlspecialchars(trim((string)$name['name'])), 0, 100);
            $code = substr(strtoupper(\Cutil::translit($name, "ru", array("replace_space" => "_", "replace_other" => "_"))), 0, 50);
            $str .= '<param name="'.$name.'" code="'.$code.'">'.substr(htmlspecialchars((string)$obParam), 0, 255).'</param>';
        }

        if( !empty((string)$obElement->delivery) )
        {
            $strOption = '';
            foreach( $obElement->{'delivery-options'}->option as $obOption )
            {
                $strDays = (string)$obOption->attributes()['days'];
                $strDays = explode('-', $strDays);
                if( sizeof($strDays) < 2 )
                {
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
                $strOption .= '></option>';
            }

            if( !empty($strOption) )
            {
                $str .= '<delivery available="true">';
                $str .= $strOption;
                $str .= '</delivery>';
            }
            else
            {
                $str .= '<delivery available="false"></delivery>';
            }
        }
        else
        {
            $str .= '<delivery available="false"></delivery>';
        }

        if( !empty((string)$obElement->pickup) )
        {

            $strOption = '';
            foreach( $obElement->{'pickup-options'}->option as $obOption )
            {
                $strDays = (string)$obOption->attributes()['days'];
                $strDays = explode('-', $strDays);
                if( sizeof($strDays) < 2 )
                {
                    $strDays[1] = $strDays[0];
                }

                $strOption .= '<option';
                $strOption .= ' price="'.substr((string)$obOption->attributes()['cost'], 0, 9).'"';
                $strOption .= ' currencyCode="RUB"';
                $strOption .= ' supplyFrom="'.substr($strDays[1], 0, 2).'"';
                $strOption .= ' supplyTo="'.substr($strDays[2], 0, 2).'"';
                $strOption .= ' orderBefore="'.substr((string)$obOption->attributes()['order-before'], 0, 2).'"';
                $strOption .= '></option>';
            }

            if( !empty($strOption) )
            {
                $str .= '<pickup available="true">';
                $str .= $strOption;
                $str .= '</pickup>';
            }
            else
            {
                $str .= '<pickup available="false"></pickup>';
            }
        }
        else
        {
            $str .= '<pickup available="false"></pickup>';
        }


        $str .= '</offer>';
        $this->addToWrite($str);
        return true;
    }
}