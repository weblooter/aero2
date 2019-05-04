class PersonalAsideMenuComponent
{
    static deleteCompany(intId, strCompanyName)
    {
        swal({
            title: 'Удалить компанию "'+strCompanyName+'"?',
            html: 'При удалении компании магазины компании и их торговые площадки также будут удалены. Перерасчет за оставшийся оплаченный период торговых площадок произведен не будет!<br/>Вы желаете удалить компанию "'+strCompanyName+'"?',
            type: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-danger',
            confirmButtonText: 'Да, удалите компанию',
            cancelButtonClass: 'btn btn-light',
            cancelButtonText: 'Нет, я передумал',
        }).then((result) => {
            if( result )
            {
                axios.post('/ajax/company/delete/' + intId + '/')
                    .then(function (response) {
                        if (response.data.result == 'SUCCESS') {
                            swal({
                                title: 'Компания "'+strCompanyName+'" успешно удалена!',
                                type: 'success',
                                onClose: () => {
                                    window.location.href='/personal/company/';
                                }
                            });
                        } else {
                            swal({
                                title: 'Ошибка!',
                                type: 'error',
                                html: 'Во время удаления компании "'+strCompanyName+'" произошла ошибка:<br/>'+response.data['error_text']
                            });
                        }
                    });
            }
        });
    }
}