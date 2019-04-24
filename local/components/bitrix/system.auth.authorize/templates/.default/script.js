document.addEventListener('DOMContentLoaded', function(){
    SystemAuthAuthorizeComponent.resizeBg();

    window.onresize = function(event) {
        SystemAuthAuthorizeComponent.resizeBg();
    };

    document.querySelector(".authform").addEventListener("submit", function(e) {SystemAuthAuthorizeComponent.sendForm(e);});

    document.querySelector(".regform").addEventListener("submit", function(e) {SystemAuthAuthorizeComponent.sendRegForm(e);});

    document.querySelector(".activateRegForm").addEventListener("click", function() {SystemAuthAuthorizeComponent.activateRegForm();});

    document.querySelector(".activateAutForm").addEventListener("click", function() {SystemAuthAuthorizeComponent.activateAutForm();});
});

class SystemAuthAuthorizeComponent {
    static resizeBg() {
        var size = 0;
        if (window.outerHeight > window.innerWidth) {
            size = window.outerHeight;
        } else {
            size = window.innerWidth;
        }
        var backelement = document.querySelector('.authbackground');
        backelement.style.height = (size * 2) + 'px';
        backelement.style.width = (size * 2) + 'px';
        backelement.style.left = (((size * 2) - window.innerWidth) / 2 * -1) + 'px';
        backelement.style.top = (((size * 2) - window.innerHeight) / 2 * -1) + 'px';
    }

    static sendForm(e) {
        e.preventDefault();
        if (!e.target.querySelector('input[type="submit"]').classList.contains('blocked')) {
            document.querySelector('.authscreen_errors .container').innerHTML = '';
            document.querySelector('.authscreen_errors').classList.remove('active');

            //Check fields
            var login = e.target.querySelector('[name="login"]').value;
            var password = e.target.querySelector('[name="password"]').value;
            if (login != "" && password != "") {
                // Success
                e.target.querySelector('input[type="submit"]').classList.add('blocked');
                document.body.classList.remove('loading');
                setTimeout(function () {
                    document.body.classList.add('loading');
                }, 1);

                //Ajax
                var form_data = {
                    login: login,
                    password: password
                }

                var that = e.target;
                var obj = this;
                axios.post('/ajax/system-auth-authorize/', qs.stringify(form_data))
                    .then(function (response) {
                        if(response.data.RESULT){
                            //Есть результат
                            if(response.data.RESULT == 'success'){
                                setTimeout(function(){
                                    window.location.reload();
                                }, 1500);
                            } else {
                                obj.doException(response.data.RESULT);
                                that.querySelector('input[type="submit"]').classList.remove('blocked');
                            }
                        } else {
                            //Подсунули говно
                            obj.doException(response.data.ERROR);
                            that.querySelector('input[type="submit"]').classList.remove('blocked');
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            } else {
                if (login == "") {
                    this.doException('Вы не заполнили поле Логин');
                }
                if (password == "") {
                    this.doException('Вы не заполнили поле Пароль');
                }
            }
        }
    }

    static sendRegForm(e) {
        e.preventDefault();
        if (!e.target.querySelector('input[type="submit"]').classList.contains('blocked')) {
            document.querySelector('.authscreen_errors .container').innerHTML = '';
            document.querySelector('.authscreen_errors').classList.remove('active');

            //Check fields
            var login = e.target.querySelector('[name="email"]').value;
            var password = e.target.querySelector('[name="password"]').value;
            var password_repeat = e.target.querySelector('[name="password_repeat"]').value;
            var errors = 0;
            if(login == ""){
                this.doException('Вы не заполнили поле "E-mail адрес"');
                errors = errors + 1;
            }
            if(/^[-\w.]+@([A-z0-9][-A-z0-9]+\.)+[A-z]{2,4}$/.test(login) == false){
                this.doException('Поле "E-mail адрес" заполнено некорректно');
                errors = errors + 1;
            }
            if(password == "" || password.length < 8){
                this.doException('Вы не заполнили поле "Пароль", оно должно содержать минимум 8 символов');
                errors = errors + 1;
            }
            if(password_repeat == "" || password_repeat.length < 8){
                this.doException('Вы не заполнили поле "Повторите пароль", оно должно содержать минимум 8 символов');
                errors = errors + 1;
            }
            if(password != password_repeat){
                this.doException('Пароли не совпадают');
                errors = errors + 1;
            }
            if(errors == 0){
                e.target.querySelector('input[type="submit"]').classList.add('blocked');
                document.body.classList.remove('loading');
                setTimeout(function () {
                    document.body.classList.add('loading');
                }, 1);

                //Ajax
                var form_data = {
                    login: login,
                    password: password
                }
                var that = e.target;
                var obj = this;
                axios.post('/ajax/system-auth-register/', qs.stringify(form_data))
                    .then(function (response) {
                        if(response.data.RESULT){
                            //Есть результат
                            if(response.data.RESULT == 'success'){
                                setTimeout(function(){
                                    window.location.reload();
                                }, 1500);
                            } else {
                                obj.doException(response.data.RESULT);
                                that.querySelector('input[type="submit"]').classList.remove('blocked');
                            }
                        } else {
                            //Подсунули говно
                            obj.doException(response.data.ERROR);
                            that.querySelector('input[type="submit"]').classList.remove('blocked');
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            } else {
                e.target.querySelector('input[type="submit"]').classList.remove('blocked');
            }
        }
    }

    static doException(text) {
        var errorNode = document.createElement('p');
        errorNode.innerHTML = text;
        document.querySelector('.authscreen_errors .container').appendChild(errorNode);
        document.querySelector('.authscreen_errors').classList.add('active');
    }

    static activateRegForm() {
        document.querySelector(".authform").style.display = "none";
        document.querySelector(".regform").style.display = "block";
    }

    static activateAutForm() {
        document.querySelector(".authform").style.display = "block";
        document.querySelector(".regform").style.display = "none";
    }
}
