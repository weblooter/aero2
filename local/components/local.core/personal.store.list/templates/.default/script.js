class PersonalStoreListComponent {
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
                                    window.location.href=window.location;
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
}