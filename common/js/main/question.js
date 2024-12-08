$(document).ready(function(){
    $('.gnb li').click(function(){
        $('.gnb li').removeClass('blue');
        $(this).addClass('blue');
        getQuestion(this);
    })
    $('.gnb li').eq(0).trigger('click');
   
    
    $('.que_box').click(function() {
        var ch = $(this).children();
    
        if ($(this).hasClass('active')) {
            $('.que_box').css({'transition': 'all 0s'})
            $('.que_text').css({'transition': 'all 0s'})
            
            $(this).removeClass('active');
            $(ch).children('.pluse').css({'display': 'block'});
            $(ch).children('.minus').css({'display': 'none'});
        }
        else {
            $('.que_box').css({'transition': 'all 0.2s'})
            $('.que_text').css({'transition': 'all 0.5s'})

            $(this).addClass('active');
            $(ch).children('.pluse').css({'display': 'none'});
            $(ch).children('.minus').css({'display': 'block'});
        }
    });


    $('.buger').click(function(){
        $(this).css({'display':'none'})
        $('.cross').css({'display':'block'})
        $('.sub_menu').css({'display':'block'})
    })
    $('.cross').click(function(){
        $(this).css({'display':'none'})
        $('.buger').css({'display':'block'})
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




function getQuestion(t) {
    let id = $(t).attr('id');
    let url = '../../../index/qna.php?' + id;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.result) {
                let blc = $('.blc').text("");
                let qna = data.qna;
                
                qna.forEach(e => {
                    let title = $("<p></p>").text(e.b_Title);
                    let img_pluse = $('<img class="pluse" src="../common/img/main/question/icon_pluse.svg" alt="">');
                    let img_minus = $('<img class="minus" src="../common/img/main/question/icon_minus.svg" alt="">');
                    let que_title = $('<div class="que_title"></div>');
                    let que_text = $('<div class="que_text"></div>');
                    let b_Content = $(e.b_Content);
                    let que_box = $('<div class="que_box"></div>');
                    
                    que_title.append(title);
                    que_title.append(img_pluse);
                    que_title.append(img_minus);

                    que_text.append(b_Content);
                    
                    que_box.append(que_title);
                    que_box.append(que_text);
                    
                    blc.append(que_box);
                });

                $('.que_box').click(function() {
                    var ch = $(this).children();
                
                    if ($(this).hasClass('active')) {
                        $('.que_box').css({'transition': 'all 0s'})
                        $('.que_text').css({'transition': 'all 0s'})
                        
                        $(this).removeClass('active');
                        $(ch).children('.pluse').css({'display': 'block'});
                        $(ch).children('.minus').css({'display': 'none'});
                    }
                    else {
                        $('.que_box').css({'transition': 'all 0.2s'})
                        $('.que_text').css({'transition': 'all 0.5s'})
            
                        $(this).addClass('active');
                        $(ch).children('.pluse').css({'display': 'none'});
                        $(ch).children('.minus').css({'display': 'block'});
                    }
                });
            }
        }
    });
}