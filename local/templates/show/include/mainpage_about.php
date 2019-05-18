<section class="about">
    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                <div class="robotblock">
                    <img src="<?=SITE_TEMPLATE_PATH?>/assets/images/robot.png" alt="">
                    <div class="robot">
                        <div class="head"></div>
                        <div class="body"></div>
                        <div class="fire left"></div>
                        <div class="fire center"></div>
                        <div class="fire right"></div>
                        <div class="arm-right"></div>
                        <div class="arm-left"><div class="arm-left2"></div><div class="arm-left3"></div></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <h2 class="bold">Что такое ROBOFEED?</h2>
                <p>Наш сервис создан чтобы <b>упростить процесс выхода</b> ваших товарных предложений <b>на торговые площадки</b>.</p>
                <p>Мы помогаем интернет-магазинам генерировать специальные прайс-листы, которые сервисы торговых площадок используют для актуализации информации о ценах и наличие товаров.</p>
                <p>Единожды интегрировавшись с Robofeed Вы получаете возможность <b>автоматически генерировать и актуализировать feed-файлы</b> для постоянно обновляющегося и расширяющегося списка торговых площадок, снимая с себя все заботы об интеграции с каждой из них отдельно.</p>
                <p>Вся работа по генерации производится на наших мощных серверах, что так же облегчает жизнь вашему интернет-магазину и позволяет снизить Ваши затраты на аренду серверных мощностей. Даже есть Ваш сайт временно перестанет быть доступным - мы продолжим работу, а значит <b>Вы не потеряете заказы</b>.</p>
            </div>
        </div>
        <div class="pros_block">
            <div class="row">
                <div class="col-md-6">
                    <div class="pros">
                        <div class="image" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/assets/images/pros1.png')"></div>
                        <h4>Простая интеграция</h4>
                        <p>
                            Если у Вас есть IT-специалист - сделайте <a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'robofeed')?>" target="_blank">Robofeed XML</a> и можете приступать к работе!<br/>
                            Если нет - не беда! В лично кабинете Вы можете воспользоваться <a href="<?=\Local\Core\Inner\Route::getRouteTo('tools', 'converter')?>" target="_blank">конвертером</a> и получить Robofeed XML из других форматов.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="pros">
                        <div class="image" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/assets/images/pros2.png')"></div>
                        <h4>Удобный интерфейс</h4>
                        <p>Личный кабинет ROBOFEED позволяет производить настройку генерации прайсов, выбирать нужные торговые площадки для отдельных компаний и интернет-магазинов, следить за статистикой выгрузок и многое другое.</p>
                    </div>
                </div>
                <div class="col-xs-12"></div>
                <div class="col-md-6">
                    <div class="pros">
                        <div class="image" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/assets/images/pros3.png')"></div>
                        <h4>Гибкая настройка</h4>
                        <p>
                            Вы можете выбрать ручной режим и тонко настроить выгружаемую информацию о товарах на торговые площадки, либо выбрать автоматизированный режим, и тогда информация о товарах будет передаваться по определенному нами алгоритму.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="pros">
                        <div class="image" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/assets/images/pros4.png')"></div>
                        <h4>Стабильность</h4>
                        <p>
                            Сгенерировав один раз <a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'robofeed')?>" target="_blank">Robofeed XML</a> Вы можете интегрироваться с любыми торговыми площадками, с которыми мы работаем. Больше не нужно делать отдельный прайс-лист под каждую площадку и следить за их изменениями - это мы берем на себя.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <h2 class="bold">Как это работает? <small>*просто!</small></h2>
        <div class="steps row">
            <div class="col-md-3 col-sm-6">
                <div class="step">
                    <h4><span>Шаг 1</span>Регистрация в сервисе</h4>
                    <p>Зарегистрировавшись в нашей системе, Вы получите удобный личный кабинет, в котором сможете управлять своими проектами, магазинами и торговыми площадками.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="step">
                    <h4><span>Шаг 2</span>Настройка магазина</h4>
                    <p>Для того, чтобы наша система смогла получать информацию о Ваших торговых предложениях, необходимо провести разовую интеграцию Вашего магазина с нашим сервисом.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="step">
                    <h4><span>Шаг 3</span>Выбор торговых площадок</h4>
                    <p>Чтобы сделать торговый процесс еще более эффективным, мы предложим Вам список торговых площадок, наиболее подходящих для продажи конкретно Ваших товаров.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="step">
                    <h4><span>Шаг 4</span>Все готово!</h4>
                    <p>
                        Дождитесь окончания формирования экспортного файла и укажите ссылку на него в настройках торговой площадки. Готово! Теперь мы будем следить за актуализацией информации о Ваших товаров.
                    </p>
                </div>
            </div>
            <div class="stepsbtn">
                <a href="/personal/" class="btn orange">Отлично, я хочу приступить к работе</a>
            </div>
        </div>
    </div>
</section>