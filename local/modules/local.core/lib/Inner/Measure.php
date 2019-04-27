<?php
namespace Local\Core\Inner;


/**
 * Класс по работе с единицами измерений
 *
 * @package Local\Core\Inner
 */
class Measure
{
    /**
     * Конвертирование из единици в единицу
     *
     * @param $val
     * @param $fromCode
     * @param $toCode
     * @param int $intRound Округление, по умолчанию 0
     *
     * @return string
     */
    public static function convert($val, $fromCode, $toCode, $intRound = 0)
    {
        $strReturn = null;

        $val = str_replace(',', '.', $val);

        if( $fromCode == $toCode )
        {
            $strReturn = $val;
        }
        else
        {
            switch ($fromCode){
                // Единицы длины
                case 'MMT':

                    switch ($toCode)
                    {
                        case 'CMT':
                            $strReturn = $val/10;
                            break;
                        case 'DMT':
                            $strReturn = $val/100;
                            break;
                        case 'MTR':
                            $strReturn = $val/1000;
                            break;
                        case 'KMT':
                            $strReturn = $val/1e+6;
                            break;
                    }

                    break;
                case 'CMT':

                    switch ($toCode)
                    {
                        case 'MMT':
                            $strReturn = $val*10;
                            break;
                        case 'DMT':
                            $strReturn = $val/10;
                            break;
                        case 'MTR':
                            $strReturn = $val/100;
                            break;
                        case 'KMT':
                            $strReturn = $val/100000;
                            break;
                    }

                    break;
                case 'DMT':

                    switch ($toCode)
                    {
                        case 'MMT':
                            $strReturn = $val*100;
                            break;
                        case 'CMT':
                            $strReturn = $val*10;
                            break;
                        case 'MTR':
                            $strReturn = $val/10;
                            break;
                        case 'KMT':
                            $strReturn = $val/10000;
                            break;
                    }

                    break;
                case 'MTR':

                    switch ($toCode)
                    {
                        case 'MMT':
                            $strReturn = $val*1000;
                            break;
                        case 'CMT':
                            $strReturn = $val*100;
                            break;
                        case 'DMT':
                            $strReturn = $val*10;
                            break;
                        case 'KMT':
                            $strReturn = $val/1000;
                            break;
                    }

                    break;
                case 'KMT':

                    switch ($toCode)
                    {
                        case 'MMT':
                            $strReturn = $val*1e+6;
                            break;
                        case 'CMT':
                            $strReturn = $val*100000;
                            break;
                        case 'DMT':
                            $strReturn = $val*10000;
                            break;
                        case 'MTR':
                            $strReturn = $val*1000;
                            break;
                    }

                    break;

                // Единицы площади
                case 'MMK':

                    switch ($toCode)
                    {
                        case 'CMK':
                            $strReturn = $val/100;
                            break;
                        case 'MTK':
                            $strReturn = $val/1e+6;
                            break;
                        case 'HAR':
                            $strReturn = $val/1e+10;
                            break;
                        case 'KMK':
                            $strReturn = $val/1e+12;
                            break;
                    }

                    break;
                case 'CMK':

                    switch ($toCode)
                    {
                        case 'MMK':
                            $strReturn = $val*100;
                            break;
                        case 'MTK':
                            $strReturn = $val/10000;
                            break;
                        case 'HAR':
                            $strReturn = $val/1e+8;
                            break;
                        case 'KMK':
                            $strReturn = $val/1e+10;
                            break;
                    }

                    break;
                case 'MTK':

                    switch ($toCode)
                    {
                        case 'MMK':
                            $strReturn = $val*1e+6;
                            break;
                        case 'CMK':
                            $strReturn = $val*10000;
                            break;
                        case 'HAR':
                            $strReturn = $val/10000;
                            break;
                        case 'KMK':
                            $strReturn = $val/1e+6;
                            break;
                    }

                    break;
                case 'HAR':

                    switch ($toCode)
                    {
                        case 'MMK':
                            $strReturn = $val*1e+10;
                            break;
                        case 'CMK':
                            $strReturn = $val*1e+8;
                            break;
                        case 'MTK':
                            $strReturn = $val*10000;
                            break;
                        case 'KMK':
                            $strReturn = $val/100;
                            break;
                    }

                    break;
                case 'KMK':

                    switch ($toCode)
                    {
                        case 'MMK':
                            $strReturn = $val*1e+12;
                            break;
                        case 'CMK':
                            $strReturn = $val*1e+10;
                            break;
                        case 'MTK':
                            $strReturn = $val*1e+6;
                            break;
                        case 'HAR':
                            $strReturn = $val*100;
                            break;
                    }

                    break;

                // Единицы массы
                case 'MGM':

                    switch ($toCode)
                    {
                        case 'GRM':
                            $strReturn = $val/1000;
                            break;
                        case 'KGM':
                            $strReturn = $val/1e+6;
                            break;
                        case 'TNE':
                            $strReturn = $val/1e+9;
                            break;
                    }

                    break;
                case 'GRM':

                    switch ($toCode)
                    {
                        case 'MGM':
                            $strReturn = $val*1000;
                            break;
                        case 'KGM':
                            $strReturn = $val/1000;
                            break;
                        case 'TNE':
                            $strReturn = $val/1e+6;
                            break;
                    }

                    break;
                case 'KGM':

                    switch ($toCode)
                    {
                        case 'MGM':
                            $strReturn = $val*1e+6;
                            break;
                        case 'GRM':
                            $strReturn = $val*1000;
                            break;
                        case 'TNE':
                            $strReturn = $val/1000;
                            break;
                    }

                    break;
                case 'TNE':

                    switch ($toCode)
                    {
                        case 'MGM':
                            $strReturn = $val*1e+9;
                            break;
                        case 'GRM':
                            $strReturn = $val*1e+6;
                            break;
                        case 'KGM':
                            $strReturn = $val*1000;
                            break;
                    }

                    break;

                // Единицы объема
                case 'MMQ':

                    switch ($toCode)
                    {
                        case 'CMQ':
                            $strReturn = $val/1000;
                            break;
                        case 'MLT':
                            $strReturn = $val/1000;
                            break;
                        case 'LTR':
                            $strReturn = $val/1e+6;
                            break;
                        case 'MTQ':
                            $strReturn = $val/1e+9;
                            break;
                    }

                    break;
                case 'CMQ':

                    switch ($toCode)
                    {
                        case 'MMQ':
                            $strReturn = $val*1000;
                            break;
                        case 'MLT':
                            $strReturn = 1;
                            break;
                        case 'LTR':
                            $strReturn = $val/1000;
                            break;
                        case 'MTQ':
                            $strReturn = $val/1e+6;
                            break;
                    }

                    break;
                case 'MLT':

                    switch ($toCode)
                    {
                        case 'MMQ':
                            $strReturn = $val*1000;
                            break;
                        case 'CMQ':
                            $strReturn = 1;
                            break;
                        case 'LTR':
                            $strReturn = $val/1000;
                            break;
                        case 'MTQ':
                            $strReturn = $val/1e+6;
                            break;
                    }

                    break;
                case 'LTR':

                    switch ($toCode)
                    {
                        case 'MMQ':
                            $strReturn = $val*1e+6;
                            break;
                        case 'CMQ':
                            $strReturn = $val*1000;
                            break;
                        case 'MLT':
                            $strReturn = $val*1000;
                            break;
                        case 'MTQ':
                            $strReturn = $val/1000;
                            break;
                    }

                    break;
                case 'MTQ':

                    switch ($toCode)
                    {
                        case 'MMQ':
                            $strReturn = $val*1e+9;
                            break;
                        case 'CMQ':
                            $strReturn = $val*1e+6;
                            break;
                        case 'MLT':
                            $strReturn = $val*1e+6;
                            break;
                        case 'LTR':
                            $strReturn = $val*1000;
                            break;
                    }

                    break;
            }
        }

        if( !empty( $strReturn ) )
        {
            $strReturn = round($strReturn, $intRound);
        }

        return $strReturn;
    }
}