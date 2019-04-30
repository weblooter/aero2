class PersonalTradingplatformListComponent {
    static deleteTP(intTpId)
    {
        if (confirm('При удалении торговой площадки денежные средства за оплаченный период не возвращаются на счет. Желаете удалить?')) {
            axios.post('/ajax/trading-platform/delete/' + intTpId + '/')
                .then(function (response) {
                    if (response.data.result == 'SUCCESS') {
                        alert('OK!');
                    } else {
                        alert(response.data['error_text'])
                    }
                })
        }
    }

    static activateTP(intTpId)
    {
        if (confirm('При активации торговой площадки со счета произойдет списание денежных средств за месяц согласно тарифу данного магазина, если торговая площадка не была оплачена ранее или срок ее оплаты истек. Желаете активировать?')) {
            axios.post('/ajax/trading-platform/activate/' + intTpId + '/')
                .then(function (response) {
                    if (response.data.result == 'SUCCESS') {
                        alert('OK!');
                    } else {
                        alert(response.data['error_text'])
                    }
                })
        }
    }

    static deactivateTP(intTpId)
    {
        if (confirm('При деактивации торговой площадки оплаченный период не замораживается, а потраченные денежные средства не возвращаются на счет. Так же у деактивированной торговой площадки перестает обновляться файл экспорта. Желаете деактивировать?')) {
            axios.post('/ajax/trading-platform/deactivate/' + intTpId + '/')
                .then(function (response) {
                    if (response.data.result == 'SUCCESS') {
                        alert('OK!');
                    } else {
                        alert(response.data['error_text'])
                    }
                })
        }
    }
}