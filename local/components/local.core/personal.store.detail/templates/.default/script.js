class PersonalStoreDetailComponent {
    static getStoreListLink()
    {
        if( this.strStoreListLink != undefined && this.strStoreListLink != null )
        {
            return this.strStoreListLink;
        }
        else
        {
            return '/personal/';
        }
    }

    static setStoreListLink(strStoreListLink)
    {
        this.strStoreListLink = strStoreListLink;
    }

    static deleteStore(intId, strStoreName)
    {
        swal({
            title: 'Удалить магазин "'+strStoreName+'"?',
            html: 'При удалении магазина его торговые площадки также будут удалены. Перерасчет за оставшийся оплаченный период торговых площадок произведен не будет!<br/>Вы желаете удалить магазин "'+strStoreName+'"?',
            type: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-danger',
            confirmButtonText: 'Да, удалите магазин',
            cancelButtonClass: 'btn btn-light',
            cancelButtonText: 'Нет, я передумал',
        }).then((result) => {
            if( result )
            {
                axios.post('/ajax/store/delete/' + intId + '/')
                    .then(function (response) {
                        if (response.data.result == 'SUCCESS') {
                            swal({
                                title: 'Магазин "'+strStoreName+'" успешно удален!',
                                type: 'success',
                                onClose: () => {
                                    window.location.href=PersonalStoreDetailComponent.getStoreListLink();
                                }
                            });
                        } else {
                            swal({
                                title: 'Ошибка!',
                                type: 'error',
                                html: '<p class="text-left">Во время удаления магазина "'+strStoreName+'" произошла ошибка:<br/>'+response.data['error_text']+'</p>'
                            });
                        }
                    });
            }
        });
    }

    static changeTariff($intStoreId, $strTariffCode, $planDirection)
    {
        var obTextes = {
            up: 'Выбранный тариф дороже действующего. Мы произведем возврат средств по оплаченным торговым площадкам пропорционально их оставшемуся периоду. После это мы проведем списание средств за каждую активную торговую площадку согласно стоимости выбранного тарифа. Убедитесь, что у Вас хватает средств на счету для оплаты всех активных торговых площадок данного магазина. По торговым площадкам, которые на момент смены тарифного плана были деактивированы, так же произойдет возврат, но активированы они не будут. Сменить тариф?',
            down: 'Выбранный тариф дешевле текущего. Средства, оплаченные за текущие торговые площадки, не будут возвращены. Сменить тариф?'
        };

        swal({
            title: 'Смена тарифа',
            html: obTextes[$planDirection],
            type: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-danger',
            confirmButtonText: 'Да, сменить тариф',
            cancelButtonClass: 'btn btn-light',
            cancelButtonText: 'Нет, я передумал',
        }).then((result) => {
            if( result )
            {
                axios.post('/ajax/store/change_tariff/' + $intStoreId + '/' + $strTariffCode + '/')
                    .then(function (response) {

                        if (response.data.result == 'SUCCESS') {
                            swal({
                                title: 'Тариф успешно изменен!',
                                type: 'success',
                                onClose: () => {
                                    window.location.href=location.href;
                                }
                            });
                        } else {
                            swal({
                                title: 'Ошибка!',
                                type: 'error',
                                html: '<p class="text-left">Во время изменения тарифа произошла ошибка:<br/>'+response.data['error_text']+'</p>'
                            });
                        }
                });
            }
        });
    }

    static init()
    {
        document.addEventListener('DOMContentLoaded', function () {
            // Tooltips for Flot Charts
            if ($('.flot-chart')[0]) {
                $('.flot-chart').bind('plothover', function (event, pos, item) {

                    if (item) {
                        if(
                            item.seriesIndex == '0'
                            || item.seriesIndex == '1'
                        )
                        {

                            var x = item.series.xaxis.options.ticks[item.datapoint[0]][1],
                                y = item.datapoint[1].toFixed(0).replace(/\d(?=(\d{3})+$)/g, '$& ');
                            $('.flot-tooltip').html('<b>'+item.series.label + ':</b><br/><br/>' + x + ' - ' + y).css({top: item.pageY+5, left: item.pageX+5}).show();
                        }
                        else if( item.seriesIndex == '2' )
                        {
                            var x = item.series.xaxis.options.ticks[item.datapoint[0]][1],
                                y = item.series.yaxis.options.ticks[item.datapoint[1]][1];
                            $('.flot-tooltip').html('<b>'+item.series.label + ':</b><br/><br/>' + x + ' - ' + y).css({top: item.pageY+5, left: item.pageX+5}).show();
                        }
                    }
                    else {
                        $('.flot-tooltip').hide();
                    }
                });

                $('<div class="flot-tooltip"></div>').appendTo('body');
            }
        });
    }
}