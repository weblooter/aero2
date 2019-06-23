<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$strRecaptchaSiteKey = \Bitrix\Main\Config\Configuration::getInstance()->get('recaptcha')['site_key'];
\Bitrix\Main\Page\Asset::getInstance()->addJs('https://www.google.com/recaptcha/api.js?render='.$strRecaptchaSiteKey );
?>

<div class="login">

    <!-- Login -->
    <div class="login__block active">
        <div class="login__block__body">
            <form class="mb-3 text-left" action="" method="post" id="registrationform">
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response" />

                <div class="form-group">
                    <label>E-mail</label>
                    <input type="text" class="form-control" data-reg-form-email />
                </div>

                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" class="form-control" data-reg-form-password />
                </div>

                <div class="form-group">
                    <label>Повторите пароль</label>
                    <input type="password" class="form-control" data-reg-form-repeat-password />
                </div>

                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" class="form-control" data-reg-form-name />
                </div>

                <div class="form-group">
                    <label>Фамилия</label>
                    <input type="text" class="form-control" data-reg-form-last-name />
                </div>

                <div class="form-group text-center">
                    <p>
                        Регистрируясь на сервисе Вы осуществляете полный и безоговорочный акцепт <a href="/dogovor-oferta/" target="_blank">договора оферты</a>.
                    </p>
                </div>

                <div class="text-center">
                    <button type="submit" href="javascript:void(0)" class="btn btn-dark">Зарегистрировать</button>
                </div>
            </form>
            <a href="<?=\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestedPageDirectory()?>/" class="text-secondary">Авторизоваться</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    SystemAuthRegistrationComponent.init();
</script>
