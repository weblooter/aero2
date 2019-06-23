<?

namespace Local\Core\Inner\Payment;


use Local\Core\Inner\Route;

class Bill implements PaymentInterface
{
    /** @inheritdoc */
    public static function getCode()
    {
        return 'bill';
    }

    /** @inheritdoc */
    public static function getTitle()
    {
        return 'Оплата по счету';
    }

    /** @inheritdoc */
    public function printPaymentForm()
    {
        $obRequest = \Bitrix\Main\Application::getInstance()
            ->getContext()
            ->getRequest();
        ?>

        <div class="card">
            <div class="card-body">
                <div class="card-title">Оплата по счету</div>
                <div class="card-subtitle">
                    Минимальная сумма пополнения - 100 российских рублей.
                </div>

                <form action="<?=\Bitrix\Main\Application::getInstance()
                    ->getContext()
                    ->getRequest()
                    ->getRequestedPageDirectory()?>/?handler=<?=self::getCode()?>" method="post">
                    <?=bitrix_sessid_post()?>

                    <?
                    $arRequest = $obRequest->getPost('UR');

                    if ($obRequest->getPost('make_bill') == 'Y') {
                        $obPdf = (new \Local\Core\Inner\Payment\Bill())->makeBillPdf([
                            [
                                'NAME' => 'Пополнение счета пользователя Robofeed.ru',
                                'COUNT' => '1',
                                'UNIT' => 'шт.',
                                'PRICE' => $arRequest['TOP_UP_SUMM'] // TODO проверить на число более 0
                            ]
                        ], $arRequest['ORG_NAME'].', ИНН '.$arRequest['INN'].', '.$arRequest['ZIP'].', '.$arRequest['ADDRESS'].', '.$arRequest['LAST_NAME'].' '.$arRequest['NAME'].' '
                           .$arRequest['SECOND_NAME']);
                        \Local\Core\Model\Data\AttemptsTopUpBalanceLogTable::add([
                            'USER_ID' => $GLOBALS['USER']->GetId(),
                            'HANDLER' => self::getCode(),
                            'QUERY_DATA' => json_encode($obRequest->getPostList()
                                ->toArray()),
                            'ADDITIONAL_DATA' => $obPdf->getBase64(),
                            'QUERY_CHECK_RESULT' => 'SU',
                            'TRY_TOP_UP_BALANCE_RESULT' => 'SU'
                        ]);
                        $obPdf->stream();
                    }
                    ?>
                    <input type="hidden" name="make_bill" value="Y" />
                    <h4 class="card-title">Ответственное лицо</h4>
                    <div class="row">
                        <div class="form-group col-4">
                            <label>Фамилия *</label>
                            <input type="text" class="form-control" name="UR[LAST_NAME]" required value="<?=$arRequest['LAST_NAME']?>" />
                        </div>
                        <div class="form-group col-4">
                            <label>Имя *</label>
                            <input type="text" class="form-control" name="UR[NAME]" required value="<?=$arRequest['NAME']?>" />
                        </div>
                        <div class="form-group col-4">
                            <label>Отчество</label>
                            <input type="text" class="form-control" name="UR[SECOND_NAME]" value="<?=$arRequest['SECOND_NAME']?>" />
                        </div>
                    </div>
                    <h4 class="card-title">Данные организации</h4>
                    <div class="row">
                        <div class="form-group col-4">
                            <label>Название организации *</label>
                            <input type="text" class="form-control" name="UR[ORG_NAME]" required value="<?=$arRequest['ORG_NAME']?>" placeholder="ООО Рога и копыт" />
                        </div>
                        <div class="form-group col-4">
                            <label>ИНН *</label>
                            <input type="text" class="form-control" name="UR[INN]" required value="<?=$arRequest['INN']?>" />
                        </div>
                    </div>
                    <h4 class="card-title">Фактический адрес организации</h4>
                    <div class="row">
                        <div class="form-group col-4">
                            <label>Почтовый индекс *</label>
                            <input type="text" class="form-control" name="UR[ZIP]" required value="<?=$arRequest['ZIP']?>" placeholder="100100" />
                        </div>
                        <div class="form-group col-8">
                            <label>Полный адрес *</label>
                            <input type="text" class="form-control" name="UR[ADDRESS]" required value="<?=$arRequest['ADDRESS']?>" placeholder="Россия, г. Москва, ул. Пушкина, д. 1, офис 1" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-4">
                            <label>Сумма пополнения в рублях *</label>
                            <input type="text" class="form-control" name="UR[TOP_UP_SUMM]" required value="<?=$arRequest['TOP_UP_SUMM'] ?? 500?>" />
                        </div>
                        <div class="col-4">
                            <label>&nbsp;</label><br />
                            <button class="btn btn-secondary">Выставить счет</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
        <?
    }

    /**
     * @var \Dompdf\Dompdf $obPdf Хранилище pdf
     */
    private $obPdf;

    /** @var string $obPdfOutputed Хранилище контента файла pdf */
    private $obPdfOutputed;

    /**
     * @see \Local\Core\Inner\Payment\Bill::makeBillPdf()
     * @var string $accountNumber Номер счета. Генерируеся после \Local\Core\Inner\Payment\Bill::makeBillPdf()
     *
     */
    private $accountNumber;

    /**
     * Делает счет в формате PDF.<br/>
     * Массив с перечнем товаров должен иметь такую структуру:<br/>
     * <code>
     * $arProductList = [
     *  [
     *   'NAME' => 'Название товара',
     *   'COUNT' => 'Количество',
     *   'UNIT' => 'Единица измерения товара',
     *   'PRICE' => 'Стоимость за штуку в рублях'
     *  ]
     * ];
     * </code>
     *
     * @param array  $arProductList Массив с перечнем товаров
     * @param string $strPurchaser  Строка с данными о покупателе
     * @param int    $intUserId     ID пользователя, которому выславляется счет
     *
     * @return $this
     */
    public function makeBillPdf(array $arProductList, $strPurchaser, $intUserId = 0)
    {
        if ($intUserId < 1) {
            $intUserId = $GLOBALS['USER']->GetId();
        }
        $this->accountNumber = 'RF_'.date('YmdHi').'_'.$intUserId;
        $html = static::makeBillHtml($arProductList, $strPurchaser, $this->accountNumber);

        $this->obPdf = new \Dompdf\Dompdf();
        $this->obPdf->loadHtml($html, 'UTF-8');
        $this->obPdf->setPaper('A4', 'portrait');
        $this->obPdf->render();

        return $this;
    }

    /**
     * Делает html счета.<br/>
     * Первый параметр описан в \Local\Core\Inner\Payment\Bill::makeBillPdf
     *
     * @param array  $arProductList    Массив с перечнем товаров
     * @param string $strPurchaser     Строка с данными о покупателе
     * @param int    $strAccountNumber Номер счета
     *
     * @return string
     * @see \Local\Core\Inner\Payment\Bill::makeBillPdf()
     */
    private static function makeBillHtml(array $arProductList, $strPurchaser, $strAccountNumber)
    {
        $arConfig = \Bitrix\Main\Config\Configuration::getInstance()
            ->get('payment')['bill'];

        $html = '
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    
    <style type="text/css">
        * { 
            font-family: arial;
            font-size: 14px;
            line-height: 14px;
        }
        table {
            margin: 0 0 15px 0;
            width: 100%;
            border-collapse: collapse; 
            border-spacing: 0;
        }        
        table td {
            padding: 5px;
        }    
        table th {
            padding: 5px;
            font-weight: bold;
        }

        .header {
            margin: 0 0 0 0;
            padding: 0 0 15px 0;
            font-size: 12px;
            line-height: 12px;
            text-align: center;
        }
        
        /* Реквизиты банка */
        .details td {
            padding: 3px 2px;
            border: 1px solid #000000;
            font-size: 12px;
            line-height: 12px;
            vertical-align: top;
        }

        h1 {
            margin: 0 0 10px 0;
            padding: 10px 0 10px 0;
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 20px;
        }

        /* Поставщик/Покупатель */
        .contract th {
            padding: 3px 0;
            vertical-align: top;
            text-align: left;
            font-size: 13px;
            line-height: 15px;
        }    
        .contract td {
            padding: 3px 0;
        }        

        /* Наименование товара, работ, услуг */
        .list thead, .list tbody  {
            border: 2px solid #000;
        }
        .list thead th {
            padding: 4px 0;
            border: 1px solid #000;
            vertical-align: middle;
            text-align: center;
        }    
        .list tbody td {
            padding: 0 2px;
            border: 1px solid #000;
            vertical-align: middle;
            font-size: 11px;
            line-height: 13px;
        }    
        .list tfoot th {
            padding: 3px 2px;
            border: none;
            text-align: right;
        }    

        /* Сумма */
        .total {
            margin: 0 0 20px 0;
            padding: 0 0 10px 0;
            border-bottom: 2px solid #000;
        }    
        .total p {
            margin: 0;
            padding: 0;
        }
        
        /* Руководитель, бухгалтер */
        .sign {
            position: relative;
        }
        .sign table {
            width: 60%;
        }
        .sign th {
            padding: 40px 0 0 0;
            text-align: left;
        }
        .sign td {
            padding: 40px 0 0 0;
            border-bottom: 1px solid #000;
            text-align: left;
            font-size: 12px;
        }
        
        .sign-1 {
            position: absolute;
            left: 60%;
            margin-left: -200px;
            top: 29px;
            z-index: -1;
        }    
        .sign-2 {
            position: absolute;
            left: 60%;
            margin-left: -200px;
            top: 104px;
            z-index: -1;
        }    
        .printing {
            position: absolute;
            left: 0%;
            top: 12px;
            z-index: -1;
        }
    </style>
</head>
<body>
    <p class="header">
        Внимание! Оплата данного счета означает полное и безоговорочное согласие с условиями договора-оферты, изложенными по адресу https://robofeed.ru/dogovor-oferta/
    </p>

    <table class="details">
        <tbody>
            <tr>
                <td colspan="2" style="border-bottom: none;">'.$arConfig['bank'].'</td>
                <td>БИК</td>
                <td style="border-bottom: none;">'.$arConfig['bik'].'</td>
            </tr>
            <tr>
                <td colspan="2" style="border-top: none; font-size: 10px;">Банк получателя</td>
                <td>Сч. №</td>
                <td style="border-top: none;">'.$arConfig['kr'].'</td>
            </tr>
            <tr>
                <td width="25%">ИНН '.$arConfig['inn'].'</td>
                <td width="30%">КПП '.$arConfig['kpp'].'</td>
                <td width="10%" rowspan="3">Сч. №</td>
                <td width="35%" rowspan="3">'.$arConfig['rs'].'</td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom: none;">'.$arConfig['recipient'].'</td>
            </tr>
            <tr>
                <td colspan="2" style="border-top: none; font-size: 10px;">Получатель</td>
            </tr>
        </tbody>
    </table>

    <h1>Счет на оплату № '.$strAccountNumber.' от '.\FormatDate('d F Y').' г.</h1>

    <table class="contract">
        <tbody>
            <tr>
                <td>Покупатель:</td>
                <th>
                    '.$strPurchaser.'
                </th>
            </tr>
        </tbody>
    </table>

    <table class="list">
        <thead>
            <tr>
                <th width="5%">№</th>
                <th width="54%">Наименование товара, работ, услуг</th>
                <th width="8%">Коли-<br>чество</th>
                <th width="5%">Ед.<br>изм.</th>
                <th width="14%">Цена</th>
                <th width="14%">Сумма</th>
            </tr>
        </thead>
        <tbody>';

        $total = 0;
        foreach ($arProductList as $i => $row) {
            $total += $row['PRICE'] * $row['COUNT'];

            $html .= '
            <tr>
                <td align="center">'.(++$i).'</td>
                <td align="left">'.$row['NAME'].'</td>
                <td align="right">'.$row['COUNT'].'</td>
                <td align="left">'.$row['UNIT'].'</td>
                <td align="right">'.self::format_price($row['PRICE']).'</td>
                <td align="right">'.self::format_price($row['PRICE'] * $row['COUNT']).'</td>
            </tr>';
        }

        $html .= '
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5">Итого:</th>
                <th>'.self::format_price($total).' руб.</th>
            </tr>
            <tr>
                <th colspan="5">В том числе НДС:</th>
                <th>Без НДС</th>
            </tr>
            <tr>
                <th colspan="5">Всего к оплате:</th>
                <th>'.self::format_price($total).' руб.</th>
            </tr>
            
        </tfoot>
    </table>
    
    <div class="total">
        <p>Всего наименований '.sizeof($arProductList).', на сумму '.self::format_price($total).' руб.</p>
        <p><strong>'.self::str_price($total).'</strong></p>
    </div>
    
    <p>В комментарии к платежу не забудьте указать номер счета.</p>
    
    <div class="sign">
        <img class="sign-1" src="'.$arConfig['sing_1_link'].'">
        <img class="sign-2" src="'.$arConfig['sing_2_link'].'">
        <img class="printing" src="'.$arConfig['printing_link'].'">

        <table>
            <tbody>
                <tr>
                    <th width="30%">Генеральный директор</th>
                    <td width="70%">Черешнев Е.С.</td>
                </tr>
                <tr>
                    <th>Главный бухгалтер</th>
                    <td>Черешнев Е.С.</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Системный метод static::makeBillHtml()
     *
     * @param $value
     *
     * @return string
     */
    private static function format_price($value)
    {
        return number_format($value, 2, ',', ' ');
    }

    /**
     * Системный метод static::makeBillHtml()
     *
     * @param $value
     *
     * @return string
     */
    private static function str_price($value)
    {
        $value = explode('.', number_format($value, 2, '.', ''));

        $f = new \NumberFormatter('ru', \NumberFormatter::SPELLOUT);
        $str = $f->format($value[0]);

        // Первую букву в верхний регистр.
        $str = mb_strtoupper(mb_substr($str, 0, 1)).mb_substr($str, 1, mb_strlen($str));

        // Склонение слова "рубль".
        $num = $value[0] % 100;
        if ($num > 19) {
            $num = $num % 10;
        }
        switch ($num) {
            case 1:
                $rub = 'рубль';
                break;
            case 2:
            case 3:
            case 4:
                $rub = 'рубля';
                break;
            default:
                $rub = 'рублей';
        }

        return $str.' '.$rub.' '.$value[1].' коп.';
    }

    /**
     * Сохраняет файл pdf по указанному пути
     *
     * @param string $strPath Абсоютный пусть сохранения файла. Предпочтительно генерировать методом \Local\Core\Inner\BxModified\CFile::makeLocalCorePath()
     *
     * @throws \Exception
     * @see \Local\Core\Inner\BxModified\CFile::makeLocalCorePath()
     */
    public function saveTo($strPath)
    {
        if (is_null($this->obPdf)) {
            throw new \Exception('PDF надо сгенерировать');
        }
        if (is_null($this->obPdfOutputed)) {
            $this->obPdfOutputed = $this->obPdf->output();
        }
        file_put_contents($strPath, $this->obPdfOutputed);
    }

    /**
     * Отдает сгенерированный pdf на загрузку. Прекращает работу php скрипта.<br/>
     * Есть название не указано - отдает с названием номера счета
     *
     * @param string $strFileName
     *
     * @throws \Exception
     */
    public function stream($strFileName = '')
    {
        if (is_null($this->obPdf)) {
            throw new \Exception('PDF надо сгенерировать');
        }

        if (is_null($this->obPdfOutputed)) {
            $this->obPdfOutputed = $this->obPdf->output();
        }

        if (empty($strFileName)) {
            $strFileName = 'Bill #'.$this->accountNumber;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$strFileName.'.pdf');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        $GLOBALS['APPLICATION']->RestartBuffer();
        echo $this->obPdfOutputed;
        die();
    }

    /**
     * Получить base64 pdf
     *
     * @return string
     * @throws \Exception
     */
    public function getBase64()
    {
        if (is_null($this->obPdf)) {
            throw new \Exception('PDF надо сгенерировать');
        }
        if (is_null($this->obPdfOutputed)) {
            $this->obPdfOutputed = $this->obPdf->output();
        }
        return base64_encode($this->obPdfOutputed);
    }

    /**
     * Выводи изобрадения pdf по base64
     *
     * @param string $base64 , полученный через \Local\Core\Inner\Payment\Bill::getBase64()
     * @param string $width  Ширина блока
     * @param string $height Высота блока
     *
     * @return string
     * @see \Local\Core\Inner\Payment\Bill::getBase64()
     */
    public static function printByBase64($base64, $width = '100%', $height = 600)
    {
        echo '<embed src="data:application/pdf;base64,'.$base64.'" width="'.$width.'" height="'.$height.'" />';
    }

    /** @inheritdoc */
    public static function getAdditionalDataInAdmin($strAdditionalData)
    {
        $cont = '';
        ob_start(function ()
            {
                return '';
            });
        static::printByBase64($strAdditionalData);
        $cont = ob_get_contents();
        ob_end_flush();
        return $cont;
    }

}