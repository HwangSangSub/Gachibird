$(document).ready(function(){

    // gnb 설정
    // line 초기 위치 설정
    var initialPos = $('.header_gnb li.selected').position().left;
    var initialWidth = $('.header_gnb li.selected').outerWidth();
    var leftMargin = 2.5 * parseFloat($("body").css("font-size"));
    $('.gnb_line').css("left", initialPos + leftMargin + "px").css("width", initialWidth + "px");
    
    $('.header_gnb li').on("click", function(){
        $('.mobile_view').stop().animate({scrollTop : 0}, 500);
        $('.header_gnb li').removeClass("selected");
        $(this).addClass("selected");

        // line 업데이트
        var leftPos = $(this).position().left;
        var width = $(this).outerWidth();
        $('.gnb_line').css("left", leftPos + leftMargin + "px").css("width", width + "px");
    });


    $('.under_menu ul li').click(function(){
        $('.under_menu li').removeClass('on')
        $(this).addClass('on');

        let i = $(this).index();
        if(i==1){
            $('.mybird_gnb').css({'display' : 'block'});
            $('.mobile_view').css({'background-color' : 'var(--color-gray-background)'});

            $('.header_title').text('오늘의 소식을 모아봤어요');
            $('.header_gnb li').eq(0).click();
        }
        else{
            $('.mybird_gnb').css({'display' : 'none'});
            $('.mobile_view').css({'background-color' : 'var(--white-000)'});

            $('.header_title').text('요런 콘텐츠는 어때요?');
        }
    })


    // mainContent 로딩
    async function includeHTML(url, targetSelector) {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Failed to fetch ${url}. Status: ${response.status}`);
            }
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
    
            const targetElement = document.querySelector(targetSelector);
            targetElement.innerHTML = doc.body.innerHTML;
        } catch (error) {
            console.error('Error loading content:', error);
        }
    }
    
    // recom.html을 로드하는 이벤트 핸들러
    $('.under_menu ul li').eq(0).click(async function() {
        const urlToInclude = 'common/index/main_recom.html';
        const targetSelector = '#mainContent';
        await includeHTML(urlToInclude, targetSelector);
    });
    // mybird.html을 로드하는 이벤트 핸들러
    $('.under_menu ul li').eq(1).click(async function() {
        const urlToInclude = 'common/index/main_mybird.html';
        const targetSelector = '#mainContent';
        await includeHTML(urlToInclude, targetSelector);
    });
    // recom.html을 로드하는 이벤트 핸들러
    $('.under_menu ul li').eq(2).click(async function() {
        const urlToInclude = 'common/index/main_mykey.html';
        const targetSelector = '#mainContent';
        await includeHTML(urlToInclude, targetSelector);
    });

    // 로딩 시 main_recom.html을 로드
    const initialUrlToInclude = 'common/index/main_recom.html';
    const initialTargetSelector = '#mainContent';
    includeHTML(initialUrlToInclude, initialTargetSelector);








})