class PersonalTradingplatformFormComponent {

    /* **************** */
    /* AJAX AND REFRESH */
    /* **************** */

    /**
     * Возвращает ID формы
     * @return {string}
     */
    static getFormId() {
        return 'tradingplatformform';
    }

    /**
     * Собирает значения формы
     *
     * @return {object}
     */
    static getFormData() {
        var formData = new FormData(document.forms[this.getFormId()]);
        var result = {};
        formData.forEach(function (value, key) {
            if (/(\[\]$)/g.test(key)) {
                key = key.replace(/(\[\]$)/g, '');

                if (typeof (result[key]) == 'undefined')
                    result[key] = [];

                result[key].push(value)
            } else {
                result[key] = value;
            }
        });
        return result;
    }

    /**
     * Обновить блок, приминив значения формы
     *
     * @param idRow Хэш блока
     */
    static refreshRow(idRow) {
        var formdata = this.getFormData();
        formdata.LOCAL_CORE_REFRESH_ROW = idRow;

        axios.post('/ajax/trading-platform-form/refresh-row/', qs.stringify(formdata))
            .then(function (response) {
                if (response.data['ROW_HTML']) {
                    var RowObj = document.querySelector('[id="' + idRow + '"]'),
                        responseHtml = document.createElement('div');

                    responseHtml.innerHTML = response.data['ROW_HTML'];
                    RowObj.innerHTML = responseHtml.querySelector('[id="' + idRow + '"]').innerHTML;

                    if (responseHtml.querySelectorAll('script').length > 0) {
                        for (var i = 0; i < responseHtml.querySelectorAll('script').length; i++) {
                            eval(responseHtml.querySelectorAll('script')[i].innerText);
                        }
                    }
                    LocalCore.initFormComponents();
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    /**
     * Обновить всю форму
     */
    static refreshForm() {
        var formdata = this.getFormData(),
            classObj = this;

        axios.post('/ajax/trading-platform-form/refresh-form/', qs.stringify(formdata))
            .then(function (response) {
                if (response.data['FORM_HTML']) {
                    var responseHtml = document.createElement('div');

                    responseHtml.innerHTML = response.data['FORM_HTML'];
                    document.querySelector('form[id="'+classObj.getFormId()+'"] [data-handler-fields]').innerHTML = responseHtml.innerHTML;

                    if (responseHtml.querySelectorAll('script').length > 0) {
                        for (var i = 0; i < responseHtml.querySelectorAll('script').length; i++) {
                            eval(responseHtml.querySelectorAll('script')[i].innerText);
                        }
                    }
                    LocalCore.initFormComponents();
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    /* ************** */
    /* MULTIPLE INPUT */

    /* ************** */

    /**
     * Удаляет указанный btn-group при множественном значении
     *
     * @param obj
     */
    static removeMultipleRow(obj) {
        console.log(obj.parentElement.parentElement.remove());
    }

    /* ******* */
    /* BUILDER */
    /* ******* */

    /**
     * Задатет значения билдера для реплейса в хранилище
     * @param obj
     */
    static setBuilderOptions(obj) {
        this._objBuilderOptions = obj;
    }

    /**
     * Извлекает значения билдера из регистра
     *
     * @return {object}
     */
    static getBuilderOptions() {
        return this._objBuilderOptions;
    }

    /**
     * Добавляет изначение из билдера в инпут
     *
     * @param strVal
     * @param inputName
     */
    static addBuilderValueToInput(strVal, inputName) {
        document.querySelector('[name="' + inputName + '"]').value = (document.querySelector('[name="' + inputName + '"]').value + strVal + ' ');
        document.querySelector('[name="' + inputName + '"]').dispatchEvent(new Event('keyup'));
    }

    /**
     * Обновляет текст поля билдера
     *
     * @param objInput
     */
    static replaceBuilderSmallString(objInput) {
        var InputVal = objInput.value,
            matches = InputVal.match(/{{([\#\-\_\|A-Za-z0-9]+)}}/g),
            obClass = this,
            smallTextHash = objInput.getAttribute('data-small-text-hash');

        if (matches instanceof Array) {
            matches.forEach(function (v, k) {
                var realV = v.replace(/({|})/g, ''),
                    newV = '';
                if (typeof obClass.getBuilderOptions()[realV] == "string") {
                    newV = '{{' + obClass.getBuilderOptions()[realV] + '}}';
                }
                InputVal = InputVal.replace(v, newV);
            });
        }
        document.querySelector('[data-small-text-id="' + smallTextHash + '"]').innerText = InputVal;
    }

    /* ***** */
    /* LOGIC */
    /* ***** */

    static changeLogicFieldValue(val, text, strDropdownHash) {
        document.querySelector('[data-dropdown-hash-id="' + strDropdownHash + '"] [type="hidden"]').value = val;
        document.querySelector('[data-dropdown-hash-id="' + strDropdownHash + '"] .local-core-dropdown-title').innerText = text;
    }
}
