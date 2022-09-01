(function ($) {
    $('body').on(
        'keyup', '#search-family', function (e) {
            var family = jQuery(this).val();
            if(family.length >=3){
                fetchfamilyresult(family);
            }
        }
    );

    jQuery("body").on('click','.family-srchs-row',function(){
        var linkval = jQuery(this).find('a').attr('href');
        window.location.href = "http://localhost/drupal_8_project/web/"+linkval;
    });

    $('#resource-tabs li').on('click', function() {
        if ($(this).hasClass('active')) {
            return false;
        }
        
        $('#resource-tabs li').removeClass('active');
        $('.add-resource-family').removeClass('active');
        $('.tab-addresource').removeClass('active');
        $(this).addClass('active');
        var id     = $(this).attr('rel');
        $('.tab-container').hide();
        $('.' + id).fadeIn('fast');
        // $('.attribute-edit').hide();
        // $('.selected-options').html('');
        // $('#form-url-edit').ajaxForm().resetForm();
        // $('tr.resource-url').hide();
        // $('#detail-view').hide();
    });

    // $('.add-resource-family').on('click', function() {
    jQuery("body").on('click','.add-resource-family',function(){
        if ($(this).hasClass('active')) {
            return false;
        }        
        $('#resource-tabs li').removeClass('active');
        $(this).addClass('active');
        var id     = $(this).attr('rel');
        $('.tab-container').hide();
        $('.tab-addresource').addClass('active');
        // $('.tab-addresource.active .view-content').fadeIn('fast');
    });

    // jQuery("body").on('click','.tab-addresource tr',function(){
    //     console.log(1);
    //     jQuery(this).toggleClass("hover");
    // });


    jQuery("body").on('click','.tab-addresource tr',function(){
    // $('#file-manager-list tbody').on('click', 'td', function() {
        var fileId = jQuery(this).find(".views-field.views-field-nid").text();
        var pathname = window.location.pathname;
        var familyId = pathname.substring(pathname.lastIndexOf('/') + 1);
        // var familyId = jQuery(".views-field.views-field-nid").text();
        if (jQuery(this).hasClass('linked')) {
            jQuery(this).removeClass('linked');
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {'fileId' : fileId , 'familyId' : familyId},
                url: 'http://localhost/drupal_8_project/web/unlinkFileToFamily',
                success: function(data) {
                    console.log(data);
                }
            });
        } else {
            jQuery(this).addClass("linked");
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {'fileId' : fileId , 'familyId' : familyId},
                url: 'http://localhost/drupal_8_project/web/linkFileToFamily',
                success: function(data) {
                    console.log(data);
                }
            });
        }
    });


    function fetchfamilyresult(family){
        $.ajax(
            {
                url: "http://localhost/drupal_8_project/web/search-family", 
                method :'POST',
                dataType: "json", 
                data : {"family":family},
                success: function (result) {
                    var output = "";
                    output +='<table class="row" width="100%">';
                    for(var i=0;i<result.output.length;i++){
                        var nid = result.output[i].nid;
                        var title = result.output[i].title;
                        var image_url = result.output[i].image_url;

                        output +='<tr class="family-srchs-row"><td><img class="family-img" src="'+image_url+'"></td><td class="col-md-9 family-title">'+title+'</td><td class="family-resource-url" style="display:none;"><a href="/resource-manager/'+nid+'" target="_blank"></a></td></tr>';
                    }  
                    output +='</table>';
                    console.log(output);
                    jQuery("#familySearch-result-block").html(output);
                }
            }
        );
    }

    // jQuery("body").on('hover','.family-srchs-row',function(){
       
    // });

})(jQuery);