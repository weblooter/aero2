class PersonalTradingplatformFormComponent {

    /**
     * Возвращает ID формы
     * @return {string}
     */
    static getFormId() {
        return 'tradingplatformform';
    }

    static showLoading()
    {
        document.querySelector('#'+this.getFormId()+' .page-loader').classList.add('d-block');
    }

    static hideLoading()
    {
        document.querySelector('#'+this.getFormId()+' .page-loader').classList.remove('d-block');
    }

    /* **************** */
    /* AJAX AND REFRESH */
    /* **************** */

    /**
     * Собирает значения формы
     *
     * @return {object}
     */
    static getFormData() {
        var classObj = this,
            formData = new FormData(document.forms[this.getFormId()]),
            result = {};

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
        var classObj = this,
            formdata = this.getFormData();

        formdata.LOCAL_CORE_REFRESH_ROW = idRow;

        classObj.showLoading();

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
                classObj.hideLoading();
            })
            .catch(function (error) {
                console.log(error);

                classObj.hideLoading();
            });
    }

    /**
     * Обновить всю форму
     */
    static refreshForm() {
        var classObj = this,
            formdata = this.getFormData();

        classObj.showLoading();

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
                    classObj.hideLoading();
                }
            })
            .catch(function (error) {
                console.log(error);
                classObj.hideLoading();
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

    /* ******** */
    /* TAXONOMY */
    /* ******** */
    static toggleDisplayBlockTaxonomy(idRow)
    {
        var classObj = this;

        classObj.showLoading();

        setTimeout(function () {
            try {

                var taxonomyRowObj = document.querySelector('#'+classObj.getFormId()+' [id="'+idRow+'"]'),
                    boolNeedHideNotEmpty = taxonomyRowObj.querySelector('[data-taxonomy-hide]').checked;

                if( taxonomyRowObj.querySelectorAll('[data-taxonomyRowWrapper]').length > 0 )
                {
                    for (var i = 0; i < taxonomyRowObj.querySelectorAll('[data-taxonomyRowWrapper]').length; i++)
                    {
                        var taxonomyRowWrapperObj = taxonomyRowObj.querySelectorAll('[data-taxonomyRowWrapper]')[i];

                        if( taxonomyRowWrapperObj.querySelectorAll('select[name]').length > 0 )
                        {
                            switch ( ( taxonomyRowWrapperObj.querySelector('select[name]').value != '' ) ) {
                                case true:
                                    if( boolNeedHideNotEmpty )
                                    {
                                        taxonomyRowWrapperObj.classList.add('d-none');
                                    }
                                    else
                                    {
                                        taxonomyRowWrapperObj.classList.remove('d-none');
                                    }
                                    break;

                                case false:
                                    taxonomyRowWrapperObj.classList.remove('d-none');
                                    break;
                            }
                        }
                    }
                }

                classObj.hideLoading();
            }
            catch (e) {
                classObj.hideLoading();
            }
        }, 1);
    }
}
