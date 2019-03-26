<?php

namespace Local\Core\Inner\Robofeed\Converter;


use Local\Core\Inner\Exception\FatalException;

class YML
{
    private $strFilePath;
    private $isYML = null;
    private $strWriteTo;

    public function __construct()
    {
        $this->strWriteTo = $_SERVER['DOCUMENT_ROOT'].'/tmp.xml';
        file_put_contents($this->strWriteTo, '');
    }

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

    public function execute()
    {
        if( is_null($this->strFilePath) )
        {
            throw new FatalException('Необходимо задать путь до файла');
        }

        $this->begin();
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
            throw new FatalException('Файл не является YML файлом, в теге "yml_catalog" не указан аттрибут "date" удалось найти тег "yml_catalog" в первых 300 символах файла.');
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
            $arAttr[$k] = (string)$v;
        }

        $str = '<offer id="'.$arAttr['id'].'" '.( !is_null($arAttr['group_id']) ? 'group_id="'.$arAttr['group_id'].'"' : '' ).'>';

        $str .= '<inStock>'.( !empty($arAttr['available']) ? $arAttr['available'] : 'true' ).'</inStock>';

        if( trim($arAttr['type']) == 'vendor.model' )
        {
            $str .= '<fullName>'.htmlspecialchars(substr((string)$obElement->model, 0, 255)).'</fullName>';
            $str .= '<simpleName>'.htmlspecialchars(substr((string)$obElement->model, 0, 255)).'</simpleName>';
        }
        else
        {
            $str .= '<fullName>'.htmlspecialchars(substr((string)$obElement->name, 0, 255)).'</fullName>';
            $str .= '<simpleName>'.htmlspecialchars(substr((string)$obElement->name, 0, 255)).'</simpleName>';
        }

        $str .= '<manufacturer>'.htmlspecialchars((string)$obElement->vendor).'</manufacturer>';
        $str .= '<manufacturerCode>'.htmlspecialchars((string)$obElement->vendorCode).'</manufacturerCode>';

        if( !empty( htmlspecialchars((string)$obElement->vendorCode) ) )
        {
            $str .= '<article>'.htmlspecialchars((string)$obElement->vendorCode).'</article>';
        }
        else
        {
            $str .= '<article>'.$arAttr['id'].'</article>';
        }

        $str .= '<url>'.htmlspecialchars((string)$obElement->url).'</url>';
        $str .= '<price>'.(string)$obElement->price.'</price>';
        $str .= '<oldPrice>'.(string)$obElement->oldprice.'</oldPrice>';
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
        $str .= '<categoryId>'.(string)$obElement->categoryId.'</categoryId>';

        foreach( $obElement->picture as $obPicture )
        {
            $str .= '<image>'.htmlspecialchars((string)$obPicture).'</image>';
        }

        $str .= '<salesNotes>'.htmlspecialchars((string)$obElement->{'sales_notes'}).'</salesNotes>';

        $str .= '<quantity>1</quantity><unitOfMeasure>PCS</unitOfMeasure>';
        $str .= '<minQuantity>'.( !empty((string)$obElement->{'min-quantity'}) ? (string)$obElement->{'min-quantity'} : 1 ).'</minQuantity>';

        $str .= '<description>'.htmlspecialchars(substr(strip_tags((string)$obElement->description), 0, 3000)).'</description>';
        $str .= '<isSex>'.(string)$obElement->adult.'</isSex>';
        $str .= '<manufacturerWarranty>'.( !empty( (string)$obElement->{'manufacturer_warranty'} ) ? (string)$obElement->{'manufacturer_warranty'} : 'true' ).'</manufacturerWarranty>';
        $str .= '<isSoftware>'.(string)$obElement->{'downloadable'}.'</isSoftware>';

        if( !empty((string)$obElement->weight) )
        {
            $str .= '<weight>'.(string)$obElement->weight.'</weight>';
            $str .= '<weightUnitCode>KGM</weightUnitCode>';
        }

        if( !empty((string)$obElement->dimensions) )
        {
            $ardimensions = explode('/', (string)$obElement->dimensions);
            $ardimensions = array_map('trim', $ardimensions);

            $str .= '<length>'.$ardimensions[0].'</length>';
            $str .= '<lengthUnitCode>CMT</lengthUnitCode>';
            $str .= '<width>'.$ardimensions[1].'</width>';
            $str .= '<widthUnitCode>CMT</widthUnitCode>';
            $str .= '<height>'.$ardimensions[2].'</height>';
            $str .= '<heightUnitCode>CMT</heightUnitCode>';
        }

        foreach( $obElement->param as $obParam )
        {
            $name = $obParam->attributes();
            $name = trim((string)$name['name']);
            $code = strtoupper(\Cutil::translit(trim($name), "ru", array("replace_space" => "_", "replace_other" => "_")));
            $str .= '<param name="'.htmlspecialchars($name).'" code="'.htmlspecialchars($code).'">'.htmlspecialchars((string)$obParam).'</param>';
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
                $strOption .= ' priceFrom="'.(string)$obOption->attributes()['cost'].'"';
                $strOption .= ' priceTo="'.(string)$obOption->attributes()['cost'].'"';
                $strOption .= ' currencyCode="RUB"';
                $strOption .= ' daysFrom="'.$strDays[1].'"';
                $strOption .= ' daysTo="'.$strDays[2].'"';
                $strOption .= ' orderBefore="'.(string)$obOption->attributes()['order-before'].'"';
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
                $strOption .= ' price="'.(string)$obOption->attributes()['cost'].'"';
                $strOption .= ' currencyCode="RUB"';
                $strOption .= ' supplyFrom="'.$strDays[1].'"';
                $strOption .= ' supplyTo="'.$strDays[2].'"';
                $strOption .= ' orderBefore="'.(string)$obOption->attributes()['order-before'].'"';
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