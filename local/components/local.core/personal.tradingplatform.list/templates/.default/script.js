class PersonalTradingplatformListComponent {
    static deleteTP(intTpId, strTPName)
    {
        swal({
            title: 'Удаление торговой площадки',
            html: 'При удалении торговой площадки <b>"'+strTPName+'"</b> денежные средства за оплаченный период не возвращаются на счет. Желаете удалить?',
            type: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-danger',
            confirmButtonText: 'Да, удалить',
            cancelButtonClass: 'btn btn-light',
            cancelButtonText: 'Нет, я передумал',
        }).then((result) => {
            if( result )
            {
                axios.post('/ajax/trading-platform/delete/' + intTpId + '/')
                    .then(function (response) {
                        if (response.data.result == 'SUCCESS') {
                            swal({
                                title: 'Торговая площадка успешно удалена!',
                                type: 'success',
                                onClose: () => {
                                    window.location.href=location.href;
                                }
                            });
                        } else {
                            swal({
                                title: 'Ошибка!',
                                type: 'error',
                                html: '<p class="text-left">Во время удаления торговой площадки <b>"'+strTPName+'"</b> произошла ошибка:<br/>'+response.data['error_text']+'</p>'
                            });
                        }
                    });
            }
        });
    }

    static activateTP(intTpId, strTPName)
    {
        swal({
            title: 'Активация торговой площадки',
            html: 'При активации торговой площадки <b>"'+strTPName+'"</b> со счета произойдет списание денежных средств за месяц согласно тарифу данного магазина, если торговая площадка не была оплачена ранее или срок ее оплаты истек. Желаете активировать?',
            type: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-danger',
            confirmButtonText: 'Да, активировать',
            cancelButtonClass: 'btn btn-light',
            cancelButtonText: 'Нет, я передумал',
        }).then((result) => {
            if( result )
            {
                axios.post('/ajax/trading-platform/activate/' + intTpId + '/')
                    .then(function (response) {
                        if (response.data.result == 'SUCCESS') {
                            swal({
                                title: 'Торговая площадка успешно активированна!',
                                type: 'success',
                                onClose: () => {
                                    window.location.href=location.href;
                                }
                            });
                        } else {
                            swal({
                                title: 'Ошибка!',
                                type: 'error',
                                html: '<p class="text-left">Во время активации торговой площадки <b>"'+strTPName+'"</b> произошла ошибка:<br/>'+response.data['error_text']+'</p>'
                            });
                        }
                    });
            }
        });
    }

    static deactivateTP(intTpId, strTPName)
    {
        swal({
            title: 'Деактивация торговой площадки',
            html: 'При деактивации торговой площадки <b>"'+strTPName+'"</b> оплаченный период не замораживается, а потраченные денежные средства не возвращаются на счет. Так же у деактивированной торговой площадки перестает обновляться экспортный файл. Желаете деактивировать?',
            type: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-danger',
            confirmButtonText: 'Да, деактивировать',
            cancelButtonClass: 'btn btn-light',
            cancelButtonText: 'Нет, я передумал',
        }).then((result) => {
            if( result )
            {
                axios.post('/ajax/trading-platform/deactivate/' + intTpId + '/')
                    .then(function (response) {
                        if (response.data.result == 'SUCCESS') {
                            swal({
                                title: 'Торговая площадка успешно деактивированна!',
                                type: 'success',
                                onClose: () => {
                                    window.location.href=location.href;
                                }
                            });
                        } else {
                            swal({
                                title: 'Ошибка!',
                                type: 'error',
                                html: '<p class="text-left">Во время деактивации торговой площадки <b>"'+strTPName+'"</b> произошла ошибка:<br/>'+response.data['error_text']+'</p>'
                            });
                        }
                    });
            }
        });
    }

    static showExportLink(strLinkText)
    {
        swal({
            html: '<p class="text-left">Ваша ссылка на экспортный файл:<br/><br/><b class="text-warning">'+strLinkText+'</b><br/><br/>Ссылка будет отдавать необходимый файл только <b>после активации</b> торговой площадки и только после <b>успешной попытки формирования</b> экспортного файла.<br/><br/>Укажите эту ссылку в настройках выбранной торговой площадки, как ссылку на прайс-лист или список нуменклатуры.</p>'
        });
    }
}