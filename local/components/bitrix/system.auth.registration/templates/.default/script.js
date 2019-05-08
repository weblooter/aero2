class SystemAuthRegistrationComponent {

    static init(){
        var curClass = this;

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('#registrationform').addEventListener('submit', function (e) {
                curClass.tryReg(e);
            })
        });

        grecaptcha.ready(function() {
            curClass.replaceRecaptcha();
        });
    }

    static replaceRecaptcha()
    {
        grecaptcha.execute(LocalCore.getRecaptchaSiteKey(), {action: 'contact'}).then(function(token) {
            document.querySelector('#registrationform #g-recaptcha-response').value = token;
        });
    }

    static tryReg(e)
    {
        e.preventDefault();

        var curClass = this,
            email = document.querySelector('#registrationform [data-reg-form-email]').value,
            password = document.querySelector('#registrationform [data-reg-form-password]').value,
            repPassword = document.querySelector('#registrationform [data-reg-form-repeat-password]').value,
            name = document.querySelector('#registrationform [data-reg-form-name]').value,
            lastName = document.querySelector('#registrationform [data-reg-form-last-name]').value;

        try {

            if(
                email.length < 1
                || password.length < 1
                || repPassword.length < 1
                || name.length < 1
                || lastName.length < 1
            )
            {
                throw new Error('Для регистрации необходимо заполнить все поля.')
            }

            if( !LocalCore.isEmail(email))
            {
                throw new Error('E-mail не похож на почтовый адрес.')
            }

            if( password.length < 6 || repPassword.length < 6 )
            {
                throw new Error('Длина паролей не может быть меньше 6 символов.')
            }

            if( password != repPassword )
            {
                throw new Error('Пароли не совпадают.')
            }


            axios.post('/ajax/system-user-register/', qs.stringify({
                'email': email,
                'password': password,
                'repPassword': repPassword,
                'name': name,
                'lastName': lastName,
                'g-recaptcha-response': document.querySelector('#registrationform #g-recaptcha-response').value,

            }))
                .then(function (response) {
                    if(response.data.result == 'success'){
                        window.location.href = (location.origin+location.pathname);
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
        catch(e)
        {
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