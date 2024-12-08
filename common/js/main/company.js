$(document).ready(function(){


    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        // 모바일 기기에서 열린 경우

    } else {
        // 데스크톱 브라우저에서 열린 경우
        var delay = 300;
        var timer = null; 
        $(window).on('resize', function(){
            clearTimeout(timer);
            timer = setTimeout(function(){
            document.location.reload();
            }, delay);
        });
    }




    // 화면 높이 조정
    function setVhProperty() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
      } 
      window.addEventListener('DOMContentLoaded', setVhProperty);
      window.addEventListener('resize', setVhProperty);
      let previousHeight = window.innerHeight;
      function handleResize() {
        const currentHeight = window.innerHeight;
        if (previousHeight !== currentHeight) {
          previousHeight = currentHeight;
        }
      }
      handleResize();
      window.addEventListener('resize', handleResize);
      



    let mh = $('.section1').height();
    $(window).scroll(function(){
        let sc = $(this).scrollTop();

        if (sc > mh*2 + 6000) {
            $('header').css({'background-color':'#191F28', 'border-bottom':''})
            $('nav ul li a').css({'color':'#F2F6F8'})
            $(".menu h1 img").attr("src", "../common/img/main/logo.png");
            $(".google").attr("src", "../common/img/main/button01_(playstore).svg");
            $(".apple").attr("src", "../common/img/main/button02_(appstore).svg");
            
            $('.sub_menu li').css({'background-color':'#191F28'})
            $('.sub_menu a li').css({'color':'#F2F6F8'})
            $('.menu_btn img').css({'filter':'none'})
        }
        else{
            $('header').css({'background-color':'#FFFFFFB3', 'border-bottom':''})
            $('nav ul li a').css({'color':'#2B3E48'})
            $(".menu h1 img").attr("src", "../common/img/main/logo_2.png");
            $(".google").attr("src", "../common/img/main/button01_2(playstore).svg");
            $(".apple").attr("src", "../common/img/main/button02_2(appstore).svg");

            $('.sub_menu li').css({'background-color':''})
            $('.sub_menu a li').css({'color':''})
            $('.menu_btn img').css({'filter':'invert(100%) sepia(100%) saturate(0%) hue-rotate(170deg) brightness(103%) contrast(107%)'})
        }
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
    if ($(window).width() <= 800) {
        $(".br01 a br:first, .br01 a br:eq(2)").remove();
        $(".br02 a br:first").remove();
        $(".br03 a br:first, .br03 a br:eq(2)").remove();
    }

    
    

    // news 드레그
    var isclick = false;
    var standard = 0;
    var moving = 0;
    var origin =0;

    $('.news_list').on({
        "touchstart mousedown" : function(e_m){
            isclick = true;
            origin = $(this).offset().left;
        },
        "touchstart" : function(e_m){
            standard = e_m.originalEvent.touches[0].screenX;
        },
        "mousedown" : function(e_m){
            standard = e_m.pageX
        },
        "touchmove" : function(e_m){
            e_m.preventDefault();
            if(isclick){
                moving = e_m.originalEvent.touches[0].screenX - standard;
                $(this).css('transform','translateX('+(origin+moving)+'px)');
            }
        },
        "mousemove" : function(e_m){
            if(isclick){
                moving = e_m.pageX - standard;
                $(this).css('transform','translateX('+(origin+moving)+'px)');
            }
        },
        "touchend mouseup mouseout" : function(e_m){
            isclick = false
            if($(this).offset().left > 0){
                $(this).css('transform','translateX(0px)');
            }
            else if ($(this).offset().left < -640) {
                $(this).css('transform', 'translateX(-640px)');
            }
        }
    })







})