<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Главная");
?>
    <section class="firstslide gradient">
        <div class="container">
            <h1>Автоматизированная синхронизация<br>с торговыми площадками</h1>
            <a href="/personal/" class="btn white">Начать работу</a>
            <div class="row triple">
                <div class="col-md-4">
                    <div class="item">
                        <div class="image" style="background-image:url('<?=SITE_TEMPLATE_PATH?>/images/triple1.png')"></div>
                        <h4>Повышение продаж</h4>
                        <p>68% пользователей ищут и&nbsp;выбирают товары на торговых площадках</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item">
                        <div class="image" style="background-image:url('<?=SITE_TEMPLATE_PATH?>/images/triple2.png')"></div>
                        <h4>Экономия на рекламе</h4>
                        <p>Основной трафик с торговых&nbsp;площадок &mdash; заинтересованные клиенты</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item">
                        <div class="image" style="background-image:url('<?=SITE_TEMPLATE_PATH?>/images/triple3.png')"></div>
                        <h4>Быстрый запуск</h4>
                        <p>Начните продавать уже с первого дня после интеграции с площадкой!</p>
                    </div>
                </div>
            </div>
        </div>
        <img class="wave-bottom" src="<?=SITE_TEMPLATE_PATH?>/images/wave-bottom.svg" alt="">
    </section>
    <section class="about">
        <div class="container">
            <div class="row">
                <div class="col-sm-4">
                    <div class="robotblock">
                        <img src="<?=SITE_TEMPLATE_PATH?>/images/robot.png" alt="">
                        <div class="robot">
                            <div class="head"></div>
                            <div class="body"></div>
                            <div class="arm-right"></div>
                            <div class="arm-left"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <h2 class="bold">Что такое ROBOFEED?</h2>
                    <p>Наш сервис создан чтобы <b>упростить процесс выхода</b> ваших товарных предложений <b>на торговые площадки</b>.</p>
                    <p>Мы помогаем интернет-магазинам генерировать специальные прайс-листы, которые сервисы торговых площадок используют для актуализации информации о ценах и наличие товаров.</p>
                    <p>Единожды интегрировавшись с Robofeed Вы получаете возможность <b>автоматически генерировать и актуализировать feed-файлы</b> для постоянно обновляющегося и расширяющегося списка торговых площадок, снимая с себя все заботы об интеграции с каждой из них отдельно.</p>
                    <p>Вся работа по генерации производится на наших мощных серверах, что так же облегчает жизнь вашему интернет-магазину и позволяет снизить Ваши затраты на аренду серверных мощностей</p>
                </div>
            </div>
            <div class="pros_block">
                <div class="row">
                    <div class="col-md-6">
                        <div class="pros">
                            <div class="image" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/pros1.png')"></div>
                            <h4>Простая интеграция</h4>
                            <p>Если у Вас есть IT-специалист &mdash; мы предоставим все необходимые инструкции и будем пристально следить за процессом интеграции. Если нету &mdash; мы сделаем все сами, за разумную плату. </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="pros">
                            <div class="image" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/pros2.png')"></div>
                            <h4>Удобный интерфейс</h4>
                            <p>Личный кабинет ROBOFEED позволяет производить настройку генерации прайсов, выбирать нужные торговые площадки для отдельных компаний и интернет-магазинов, управлять биллингом, просматривать статистику выгрузок и многое другое.</p>
                        </div>
                    </div>
                    <div class="col-xs-12"></div>
                    <div class="col-md-6">
                        <div class="pros">
                            <div class="image" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/pros3.png')"></div>
                            <h4>Гибкая настройка</h4>
                            <p>Вы можете сами управляете данными, которые будут представлять ваш товар на торговой площадке. Габариты, стоимость доставки, цвета, размеры и прочие характеристики &mdash; все это легко настраивается в нашем интерфейсе.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="pros">
                            <div class="image" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/pros4.png')"></div>
                            <h4>Приятная тарификация</h4>
                            <p>Наша тарифная сетка построена таким образом, чтобы в первую очередь быть удобной как для начинающих предпринимателей, так и для гигантов ритейла. Цены на наши услуги стабильны и не меняются внезапно, как многое в этом мире.</p>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="bold">Как это работает? <span>*просто!</span></h2>
            <div class="steps row">
                <div class="col-md-3 col-sm-6">
                    <div class="step">
                        <h4><span>Шаг 1</span>Регистрация в сервисе</h4>
                        <p>Зарегистрировавшись в нашей системе, Вы получите удобный личный кабинет, в котором сможете управлять своими проектами, биллингом и пользоваться техподдержкой.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="step">
                        <h4><span>Шаг 2</span>Настройка Вашего сайта</h4>
                        <p>Для того, чтобы наша система смогла получать от вашего интернет-магазина корректный список товаров и предложений, необходимо провести разовую настройку Вашего сайта.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="step">
                        <h4><span>Шаг 3</span>Выбор торговых платформ</h4>
                        <p>Чтобы сделать торговый процесс еще более эффективным, мы предложим Вам для выбора список торговых площадок, наиболее подходящих для продажи конкретно Ваших товаров.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="step">
                        <h4><span>Шаг 4...</span>Этого шага нет, все готово!</h4>
                        <p>Откиньтесь на спинку кресла, у Вас все получилось и скоро ваши товары появятся на выбранных вами торговых площадках. Занимайтесь более интересными делами, пока робот будет заниматься рутиной.</p>
                    </div>
                </div>
                <div class="stepsbtn">
                    <a href="/personal/" class="btn orange">Отлично, я хочу приступить к работе</a>
                </div>
            </div>
        </div>
    </section>
    <section class="platforms gradient second">
        <img class="wave-top" src="<?=SITE_TEMPLATE_PATH?>/images/wave-top-2.svg" alt="">
        <div class="container">
            <h2 class="bold centered upper">Площадки, на которых о Вас узнают</h2>
            <div class="platforms_items row">
                <div class="col-md-2 col-sm-4 col-xs-6"><a href="/" class="platform"><span class="image" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/images/platform1.svg);"></span><span class="title">market.yandex.ru</span></a></div>
                <div class="col-md-2 col-sm-4 col-xs-6"><a href="/" class="platform"><span class="image" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/images/platform2.svg);"></span><span class="title">e-katalog.ru</span></a></div>
                <div class="col-md-2 col-sm-4 col-xs-6"><a href="/" class="platform"><span class="image" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/images/platform3.svg);"></span><span class="title">beru.ru</span></a></div>
                <div class="col-md-2 col-sm-4 col-xs-6"><a href="/" class="platform"><span class="image" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/images/platform4.svg);"></span><span class="title">auto.ru/parts/</span></a></div>
                <div class="col-md-2 col-sm-4 col-xs-6"><a href="/" class="platform"><span class="image" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/images/platform5.svg);"></span><span class="title">avito.ru</span></a></div>
                <div class="col-md-2 col-sm-4 col-xs-6"><a href="/" class="platform"><span class="image" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/images/platform6.svg);"></span><span class="title">google.ru/retail/</span></a></div>
            </div>
        </div>
        <img class="wave-bottom" src="<?=SITE_TEMPLATE_PATH?>/images/wave-bottom-2.svg" alt="">
    </section>
    <?$APPLICATION->IncludeComponent(
        "local.core:mainpage.calc",
        "",
        Array()
    );?>
    <section class="faqblock">
        <div class="container">
            <h2 class="bold">Часто задаваемые вопросы</h2>
            <div class="faq">
                <p class="question"><b>Что такое торговые площадки и зачем они мне?</b><i class="arrowicon icon-downwards-arrow-key"></i></p>
                <p class="answer">Торговые площадки - сайты агрегаторы товаров, которые позволяют потенциальным покупателям узнать стоимость искомых товаров у разных интернет магазинов, тем самым помогая определить самые выгодные условия для покупки. Размещение Ваших торговых предложений на торговой площадке может сыграть ключевую роль в продажах, благодаря генерирации потока покупателей с площадки.</p>
                <p class="question"><b>Какие сложности в интеграции с торговыми площадками?</b><i class="arrowicon icon-downwards-arrow-key"></i></p>
                <p class="answer">Помимо заключения договора и прохождения проверки вашего сайта администрацией торговой площадки, есть еще одна немаловажная и очень трудоемкая задача. По условию работы с торговыми площадками, ваш сайт обязан периодически генерировать список своих торговых предложений (так называемый "фид") для отправки на площадку. Однако, сложность заключается в том, что каждая площадка требует оформлять этот список в индивидуальном для нее формате, что влечет за собой необходимость поддерживать актуальной информацию о товарах для каждой площадки индивидуально. Если это не будет сделано - площадка не будет отображать ваши торговые предложения, а в некоторых случаях может заблокировать Вас.</p>
                <p class="question"><b>Как Robofeed может помочь мне?</b><i class="arrowicon icon-downwards-arrow-key"></i></p>
                <p class="answer">Наш сервис берет на себя всю заботу о генерации и обновлении ваших списков товаров (фидов). Вы единожды настраиваете на своем сайте механизм составления списка товаров. Далее мы будем получать этот список, приводить его к правильному виду для каждой выбранной Вами площадки и своевременно отправлять его им. Таким образом, вы сможете забыть о необходимости постоянно настраивать и администрировать каждую площадку. Все будет сделано за Вас.</p>
                <p class="question"><b>Как происходит первичная интеграция?</b><i class="arrowicon icon-downwards-arrow-key"></i></p>
                <p class="answer">После регистрации на нашем сайте и заключении электронного договора наш представитель связывается с Вами для уточнения деталей интеграции. Далее наш специалист, получив от Вас доступ к сайту, займется настройкой вашего сайта для работы с Robofeed. Сроки и стоимость интеграции зависят от нескольких критериев, под которые подпадает конкретно ваш интернет-магазин, поэтому оценка будет осуществляться индивидуально, до начала работ.</p>
                <p class="question"><b>У меня есть штатный программист, может ли он помочь в интеграции?</b><i class="arrowicon icon-downwards-arrow-key"></i></p>
                <p class="answer">Конечно, при наличии собственного IT-специалиста Вы всегда можете получить от нас инструкцию по интеграции с Robofeed.</p>
            </div>
        </div>
    </section>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>