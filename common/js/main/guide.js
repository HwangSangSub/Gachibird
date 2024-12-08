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


    function updateNavPosition() {
        var windowWidth = $(window).width();
        if (windowWidth >= 801) {
            var winhalf = windowWidth / 2 - 401.5;
            $('.gnb').css({'left': winhalf});
        }
        else{
            var mobileWidth = $('.art_img .mobile').width();
            $('.art_section01 .art_img img:nth-child(1)').css({'margin-right': -mobileWidth / 2 + 'px'});
            $('.art_section05 .art_img img:nth-child(1)').css({'margin-right': -mobileWidth / 2 + 'px'});
            $('.art_section07 .art_img img:nth-child(2)').css({'margin-right': -mobileWidth / 2 + 'px'});

            $('.together01 .art_img img:nth-child(1)').css({'margin-right': -mobileWidth / 2 + 'px'});
            $('.together02 .art_img img:nth-child(1)').css({'margin-right': -mobileWidth / 2 + 'px'});
            $('.together03 .art_img img:nth-child(2)').css({'margin-right': -mobileWidth / 2 + 'px'});
            $('.together04 .art_img img:nth-child(1)').css({'margin-right': -mobileWidth / 2 + 'px'});
            $('.together05 .art_img img:nth-child(1)').css({'margin-right': -mobileWidth / 2 + 'px'});
            $('.together06 .art_img img:nth-child(2)').css({'margin-right': -mobileWidth / 2 + 'px'});
        }
    }
    updateNavPosition(); 
    $(window).on('resize', function () {
        updateNavPosition();
    });
    

    $('.box_btn').click(function(){
        var $this = $(this);
        if ($this.text() === '닫기') {
            $this.text('더보기');
            $this.parent('.non_blur').siblings('.blur').css({'filter':'none'});
            $this.siblings('.box_text').css({'opacity':'0', 'transform':'translateY(20px)' })
        } else {
            $this.text('닫기');
            $this.parent('.non_blur').siblings('.blur').css({'filter':'blur(25px)'});
            $this.siblings('.box_text').css({'opacity':'1', 'transform':'translateY(0)' })
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











    



})