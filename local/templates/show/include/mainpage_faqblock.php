<section class="faqblock">
    <div class="container">
        <h2 class="bold">Часто задаваемые вопросы</h2>
        <div class="faq">
            <p class="question"><b>Что такое торговые площадки и зачем они мне?</b></p>
            <p class="answer">Торговые площадки - сайты агрегаторы товаров, которые позволяют потенциальным покупателям узнать стоимость искомых товаров и условия доставок у разных интернет магазинов, тем самым помогая определить самые выгодные условия для покупки. Размещение Ваших торговых предложений на торговых площадках может сыграть ключевую роль в продажах, благодаря генерации потока покупателей с площадки.</p>
            <p class="question"><b>Какие сложности в интеграции с торговыми площадками?</b></p>
            <p class="answer">Помимо заключения договора и прохождения проверки вашего сайта администрацией торговой площадки, есть еще одна немаловажная и очень трудоемкая задача. По условию работы с торговыми площадками, ваш сайт обязан периодически генерировать список своих торговых предложений (так называемый "фид") для отправки на площадку. Однако, сложность заключается в том, что каждая площадка требует оформлять этот список в индивидуальном для нее формате, что влечет за собой необходимость поддерживать актуальной информацию о товарах для каждой площадки индивидуально. Если это не будет сделано - площадка не будет отображать ваши торговые предложения, а в некоторых случаях может заблокировать Вас.</p>
            <p class="question"><b>Как Robofeed может помочь мне?</b></p>
            <p class="answer">Наш сервис берет на себя всю заботу о генерации и обновлении ваших списков товаров (фидов). Вы единожды настраиваете на своем сайте механизм составления списка товаров. Далее мы будем получать этот список, приводить его к правильному виду для каждой выбранной Вами площадки и своевременно отправлять его им. Таким образом, вы сможете забыть о необходимости постоянно настраивать и администрировать каждую площадку. Все будет сделано за Вас.</p>
            <p class="question"><b>Как происходит интеграция?</b></p>
            <p class="answer">После регистрации на нашем сервисе Вам будет необходимо создать компанию и магазин в личном кабинете, загрузить в магазин <a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'robofeed')?>" target="_blank">Robofeed XML</a>. Создать Robofeed XML Вам поможет Ваш программист из отдела web-разработок. Достаточно будет передать ему ссылку с описанием структуры <a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'robofeed')?>" target="_blank">Robofeed XML</a>. После загрузки Robofeed XML в магазин останется подождать пока мы проиндексируем полученный фид и все. Дальше Вы можете занять настройкой генерации экспортных файлов для выбранных Вами торговых площадок.</p>
            <p class="question"><b>У меня нет программиста, может ли Вы помочь мне в создании Robofeed XML?</b></p>
            <p class="answer">
                Мы заняты расширением функционала сервиса, списком поддерживаемых торговых площадок и технической поддержкой. По этой причине, к сожалению, у нас не остается времени на помощь в создании Robofeed XML.<br/>
                Тем не менее наши партнеры из <a href="https://weblooter.ru/?utm_source=robofeed.ru&utm_medium=make_robofeed" target="_blank"><img src="/local/templates/.default/assets/img/weblooter.svg" height="38"> Weblooter Inc.</a> помогут Вам с этой задачей.
            </p>
        </div>
    </div>
</section>