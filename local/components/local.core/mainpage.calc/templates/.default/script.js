$(function(){
    var vals = $('.tarifsblock').attr('data-values').split(',');

    $('.calculator .range').ionRangeSlider({
        values: vals,
        skin: "big",
        grid: true,
        grid_num: 10,
        from: $('.tarifsblock').attr('data-start')
    });

    //Добавление магазина
    $('.calculator .addshop span').bind('click', function(){
        var newelem = $('.calculator .shops').append($('.calcsources .shoprow').clone());
        $(newelem).find('.range').ionRangeSlider({
            values: vals,
            skin: "big",
            grid: true,
            grid_num: 10,
            from: $('.tarifsblock').attr('data-start')
        });
        $('.calculator .shoprow:last-child').find('input[type="checkbox"]').each(function(){
            var id = $(this).attr('id');
            var idarray = $(this).attr('id').split('-');
            $(this).attr('id', idarray[0]+'-'+$('.shops .shoprow').length);
            $(this).siblings('label[for="'+id+'"]').attr('for', idarray[0]+'-'+$('.shops .shoprow').length);
        })
    });

    //Расчеты
    function calculate(){
        $('.calculator .shoprow').each(function(){
            var products = $(this).find('input.range').val();
            var feeds = $(this).find('input[type="checkbox"]:checked').length;
            $(this).find('.tarifswrap').html($('.calcsources .tarif[data-tarif="'+products+'"]').clone());
            var oldprice = parseInt($(this).find('.tarifswrap p.price.old').attr('data-price')) * feeds;
            $(this).find('.tarifswrap p.price.old span').text(oldprice.toLocaleString('ru'));
            var price = parseInt($(this).find('.tarifswrap p.price:not(.old)').attr('data-price')) * feeds;
            $(this).find('.tarifswrap p.price:not(.old) span').text(price.toLocaleString('ru'));
        });
    }


    $(document).on('change', '.calculator .shop input[type="checkbox"], .calculator .shop input[type="text"]', function(){
        calculate();
    });
})