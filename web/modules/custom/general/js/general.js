(function ($) {
    $('.content').on('click', '.prevnext-element a', function(e) {
              e.preventDefault();
              var hrefval = jQuery(this).attr('href');
              $.ajax({
                url: "/pagination-content", 
                method :'POST',
                dataType: "json", 
                data : {"current_path":hrefval},
                success: function(result){
                  console.log(result.output);
                  jQuery('article').replaceWith(result.output);
                }
            });
          });
    
        
})(jQuery);