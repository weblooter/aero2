class SystemAuthForgotpasswdComponent {

    static init()
    {
        var curClass = this;

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('#forgotform').addEventListener('submit', function (e) {
                curClass.tryRestore(e);
            })
        });

        grecaptcha.ready(function() {
            curClass.replaceRecaptcha();
        });
    }

    static replaceRecaptcha()
    {
        grecaptcha.execute(LocalCore.getRecaptchaSiteKey(), {action: 'contact'}).then(function(token) {
            document.querySelector('#forgotform #g-recaptcha-response').value = token;
        });
    }

    static tryRestore(e)
    {
        e.preventDefault();

        var curClass = this,
            email = document.querySelector('#forgotform [data-forgot-form-email]').value;

        try {
            if( email.length < 1 )
            {
                throw new Error('Для восстановления пароля необходимо заполнить E-mail.');
            }

            if( !LocalCore.isEmail(email) )
            {
                throw new Error('E-mail не похож на электронный адрес.');
            }

            axios.post('/ajax/system-user-restore-password/', qs.stringify({
                email: email,
                'g-recaptcha-response': document.querySelector('#forgotform #g-recaptcha-response').value,
            }))
                .then(function (response) {
                    if(response.data.result == 'success'){
                        swal({
                            type: 'success',
                            title: 'Пароль восстановлен',
                            html: '<p>Мы изменили Ваш пароль и отправили его Вам на электронную почту. Дождитесь письма, авторизуйтесь и не забудьте сменить пароль на удобный для Вас.</p>',
                            onClose: () => {
                                window.location.href=(location.origin+location.pathname);
                            }
                        })
                    } else {
                        throw new Error(( response.data.hasOwnProperty('error_text') ) ? response.data['error_text'] : '');
                    }
                })
                .catch(function (e) {
                    swal({
                        title: 'Ошибка',
                        html: '<p class="text-left">'+e.message+'</p>',
                        type: 'error',
                    });
                    curClass.replaceRecaptcha();
                });

        }
        catch (e) {
            swal({
                title: 'Ошибка',
                html: '<p class="text-left">'+e.message+'</p>',
                type: 'error',
            });
            curClass.replaceRecaptcha();
        }

        return false;

    }
}
