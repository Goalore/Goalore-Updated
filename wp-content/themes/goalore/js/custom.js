/*$( document ).ready(function() {
   
   $('.open-form').click(function(){
    // $(this).toggleClass('open');
    $('.newmc-form-sec').addClass('open');
    $('.newmc-form-sec').removeClass('close');
    $('.milestone-challenges-sec').addClass('close');
    $('.milestone-challenges-sec').removeClass('open');
  	});

   $('.back-btn').click(function(){
    // $(this).toggleClass('open');
    $('.newmc-form-sec').addClass('close');
    $('.newmc-form-sec').removeClass('open');
    $('.milestone-challenges-sec').addClass('open');
    $('.milestone-challenges-sec').removeClass('close');
  	});

});
*/

(function($){
    $(window).on("load",function(){
        $(".gib-scroll").mCustomScrollbar();
    });
})(jQuery);


function openNav() {
  document.getElementById("myNav").style.height = "100%";
}

function closeNav() {
  document.getElementById("myNav").style.height = "0%";
}