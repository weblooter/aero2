class PersonalCompanyFormComponent {
    static changeBlock() {
        var companyType = document.querySelector('#companyformcomponent [data-company-type-value]').value,
            urCompanyBlock = document.querySelector('#companyformcomponent [data-company-type="UR"]');
        if( companyType == 'UR' )
        {
            urCompanyBlock.classList.remove('d-none');
        }
        else
        {
            urCompanyBlock.classList.add('d-none');
        }
    }

    static searchCompanyByInn()
    {
        var obInnField = document.querySelector('#companyformcomponent [data-inn-field]'),
            inn = obInnField.value;

        if( inn.length >=10 && inn.length <= 12 )
        {
            axios.post('/ajax/dadata/search-company/inn/' + inn + '/')
                .then(function (response) {
                    if (response.data.result == 'SUCCESS') {

                        if( typeof response.data.company == 'object' && response.data.company.hasOwnProperty('COMPANY_NAME_SHORT'))
                        {
                            swal({
                                title: 'Мы что то нашли',
                                html: 'По Вашему ИНН мы нашли <b>'+response.data.company.COMPANY_NAME_SHORT+'</b>. Запонить данные в автоматическом режиме?',
                                type: 'question',
                                showCancelButton: true,
                                buttonsStyling: false,
                                confirmButtonClass: 'btn btn-success',
                                confirmButtonText: 'Да',
                                cancelButtonClass: 'btn btn-light',
                                cancelButtonText: 'Нет',
                            }).then((result) => {
                                if( result )
                                {

                                    console.log(response.data.company);

                                    for(var strKey in response.data.company)
                                    {
                                        if( strKey == 'COMPANY_INN' )
                                            continue;

                                        if( response.data.company.hasOwnProperty(strKey) )
                                        {
                                            if( document.querySelectorAll('#companyformcomponent [name="COMPANY_FIELD['+strKey+']"]').length > 0 )
                                            {
                                                document.querySelector('#companyformcomponent [name="COMPANY_FIELD['+strKey+']"]').value = response.data.company[strKey];
                                            }
                                        }
                                    }
                                }
                            });
                        }

                    }
                    else{
                        swal({
                            title: 'Не нашли',
                            type: 'error',
                            html: 'К сожалению нам не удалось найти компанию. Запоните форму самостоятельно.'
                        });
                    }
                });
        }
        else
        {
            swal({
                title: 'Ошибка!',
                type: 'error',
                html: 'ИНН должно иметь 10 символов, если это организация, или 12, если это ИП.'
            });
        }
    }

    static init()
    {
        this.changeBlock();

        if ($(".textarea-autosize")[0] && autosize($(".textarea-autosize")), $("input-mask")[0] && $(".input-mask").mask(), $("select.select2")[0]) {
            var a = $(".select2-parent")[0] ? $(".select2-parent") : $("body");
            $("select.select2").select2({dropdownAutoWidth: !0, width: "100%", dropdownParent: a})
        };
    }
}