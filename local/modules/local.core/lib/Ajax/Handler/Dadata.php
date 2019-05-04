<?php

namespace Local\Core\Ajax\Handler;


class Dadata
{
    public static function searchCompanyByInn(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $arResult = [];

        $obCache = \Bitrix\Main\Application::getInstance()->getCache();

        if(
            $obCache->startDataCache(
                60*60*24,
                __METHOD__.__LINE__.'#INN'.$args['inn'],
                \Local\Core\Inner\Cache::getCachePath(['Inner', 'Client', 'Dadata'], ['CompanyByInn', $args['inn']])
            )
        )
        {
            $query = new \Local\Core\Inner\Client\Dadata\Query;
            $query->set('query', $args['inn']);
            $query->set('count', 1);
            $legalPartyClient = new \Local\Core\Inner\Client\Dadata\LegalPartyClient();
            $res = $legalPartyClient->suggest($query);
            $arRes = $res->getData();
            if( !empty( $arRes['suggestions'][0]['data'] ) )
            {
                $strAddress = [];
                $strAddress[] = $arRes['suggestions'][0]['data']['address']['data']['street_with_type'];

                $strAddress[] = (
                    !empty( $arRes['suggestions'][0]['data']['address']['data']['house_type'] )
                    && !empty( $arRes['suggestions'][0]['data']['address']['data']['house'] )
                ) ? $arRes['suggestions'][0]['data']['address']['data']['house_type'].' '.$arRes['suggestions'][0]['data']['address']['data']['house'] : '';

                $strAddress[] = (
                    !empty( $arRes['suggestions'][0]['data']['address']['data']['block_type'] )
                    && !empty( $arRes['suggestions'][0]['data']['address']['data']['block'] )
                ) ? $arRes['suggestions'][0]['data']['address']['data']['block_type'].' '.$arRes['suggestions'][0]['data']['address']['data']['block'] : '';

                $strAddress = array_diff($strAddress, [''], [null]);
                $strAddress = implode(', ', $strAddress);

                $strOffice = (
                    !empty( $arRes['suggestions'][0]['data']['address']['data']['flat_type'] )
                    && !empty( $arRes['suggestions'][0]['data']['address']['data']['flat'] )
                ) ? $arRes['suggestions'][0]['data']['address']['data']['flat_type'].' '.$arRes['suggestions'][0]['data']['address']['data']['flat'] : '';

                $arResult = [
                    'result' => 'SUCCESS',
                    'company' => [
                        'COMPANY_INN' => $arRes['suggestions'][0]['data']['inn'],
                        'COMPANY_NAME_SHORT' => $arRes['suggestions'][0]['data']['name']['short_with_opf'],
                        'COMPANY_NAME_FULL' => $arRes['suggestions'][0]['data']['name']['full_with_opf'],
                        'COMPANY_OGRN' => $arRes['suggestions'][0]['data']['ogrn'],
                        'COMPANY_KPP' => $arRes['suggestions'][0]['data']['kpp'],
                        'COMPANY_OKPO' => $arRes['suggestions'][0]['data']['okpo'],
                        'COMPANY_OKTMO' => $arRes['suggestions'][0]['data']['address']['data']['oktmo'],
                        'COMPANY_DIRECTOR' => $arRes['suggestions'][0]['data']['management']['name'],

                        'COMPANY_ADDRESS_COUNTRY' => $arRes['suggestions'][0]['data']['address']['data']['country'],
                        'COMPANY_ADDRESS_REGION' => $arRes['suggestions'][0]['data']['address']['data']['region_with_type'],
                        'COMPANY_ADDRESS_AREA' => $arRes['suggestions'][0]['data']['address']['data']['area_with_type'],
                        'COMPANY_ADDRESS_CITY' => $arRes['suggestions'][0]['data']['address']['data']['city_with_type'],
                        'COMPANY_ADDRESS_ADDRESS' => $strAddress,
                        'COMPANY_ADDRESS_OFFICE' => $strOffice,
                        'COMPANY_ADDRESS_ZIP' => $arRes['suggestions'][0]['data']['address']['data']['postal_code'],
                    ]
                ];

                $arResult['company'] = array_diff($arResult['company'], [''], [null]);

                $obCache->endDataCache($arResult);
            }
            else
            {
                $obCache->abortDataCache();
                $arResult['result'] = 'NOTFOUND';
            }
        }
        else
        {
            $arResult = $obCache->getVars();
        }

        $response->setContentJson($arResult, 200);
    }
}