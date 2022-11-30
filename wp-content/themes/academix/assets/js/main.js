jQuery(document).ready( function($) {
    'use strict';
    /* Preloader JS */
    $(window).load(function() {
	    $('.preloader-wrap').fadeOut('500', function() {
            $(this).remove();
        });
	});

    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });

    //=================== Adminer add class ====================
    $('#wpadminbar').addClass('mobile');
});

jQuery(window).load(function() {
    jQuery('.mobile-menu').meanmenu();
});

jQuery(document).ready(function(){
    jQuery(".promagnifier").on("click", function () {
     jQuery('.loader-container').show();  
       
    });
       jQuery(":submit").on("click", function () {
     jQuery('.loader-container').show();
       
    });
       jQuery(".asp_r_pagepost").on("click", function () {
     jQuery('.loader-container').show();
       
    });

});
jQuery(window).load(function(){
       jQuery('.loader-container').fadeOut();
});

jQuery(document).ready(function(){
    jQuery(".history_sub").on("click", function () {
           jQuery(".sub_theme_display").css("display","none");
           jQuery(".sub_theme_1").css("display","block");
       
    });
    jQuery(".philosophy_sub").on("click", function () {
           jQuery(".sub_theme_display").css("display","none");
           jQuery(".sub_theme_2").css("display","block");
       
    });
    jQuery(".perception_sub").on("click", function () {
           jQuery(".sub_theme_display").css("display","none");
           jQuery(".sub_theme_3").css("display","block");
       
    });
    jQuery(".practice_sub").on("click", function () {
           jQuery(".sub_theme_display").css("display","none");
           jQuery(".sub_theme_4").css("display","block");
       
    });
    jQuery(".wsa_sub").on("click", function () {
           jQuery(".sub_theme_display").css("display","none");
           jQuery(".sub_theme_5").css("display","block");
       
    });
    jQuery(".vision_sub").on("click", function () {
           jQuery(".sub_theme_display").css("display","none");
           jQuery(".sub_theme_6").css("display","block");
       
    });



});

