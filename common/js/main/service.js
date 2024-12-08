$(document).ready(function () {

    // header 숨기기 효과
    let prevScrollPos = window.pageYOffset;
    window.onscroll = function () {
        const currentScrollPos = window.pageYOffset;
        if (prevScrollPos > currentScrollPos) {
            document.querySelector("header").style.top = "0";
        } else {
            document.querySelector("header").style.top = "-60px";
            $('.cross').trigger('click');
        }
        prevScrollPos = currentScrollPos;
    };


    // header 변경 효과
    let plx = $('.parallax');
    let plxh = plx.height();
    let plxh3 = plxh * 0.3;
    $(window).scroll(function () {
        let sc = $(this).scrollTop();
        if (sc > plxh * 3 + plxh3 * 2 + 2000) {
            $('header').css({ 'background-color': '#FFFFFFB3', 'border-bottom': '' })
            $('nav ul li a').css({ 'color': '#2B3E48' })
            $(".menu h1 img").attr("src", "../common/img/main/logo_2.png");
            $(".google").attr("src", "../common/img/main/button01_2(playstore).svg");
            $(".apple").attr("src", "../common/img/main/button02_2(appstore).svg");
            $('.menu_btn img').css({'filter':'invert(100%) sepia(100%) saturate(0%) hue-rotate(170deg) brightness(103%) contrast(107%)'})
        
            $('.sub_menu li').css({'background-color':'#FFFFFF99'})
            $('.sub_menu a li').css({'color':'#2B3E48'})
        }
        else {
            $('header').css({ 'background-color': '#191F28', 'border-bottom': '' })
            $('nav ul li a').css({ 'color': '' })
            $(".menu h1 img").attr("src", "../common/img/main/logo.png");
            $(".google").attr("src", "../common/img/main/button01_(playstore).svg");
            $(".apple").attr("src", "../common/img/main/button02_(appstore).svg");
            $('.menu_btn img').css({'filter':''})

            $('.sub_menu li').css({'background-color':''})
            $('.sub_menu a li').css({'color':''})
        }
    });

    

    // 애니메이션 조정
    var ani01 = $(".rot01");
    var ani02 = $(".rot02");
    var ani03 = $(".rot03");
    var ani04 = $(".rot04");
    $(window).scroll(function () {
        var sc = $(this).scrollTop();

        if (sc > 0) {
            ani01.removeClass("rot01");
            ani02.removeClass("rot02");
            ani03.removeClass("rot03");
            ani04.removeClass("rot04");
        }
        else {
            ani01.addClass("rot01");
            ani02.addClass("rot02");
            ani03.addClass("rot03");
            ani04.addClass("rot04");
        }
    });



    // section04 높이 설정
    // $('.section04').each(function () {
    //     var totalHeight = 0;
    //     $(this).find('article').each(function () {
    //         totalHeight += $(this).outerHeight(true);
    //     });
    //     $(this).height(totalHeight);
    // });





    
    // 버튼 클릭시 각 콘텐츠 이동
    $('.ser_01, .ser_02, .ser_03, .ser_04').click(function (e) {
        var targetClass = $(this).attr('class').split('_')[1];
        var targetOffset = $('.move' + targetClass).offset().top - 40;
        $('html, body').animate({
            scrollTop: targetOffset
        }, 1000);
    });


    
    $('.burger').click(function(){
        $(this).css({'display':'none'})
        $('.cross').css({'display':'block'})
        $('.sub_menu').css({'display':'block'})
    })
    $('.cross').click(function(){
        $(this).css({'display':'none'})
        $('.burger').css({'display':'block'})
        $('.sub_menu').css({'display':'none'})
    })
    
    function checkScreenWidth() {
      var windowWidth = $(window).width();
      if (windowWidth >= 801) {
          $('.cross').trigger('click');
      }


    }
    checkScreenWidth();
    $(window).resize(function() {
        checkScreenWidth();
    });




    let i = 0;
    $('.right').click(function(){
        i++;
        if (i > 3) i = 3;
        if (i < 4) {
            let translateValue = -100 * i + '%';
            $('.secvice_img div').css('transform', 'translateX(' + translateValue + ')');
        }

        $('.section04 article').removeClass('mq_block')
        $('.section04 article').eq(i+1).addClass('mq_block')
        
        $('.photo_index>div').removeClass('color')
        $('.photo_index>div').eq(i).addClass('color')
    });

    $('.left').click(function(){
        i--;
        if (i < 0) i = 0;
        if (i < 4) {
            let translateValue = -100 * i + '%';
            $('.secvice_img div').css('transform', 'translateX(' + translateValue + ')');
        }

        $('.section04 article').removeClass('mq_block')
        $('.section04 article').eq(i+1).addClass('mq_block')

        $('.photo_index>div').removeClass('color')
        $('.photo_index>div').eq(i).addClass('color')
    });
    
    
    function checkScreenSize() {
        if ($(window).width() >= 801) {
            for (let i = 0; i < 3; i++) {
                $('.left').trigger('click');
            }
        }
    }
    $(window).on('resize', checkScreenSize);
    checkScreenSize();


})