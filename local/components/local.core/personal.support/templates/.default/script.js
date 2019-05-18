class PersonalSupportComponent {

    static sendMessage()
    {
        var classObj = this,
            strMessage = document.querySelector('[data-support-message]').value;

        try {

            if( strMessage.length < 1 )
            {
                throw new Error('Для отправки сообщения необходимо ввести текст.');
            }

            axios.post('/ajax/support/add-message/', qs.stringify({
                'SUPPORT_ID' : this.getSupportId(),
                'MSG': strMessage
            }))
                .then(function (response) {
                    switch (response.data.result) {
                        case 'success':
                                location.href = ( classObj.getSupportListUrl()+response.data['SUPPORT_ID']+'/' );
                            break;

                        case 'error':
                            swal({
                                type: 'error',
                                html: response.data['error_text']
                            });
                            break;
                    }
                });

        }
        catch(e)
        {
            swal({
                type: 'error',
                html: e.message
            });
        }
    }

    static closeTask()
    {
        var classObj = this;

        try {

            axios.post('/ajax/support/close-task/', qs.stringify({
                'SUPPORT_ID' : this.getSupportId(),
            }))
                .then(function (response) {
                    switch (response.data.result) {
                        case 'success':
                            location.reload();
                            break;

                        case 'error':
                            swal({
                                type: 'error',
                                html: response.data['error_text']
                            });
                            break;
                    }
                });

        }
        catch(e)
        {
            swal({
                type: 'error',
                html: e.message
            });
        }
    }


    static setSupportId(intId)
    {
        this._supportId = intId;
    }
    static getSupportId()
    {
        return this._supportId;
    }

    static setSupportListUrl(str)
    {
        this._supportListUrl = str;
    }
    static getSupportListUrl()
    {
       return this._supportListUrl;
    }
}