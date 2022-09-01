(function ($) {
    $('.main-content').on(
        'click', '.prevnext-element a', function (e) {
            e.preventDefault();
            var hrefval = jQuery(this).attr('href');
            $.ajax(
                {
                    url: "/pagination-content", 
                    method :'POST',
                    dataType: "json", 
                    data : {"current_path":hrefval},
                    success: function (result) {
                        jQuery('article').html(result.output);
                        var divHeight = jQuery('.detailpg-main-img').height();
                        jQuery('.view-listing-page-content .text-formatted').css('max-height', divHeight+'px');
                    }
                }
            );
        }
    );

    jQuery(document).ready(function() {
        console.log(1);
        // if(jQuery('body').hasClass('path-frontpage')) {
            var pathhash =window.location.hash;        
            if(pathhash.length > 0){
                console.log(pathhash);
                pathhash = ""+pathhash+"";
                console.log(jQuery(pathhash));
                console.log(jQuery("#slider_item_1"));
                    // console.log(jQuery(`#wholepage ${pathhash}`).offset());
                jQuery('html, body').animate({
                        scrollTop: jQuery(pathhash).offset().top
                }, 2000);
            }
        // }
    });
})(jQuery);