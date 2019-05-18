class LocalCore {
    static initFormComponents() {
        if ($(".textarea-autosize")[0] && autosize($(".textarea-autosize")), $("input-mask")[0] && $(".input-mask").mask(), $("select.select2")[0]) {
            var a = $(".select2-parent")[0] ? $(".select2-parent") : $("body");
            $("select.select2").select2({dropdownAutoWidth: !0, width: "100%", dropdownParent: a, language: "ru",})
        }

        if( $('[type="file"].file').length > 0 )
        {
            $('[type="file"].file').fileinput({'showUpload':false, 'showCancel':false});
        }

        if( $('[data-toggle="tooltip"]').length > 0 )
        {
            $('[data-toggle="tooltip"]').tooltip();
        }

        if( $('select.taxonomy-field-select').length > 0 )
        {
            $('select.taxonomy-field-select').each(function (k, v) {
                $(v).select2({
                    dropdownAutoWidth: !0,
                    width: "100%",
                    placeholder: $(v).attr('data-placeholder'),
                    language: "ru",
                    minimumInputLength: 3,
                    ajax: {
                        transport: function (params, success, failure) {
                            axios.post('/ajax/taxonomy/'+$(v).attr('data-action')+'/', qs.stringify(params.data))
                                .then(function (response) {
                                    success(response.data);
                                })
                                .catch(function (error) {
                                    failure(error.data);
                                });
                        },
                        cache: false
                    }
                });
            });
        }
    }

    static setRecaptchaSiteKey(strKey)
    {
        this._recaptchaSiteKey = strKey;
    }

    static getRecaptchaSiteKey()
    {
        return this._recaptchaSiteKey;
    }

    static isEmail(strEmail)
    {
        return /^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i.test( strEmail );
    }

    static notify(title, sessage)
    {

        $.notify({
            title: title,
            message: sessage,
            url: ''
        },{
            element: 'body',
            type: 'inverse',
            allow_dismiss: true,
            placement: {
                from: 'top',
                align: 'right'
            },
            offset: {
                x: 20,
                y: 20
            },
            spacing: 10,
            z_index: 1031,
            delay: 3000,
            timer: 1000,
            url_target: '_blank',
            mouse_over: false,
            animate: {
                enter: 'animated fadeInLeft',
                exit: 'animated fadeOutLeft'
            },
            template:   '<div data-notify="container" class="alert alert-dismissible alert-{0} alert--notify" role="alert">' +
                '<button type="button" aria-hidden="true" data-notify="dismiss" class="close"><i class="zmdi zmdi-close text-secondary"></i></button>' +
                '<div class="d-inline-block" data-notify="title">{1}</div> ' +
                '<span class="d-block" data-notify="message">{2}</span>' +
                '</div>'
        });
    }
}