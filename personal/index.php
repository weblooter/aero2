<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetTitle("Рабочий стол");
$APPLICATION->AddChainItem('Рабочий стол');
?>
<?/*
    <div class="col-12">

        <a href="<?=\Local\Core\Inner\Route::getRouteTo(
            'company',
            'add'
        )?>" class="btn btn-warning">+ Добавить компанию</a>

    </div>
*/?>
    <a onclick="javascript:swal('Hello world!');" class="button">test simple</a>
    <a onclick="javascript:swal('Here is the title!', '...and here is the text!');" class="button orange">test text</a>
    <a onclick="javascript:swal('Good job!', 'You clicked the button!', 'success');" class="button red">test success</a>
    <a onclick="javascript:usure();" class="button black">test sure</a>

    <script>
        function usure(){
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        swal("Poof! Your imaginary file has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Your imaginary file is safe!");
                    }
                });
        }
    </script>

    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/assets/css/desktop.css">
    <script src="<?=SITE_TEMPLATE_PATH?>/assets/js/highcharts.js"></script>
    <div class="alert alert-danger" role="alert">
        У вас молоко убежало так, проверьте, может еще не совсем и можно еще возратить все взад чтобы не убегало да.
    </div>

    <div class="desktop">
        <div class="desktop-head">
            <div class="shop-select">
                <span><b>Выбранный магазин:</b></span>
                <select>
                    <option value="1">Fides</option>
                    <option value="2">Еще один</option>
                </select>
            </div>
            <div class="shop-actions">
                <a href="#"><i class="icon-plus"></i> Добавить</a>
                <a href="#"><i class="icon-pencil"></i> Редактировать</a>
                <a href="#"><i class="icon-trash"></i> Удалить</a>
            </div>
        </div>
        <div class="shop-info">
            <div class="row">
                <div class="col-sm-7 border">
                    <div class="shop-left">
                        <p><span>Сайт:</span><span><b>www.lox.ru</b></span></p>
                        <p><span>Источник данных:</span><span>Загруженный файл XML <a href="#" title="Скачать"><i class="icon-cloud-download"></i></a></span></p>
                        <p><span>Дата последнего импорта:</span><span>2019-04-22 15:14:03</span></p>
                        <p><span>Дата последнего Успехго импорта:</span><span>2019-04-22 15:14:03</span></p>
                        <p><span>Текущее состояние Robofeed XML:</span><span><span class="success">Импорт успешен</span></span></p> <!-- fail -->
                        <p><span>Общее кол-во товаров в Robofeed XML:</span><span>12 803</span></p>
                        <p><span>Из них импортировано:</span><span>12 803</span></p>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="shop-right">
                        <div class="tarif">
                            <p><span>Тариф:</span><span><b>Минимаркет (Акция)</b> <a href="#" title="Редактировать"><i class="icon-pencil"></i></a></span></p>
                            <p><span>Максимум товаров:</span><span>20000</span></p>
                            <p><span>Стоимость за площадку:</span><span>1700 руб./мес.</span></p>
                            <p><span>Начало расчетного периода:</span><span>2019-04-02 21:45</span></p>
                            <p><span>Окончание расчетного периода:</span><span>2019-06-01 00:00</span></p>
                            <p><span>Будет переключен на тариф:</span><span><b>Минимаркет</b></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ul class="places">
            <li class="title"><span><b>Торговые площадки</b></span><div class="righted"><a href="#" class="linkbtn"><i class="icon-plus"></i> Добавить</a></div></li>
            <li><span>ЯндексМаркет</span><div class="righted"><a href="#" class="linkbtn"><i class="icon-pencil"></i> Редактировать</a> <a href="#" class="linkbtn"><i class="icon-trash"></i> Удалить</a></div></li>
            <li><span>Беру</span><div class="righted"><a href="#" class="linkbtn"><i class="icon-pencil"></i> Редактировать</a> <a href="#" class="linkbtn"><i class="icon-trash"></i> Удалить</a></div></li>
        </ul>
        <div class="row">
            <div class="col-sm-7">
                <h3 class="bold">Статистика выгрузок</h3>
                <div id="desktop-graph"></div>
                <div class="legend">
                    <p><span style="background: var(--yellow)"></span> Новые</p>
                    <p><span style="background: #c5c5c5"></span> Без изменений</p>
                    <p><span style="background: var(--darkgray)"></span> Ошибки</p>
                </div>
            </div>
            <div class="col-sm-5">
                <h3 class="bold">Лог</h3>
                <table class="logtable">
                    <tr>
                        <th>Дата</th>
                        <th>Всего товаров</th>
                        <th>Импорт</th>
                        <th>Статус</th>
                    </tr>
                    <tr>
                        <td>01.02 18:00</td>
                        <td>21000</td>
                        <td>4371</td>
                        <td><span class="success">Успех</span></td>
                    </tr>
                    <tr>
                        <td>01.02 18:00</td>
                        <td>21000</td>
                        <td>4371</td>
                        <td><span class="success">Успех</span></td>
                    </tr>
                    <tr>
                        <td>01.02 18:00</td>
                        <td>21000</td>
                        <td>4371</td>
                        <td><span class="fail">Ошибка</span></td>
                    </tr>
                    <tr>
                        <td>01.02 18:00</td>
                        <td>21000</td>
                        <td>4371</td>
                        <td><span class="success">Успех</span></td>
                    </tr>
                    <tr>
                        <td>01.02 18:00</td>
                        <td>21000</td>
                        <td>4371</td>
                        <td><span class="fail">Ошибка</span></td>
                    </tr>
                    <tr>
                        <td>01.02 18:00</td>
                        <td>21000</td>
                        <td>4371</td>
                        <td><span class="success">Успех</span></td>
                    </tr>
                    <tr>
                        <td>01.02 18:00</td>
                        <td>21000</td>
                        <td>4371</td>
                        <td><span class="success">Успех</span></td>
                    </tr>
                    <tr>
                        <td>01.02 18:00</td>
                        <td>21000</td>
                        <td>4371</td>
                        <td><span class="success">Без изменений</span></td>
                    </tr>
                    <tr>
                        <td>01.02 18:00</td>
                        <td>21000</td>
                        <td>4371</td>
                        <td><span class="success">Успех</span></td>
                    </tr>
                    <tr>
                        <td>01.02 18:00</td>
                        <td>21000</td>
                        <td>4371</td>
                        <td><span class="success">Успех</span></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="loadmoretd"><a href="#" class="linkbtn"><i class="icon-compass"></i> Отобразить все</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $('.shop-select select').selectpicker();
        });

        var chart = Highcharts.chart('desktop-graph', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Статистика по выгрузкам за неделю'
            },
            xAxis: {
                categories: ['01.02.2019', '02.02.2019', '03.02.2019', '04.02.2019', '05.02.2019', '06.02.2019', '07.02.2019']
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Количество товарных предложений'
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'normal',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
            },
            tooltip: {
                headerFormat: '<b>{point.x}</b><br/>',
                pointFormat: '{series.name}: {point.y}<br/>Всего: {point.stackTotal}'
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                }
            },
            series: [{
                name: 'Новые',
                data: [4000, 3000, 4000, 7000, 2000, 3000, 1000],
                color: 'rgb(255, 179, 0)'
            }, {
                name: 'Без изменений',
                data: [2000, 2000, 3000, 2000, 1000, 2000, 3000],
                color: 'rgb(197, 197, 197)'
            }, {
                name: 'Ошибки',
                data: [3000, 4000, 4000, 2000, 5000, 1000, 2000],
                color: 'rgb(34, 34, 34)'
            }]
        });
    </script>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>