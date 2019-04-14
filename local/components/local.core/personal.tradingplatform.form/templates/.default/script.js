class LocalCoreTradingPlatform {
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
                console.log(response.data);
                if( response.data['ROW_HTML'] )
                {
                    var RowObj = document.querySelector('[id="'+idRow+'"]'),
                        responseHtml = document.createElement('div');

                    responseHtml.innerHTML = response.data['ROW_HTML'];
                    RowObj.innerHTML = responseHtml.querySelector('[id="'+idRow+'"]').innerHTML;
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

    }

    /**
     * Удаляет указанный btn-group при множественном значении
     *
     * @param obj
     */
    static removeMultipleRow(obj)
    {
        console.log(obj.parentElement.parentElement.remove());
    }
}
