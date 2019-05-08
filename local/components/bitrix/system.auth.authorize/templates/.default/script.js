class SystemAuthAuthorizeComponent {

    static init()
    {
        var curClass = this;

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('#loginform').addEventListener('submit', function (e) {
                curClass.tryAuth(e);
            })
        });

        grecaptcha.ready(function() {
            curClass.replaceRecaptcha();
        });
    }

    static replaceRecaptcha()
    {
        grecaptcha.execute(LocalCore.getRecaptchaSiteKey(), {action: 'contact'}).then(function(token) {
            document.querySelector('#loginform #g-recaptcha-response').value = token;
        });
    }

    static tryAuth(e)
    {
        e.preventDefault();

        var curClass = this,
            login = document.querySelector('#loginform [data-login-form-login]').value,
            password = document.querySelector('#loginform [data-login-form-password]').value;

        try {
            if( login.length < 1 || password.length < 1 )
            {
                throw new Error('Для авторизации необходимо заполнить E-mail и пароль.');
            }

            axios.post('/ajax/system-user-authorize/', qs.stringify({
                login: login,
                password: password,
                'g-recaptcha-response': document.querySelector('#loginform #g-recaptcha-response').value,
            }))
                .then(function (response) {
                    if(response.data.result == 'success'){
                        window.location.reload();
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
