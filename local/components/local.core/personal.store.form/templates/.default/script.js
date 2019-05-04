class PersonalStoreFormComponent {
    static changeSource()
    {
        var strSourceValue = document.querySelector('#personalstoreformcomponent [data-source-value]').value,
            obSourceBlockLink = document.querySelector('#personalstoreformcomponent [data-source="LINK"]'),
            obSourceBlockFile = document.querySelector('#personalstoreformcomponent [data-source="FILE"]');

        switch (strSourceValue) {
            case 'FILE':
                obSourceBlockLink.classList.add('d-none');
                obSourceBlockFile.classList.remove('d-none');
                break;
            case 'LINK':
                obSourceBlockLink.classList.remove('d-none');
                obSourceBlockFile.classList.add('d-none');
                break;
        }
    }

    static changeLinkAccess()
    {
        var boolAccessValue = document.querySelector('#personalstoreformcomponent [data-need-access-value]').checked,
            obAccessBlock = document.querySelector('#personalstoreformcomponent [data-need-access]');

        if( boolAccessValue )
        {
            obAccessBlock.classList.remove('d-none');
        }
        else
        {
            obAccessBlock.classList.add('d-none');
        }
    }

    static changeFile()
    {
        // STORE_FIELD[FILE]
        var arFile = document.querySelector('#personalstoreformcomponent [name="STORE_FIELD[FILE]"]').files,
            maxFileSize = document.querySelector('#personalstoreformcomponent [name="STORE_FIELD[FILE]"]').getAttribute('data-max-size') - 0;

        console.log(arFile[0].size);
        console.log(maxFileSize);

        if( ( (arFile[0].size-0)/1000/1000 ) >= maxFileSize )
        {
            swal({
                title: 'Файл слишком большой!',
                text: 'Максимальный разрешенный размер загружаемого файла - '+maxFileSize+' Мб. Загрузите другой файл.',
                type: 'info'
            })
        }
        else
        {
            document.querySelector('#personalstoreformcomponent [data-file-title]').innerText = arFile[0].name;
        }
    }

    static init()
    {
        this.changeSource();
        this.changeLinkAccess();
    }
}