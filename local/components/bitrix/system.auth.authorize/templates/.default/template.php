<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$strRecaptchaSiteKey = \Bitrix\Main\Config\Configuration::getInstance()->get('recaptcha')['site_key'];
\Bitrix\Main\Page\Asset::getInstance()->addJs('https://www.google.com/recaptcha/api.js?render='.$strRecaptchaSiteKey );
?>
<div class="login">

    <!-- Login -->
    <div class="login__block active">
        <div class="login__block__body">
            <form class="mb-3" action="" method="post" id="loginform">
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response" />

                <div class="form-group">
                    <input type="text" class="form-control text-center" placeholder="E-mail" data-login-form-login />
                </div>

                <div class="form-group">
                    <input type="password" class="form-control text-center" placeholder="Пароль" data-login-form-password />
                </div>
                <button type="submit" href="javascript:void(0)" class="btn btn-dark">Войти</button>
            </form>
            <a href="?register=yes" class="text-secondary">Регистрация</a><br/>
            <a href="?forgot_password=yes" class="text-secondary">Я не помню пароль</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    SystemAuthAuthorizeComponent.init();
</script>