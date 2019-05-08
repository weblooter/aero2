<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$strRecaptchaSiteKey = \Bitrix\Main\Config\Configuration::getInstance()->get('recaptcha')['site_key'];
\Bitrix\Main\Page\Asset::getInstance()->addJs('https://www.google.com/recaptcha/api.js?render='.$strRecaptchaSiteKey );
?>
<div class="login">

    <!-- Login -->
    <div class="login__block active">
        <div class="login__block__body">
            <form class="mb-3" action="" method="post" id="forgotform">
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response" />

                <div class="form-group">
                    <input type="text" class="form-control text-center" placeholder="E-mail" data-forgot-form-email />
                </div>
                <button type="submit" href="javascript:void(0)" class="btn btn-dark">Восстановить</button>
            </form>
            <a href="<?=\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestedPageDirectory()?>/" class="text-secondary">Авторизоваться</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    SystemAuthForgotpasswdComponent.init();
</script>