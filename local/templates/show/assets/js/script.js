$(function(){
    $('select').selectpicker();

    
    //Боковое меню
    if($('.leftmenu').length > 0){
        $(".leftmenu").stick_in_parent({
            offset_top: 100
        });
    }
    
    //Мобильное меню 
    $('.mobilemenu_toggle').bind('click', function(){
       $('body').toggleClass('mobilemenu'); 
       $('.mainmenu').slideToggle(200); 
    });
    

    $(window).scroll(function() {
      if ($(window).scrollTop() > 0) {
        $('header').addClass('fixed');
      } else {
        $('header').removeClass('fixed');
      }
    });
    
    $('.question').bind('click', function(){
        $(this).toggleClass('active').next('.answer').slideToggle(200);
    });

    //Слайдер в первом блоке на главной (планшет)
    if($(window).width() > 991){
        if($('.firstslide .triple').hasClass('slick-initialized')){
            $('.firstslide .triple').slick('unslick');
        }
    } else {
        if($('.firstslide .triple').hasClass('slick-initialized') == false){
            $('.firstslide .triple').slick({
                dots: true,
                arrows: false,
                slidesToShow: 2,
                autoplay: true,
                responsive: [
                    {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 1,
                    }
                    }
                ]
            });
        }
    }
    
    $('.authscreen').bind('submit', function(){
        $('body').removeClass('loading');
        setTimeout(function(){
            $('body').addClass('loading');
        }, 1)
    })
})

//Слайдер в первом блоке на главной (планшет)
$(window).resize(function(){
    if($(window).width() > 991){
        if($('.firstslide .triple').hasClass('slick-initialized')){
            $('.firstslide .triple').slick('unslick');
        }
    } else {
        if($('.firstslide .triple').hasClass('slick-initialized') == false){
            $('.firstslide .triple').slick({
                dots: true,
                arrows: false,
                slidesToShow: 2,
                autoplay: true,
                responsive: [
                    {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 1,
                    }
                    }
                ]
            });
        }
    }
});