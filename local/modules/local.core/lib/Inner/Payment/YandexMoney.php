<?

namespace Local\Core\Inner\Payment;


use Local\Core\Inner\Route;

class YandexMoney implements PaymentInterface
{
    /** @inheritdoc */
    public static function getCode()
    {
        return 'yandex-money';
    }

    /** @inheritdoc */
    public static function getTitle()
    {
        return 'Оплата картой';
    }

    /** @inheritdoc */
    public function printPaymentForm()
    {
        if (
            \Bitrix\Main\Application::getInstance()
                ->getContext()
                ->getRequest()
                ->get('result') == 'success'
        ) {
            $this->printPaymentSuccess();
        } else {
            $arConf = \Bitrix\Main\Config\Configuration::getInstance()
                ->get('payment')['yandex-money'];
            $arUserLastBalanceUpId = \Local\Core\Model\Data\BalanceLogTable::getList([
                'filter' => [
                    'USER_ID' => $GLOBALS['USER']->GetId(),
                    '>OPERATION' => 0
                ],
                'order' => ['DATE_CREATE' => 'DESC'],
                'select' => ['ID'],
                'limit' => 1,
                'offset' => 0
            ])
                ->fetch();
            $strLabelCode = $arUserLastBalanceUpId['ID'].'|'.$GLOBALS['USER']->GetId();
            ?>
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Оплата картой</div>
                    <div class="card-subtitle">
                        При оплате картой комиссия составляет 3%.<br/>
                        Минимальная сумма пополнения - 100 российских рублей.
                    </div>

                    <form method="POST" action="https://money.yandex.ru/quickpay/confirm.xml" data-ya-payment-form>
                        <input type="hidden" name="receiver" value="<?=$arConf['receiver_id']?>">
                        <input type="hidden" name="quickpay-form" value="shop">

                        <input type="hidden" name="targets" value="Robofeed.ru - Пополнение баланса пользователя <?=$GLOBALS['USER']->GetEmail()?> (<?=$GLOBALS['USER']->GetId()?>)">
                        <input type="hidden" name="paymentType" value="AC">
                        <input type="hidden" name="sum" value="" />

                        <input type="hidden" name="formcomment" value="Robofeed.ru: Пополнение баланса">
                        <input type="hidden" name="short-dest" value="Robofeed.ru: Пополнение баланса">
                        <input type="hidden" name="label" value="<?=$strLabelCode?>">
                        <input type="hidden" name="comment" value="Пополнение баланса пользователя <?=$GLOBALS['USER']->GetEmail()?> (<?=$GLOBALS['USER']->GetId()?>)">
                        <input type="hidden" name="successURL" value="<?=$arConf['successURL']?>">


                        <input type="hidden" name="need-fio" value="false">
                        <input type="hidden" name="need-email" value="false">
                        <input type="hidden" name="need-phone" value="true">
                        <input type="hidden" name="need-address" value="false">

                        <div class="row">
                            <div class="form-group col-md-4 col-lg-3">
                                <label>Сумма пополнения: </label>
                                <input type="text" class="form-control" name="TOTAL_SUMM" onkeyup="PaymentYandexMoney.calculateInsert()" onchange="PaymentYandexMoney.calculateInsert()" onblur="PaymentYandexMoney.calculateInsert()" value="500" />
                            </div>
                            <div class="form-group col-md-4 col-lg-3">
                                <label>Итого к оплате: </label>
                                <p class="lead" data-will-be-insert readonly>
                                    500
                                </p>
                            </div>
                            <div class="form-group col-md-4 col-lg-3">
                                <label>&nbsp;</label><br/>
                                <button class="btn btn-secondary">Пополнить счет</button>
                            </div>
                        </div>
                        <div class="clearboth"></div>
                    </form>

                </div>

            </div>

            <script type="text/javascript">
                class PaymentYandexMoney
                {
                    static calculateInsert() {
                        var inputEnter = document.querySelector('[data-ya-payment-form] [name="TOTAL_SUMM"]'),
                            inputView = document.querySelector('[data-ya-payment-form] [data-will-be-insert]'),
                            inputReal = document.querySelector('[data-ya-payment-form] [name="sum"]'),
                            summ = inputEnter.value.replace(/[^\d]/g, ''),
                            realSumm = 0;

                        if (summ < 0)
                            summ = 0;
                        inputEnter.value = summ;
                        realSumm = Math.ceil(summ * 1.03);
                        if( realSumm < 100 || isNaN(realSumm) )
                        {
                            realSumm = 103;
                        }
                        inputView.innerText = ( realSumm.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1 ") );
                        inputReal.value = realSumm;
                    }
                }

                document.addEventListener('DOMContentLoaded', function () {
                    PaymentYandexMoney.calculateInsert();
                })
            </script>
            <?
        }
    }

    /** @inheritdoc */
    public function printPaymentSuccess()
    {
        ?>
        <p>
            Оплата прошла успешно. После зачисления денежнех средств мы пополним Ваш баланс.
        </p>
        <a href="<?=Route::getRouteTo('company', 'list')?>" class="btn btn-warning">Вернуться к компаниям</a>
        <?
    }

    /**
     * Метод проверяет запрос от яндекс денег, подтверждающий платеж
     *
     * @throws \Bitrix\Main\SystemException
     */
    public static function checkPaymentRequest()
    {
        $obRequest = \Bitrix\Main\Application::getInstance()
            ->getContext()
            ->getRequest();
        if (
        !empty($obRequest->getPostList()
            ->toArray())
        ) {

            $arAttemptLog = [
                'QUERY_DATA' => $obRequest->getPostList()
                    ->toArray(),
                'HANDLER' => self::getCode()
            ];

            try {
                $r = 'notification_type&operation_id&amount&currency&datetime&sender&codepro&notification_secret&label';
                $r = explode('&', $r);
                foreach ($r as &$i) {
                    if ($i == 'notification_secret') {
                        $i = \Bitrix\Main\Config\Configuration::getInstance()
                            ->get('payment')['yandex-money']['secret_key'];
                    } else {
                        $i = $obRequest->getPost($i);
                    }
                }
                unset($i);
                $r = implode('&', $r);
                $r = hash('sha1', $r);

                if ($r == $obRequest->getPost('sha1_hash')) {

                    list($intLastPlusOperationId, $intUserId) = explode('|', $obRequest->getPost('label'));

                    $arAttemptLog['USER_ID'] = $intUserId;

                    $arUserLastBalanceUpId = \Local\Core\Model\Data\BalanceLogTable::getList([
                        'filter' => [
                            'USER_ID' => $intUserId,
                            '>OPERATION' => 0
                        ],
                        'order' => ['DATE_CREATE' => 'DESC'],
                        'select' => ['ID'],
                        'limit' => 1,
                        'offset' => 0
                    ])
                        ->fetch();

                    $arAttemptLog['ADDITIONAL_DATA'] = [
                        'Заявленный ID последнего лога на пополнение баланса' => $intLastPlusOperationId,
                        'ID последнего лога на пополнение баланса на текущий момент' => $arUserLastBalanceUpId['ID'],
                        'ID юзвера' => $intUserId
                    ];

                    if ($arUserLastBalanceUpId['ID'] == $intLastPlusOperationId) {

                        $arAttemptLog['QUERY_CHECK_RESULT'] = 'SU';

                        $obRes = \Local\Core\Inner\Balance\Base::payToAccount($obRequest->getPost('amount'), $intUserId, 'Пополнение счета через оплату картой');

                        if ($obRes->isSuccess()) {
                            $arAttemptLog['TRY_TOP_UP_BALANCE_RESULT'] = 'SU';
                        } else {
                            $arAttemptLog['TRY_TOP_UP_BALANCE_RESULT'] = 'ER';
                            $arAttemptLog['TRY_TOP_UP_BALANCE_ERROR_TEXT'] = implode('<br/>', $obRes->getErrorMessages());
                        }
                    } else {
                        $arAttemptLog['QUERY_CHECK_RESULT'] = 'ER';
                        $arAttemptLog['QUERY_CHECK_ERROR_TEXT'] = 'Завленный ID последнего пополнения и фактический не совпадают';
                    }
                } else {
                    $arAttemptLog['QUERY_CHECK_RESULT'] = 'ER';
                    $arAttemptLog['QUERY_CHECK_ERROR_TEXT'] = 'Полученный хэш от значений и заявленый не совпадают';
                }
            } catch (\Throwable $e) {
                $arAttemptLog['QUERY_CHECK_ERROR_TEXT'] = $e->getMessage();
            }
            finally {
                if (!empty($arAttemptLog['QUERY_DATA'])) {
                    $arAttemptLog['QUERY_DATA'] = json_encode($arAttemptLog['QUERY_DATA']);
                }

                if (!empty($arAttemptLog['ADDITIONAL_DATA'])) {
                    $arAttemptLog['ADDITIONAL_DATA'] = json_encode($arAttemptLog['ADDITIONAL_DATA']);
                }
                \Local\Core\Model\Data\AttemptsTopUpBalanceLogTable::add($arAttemptLog);
            }
        }
    }

    /** @inheritdoc */
    public static function getAdditionalDataInAdmin($strAdditionalData)
    {
        return '<pre>'.print_r((array)json_decode($strAdditionalData), true).'</pre>';
    }
}