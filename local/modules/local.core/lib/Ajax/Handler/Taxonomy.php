<?php

namespace Local\Core\Ajax\Handler;

class Taxonomy
{
    public static function getTaxonomyResult(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $arResult = [];

        $arTaxonomyOptions = \Local\Core\Inner\TaxonomyData\Base::getData($args['action']);
        if( !is_null($arTaxonomyOptions) )
        {
            $arTaxonomyOptions = \Local\Core\Inner\TaxonomyData\Base::convertData($arTaxonomyOptions);

            $strQuery = mb_strtoupper(trim($request->getPost('q')));
            $strQuery = str_replace('Ё', 'Е', $strQuery);

            foreach ($arTaxonomyOptions as $value => $text)
            {
                if( !empty( $strQuery ) )
                {
                    if( stripos( $text,  $strQuery) !== false )
                    {
                        $arResult[] = ['id' => htmlspecialchars($value), 'text' => htmlspecialchars($text)];
                    }
                }
                else
                {
                    $arResult[] = ['id' => htmlspecialchars($value), 'text' => htmlspecialchars($text)];
                }
            }
        }

        $response->setContentJson([
            'results' => $arResult
        ]);
    }
}