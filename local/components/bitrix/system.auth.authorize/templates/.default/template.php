<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="authbackground"></div>
<div class="authscreen_errors">
    <div class="container">
    </div>
</div>
<div class="authscreen_holder">
    <div class="authscreen_decor"></div>
    <div class="authscreen">
        <form action="#" class="authform">
            <div class="logo">
                <div class="logoinner">ROBOFEED</div>
            </div>
            <div class="inputwrap icon-user">
                <input type="text" placeholder="Логин" required="required" name="login">
            </div>
            <div class="inputwrap icon-key">
                <input type="password" placeholder="Пароль" required="required" name="password">
            </div>
            <input type="submit" value="Авторизоваться">
            <p><a href="javascript:void(0)" class="activateRegForm">Зарегистрироваться</a><a href="#">Забыли пароль?</a></p>
        </form>
        <form action="#" class="regform">
            <div class="logo">
                <div class="logoinner">ROBOFEED</div>
            </div>
            <div class="inputwrap icon-user">
                <input type="text" placeholder="E-mail адрес" name="email" required>
            </div>
            <div class="inputwrap icon-key">
                <input type="password" placeholder="Пароль" name="password" required>
            </div>
            <div class="inputwrap icon-key">
                <input type="password" placeholder="Повторите пароль" name="password_repeat" required>
            </div>
            <input type="submit" value="Зарегистрироваться">
            <p><a href="javascript:void(0)" class="activateAutForm">Авторизация</a></p>
        </form>
    </div>
</div>