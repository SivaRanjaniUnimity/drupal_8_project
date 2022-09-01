(function ($) {
  Drupal.behaviors.hoya_job_openings = {
      attach: function(context,settings) {  
   
      $("body",context).once('hoya_job_openings').on("click", ".readmore-content", function(){
        jQuery("body .visible-content-detail").toggleClass("active");
      });

      $("body",context).once().on("click", "input[name*='job_location']", function(){
          var result_data = new Array();
          var location = jQuery("label[for='" + this.id + "']").text();
          var joblocation_array = new Array();
          jQuery('input[name*="job_location"]:checked').each(function(i) {
              joblocation_array[i] = this.value;
          });
          if(joblocation_array.length > 0){
            jQuery("[class*=js-form-item-jobrole]").hide();
            jQuery.ajax(
              {
                url: "/fetch-job-result", 
                method :'POST',
                dataType: "json", 
                async: false,
                data : {"filterdata":location, "filter_id":joblocation_array, "type":"location"},
                success: function (result) {
                  if(result.dataset.length > 0){
                    result_data = result.dataset;
                    callAnychart(result_data);
                }
                if(result.available_roles.length > 0){
                  for(var i=0;i<result.available_roles.length;i++){
                     var roleid = result.available_roles[i];
                      // jQuery("input[name*='jobrole'][value='"+roleid+"']").removeAttr("disabled");                      
                      jQuery(".js-form-item-jobrole-"+roleid).show();
                  }
                }
                }
              });
          }
          else{
            jQuery("[class*=js-form-item-jobrole]").show();
            callAnychart();
          }
      });
      
      }
    };
  
  })(jQuery);  
  
  callAnychart();
  function callAnychart(locationData){
    anychart.onDocumentReady(function () {
    // once('#map-loc-container', 'html', context).forEach( function (element) {
      jQuery("#map-loc-container").empty();
      var stationsData = getData("info2");      
      var baseLink = '#';
    
      // create map chart
      var map = anychart.map();      
      map.unboundRegions().enabled(true).fill('#83aded').stroke("#3479C6");  

      var datainfo = getData("info1");
      var dataSet = anychart.data.set(datainfo);      
    
      // settings for map chart
      map
        .padding([10, 0, 10, 10])
        .geoData(anychart.maps.world);
      // map.interactivity().selectionMode('none');
       map.background('#3479C6');
      
      // create marker series for map chart
      var mapSeries = map.marker(anychart.data.set(stationsData));
      
      if(locationData != undefined){
        var loc_dataset = anychart.data.set(locationData);
        // var dataset2 = anychart.data.set(dataset2);
        var sereis2 = map.choropleth(loc_dataset);
        sereis2.colorScale(anychart.scales.linearColor('#0057b8'));        
        sereis2.fill('#0057b8');        
        sereis2.hovered().fill('#0057b8');        
        sereis2.selected().fill('#0057b8');       
      }

      // specify the chart type and set the series 
      var series1 = map.choropleth(dataSet);
        
      // set map color settings
      // series1.colorScale(anychart.scales.linearColor('#83aded'));
      series1.normal().fill('#83aded').stroke("#3479C6");
      series1.hovered().fill('#0057b8').stroke("#3479C6");
      series1.selected().fill('#0057b8').stroke("#3479C6");
    
      // setting for marker series for map chart
      mapSeries
        .labels()
        .enabled(true)
        .position('center-top')
        .anchor('center-top')
        .offsetY(-12)
        .hAlign('center')
        .fontColor('#e5ad4a')
        .fontWeight('bold')
        .format(function () {
          return this.getData('');
        });
    
      mapSeries.size(15).geoIdField('code_hasc');
    
     
          // set the link for the directory with images
        var image_link = '/modules/custom/hoya_job_openings/images/noun-location.svg';
        
        // set the images for the Marker series
        mapSeries.normal().stroke(null);
        mapSeries.hovered().stroke(null);
        mapSeries.selected().stroke(null);
        mapSeries.normal().fill(customImageMarker(1)).size(15);
        mapSeries.hovered().fill(customImageMarker(1)).size(15);
        mapSeries.selected().fill(customImageMarker(1)).size(15);         
        
        
        mapSeries.colorScale('#FFFFFF');
    
      // custom text in tooltips for marker series for map chart
      mapSeries
        .tooltip()
        .useHtml(true)
        .title(false)
        .separator(false)
        .format(function () {
          return (
            '<div style="width:80px;height:55px;background-color: #FFFFFF; display: flex; justify-content:center; align-items: center;"><div style="position: absolute; "><div style="width:26px; height:26px; border-radius:13px; color:#fff; line-height:26px; text-align:center; background:#3479C6; margin: 0 auto;">' +
            this.getData('number') +
            '</div><div style="padding-top:3px;color: #0057B8;text-align:center;">' +
            this.getData('name') +
            '</div></div></div>'
          );
        });
        
        
          // custom text in tooltips for marker series for map chart
      series1
        .tooltip()
        .useHtml(true)
        .title(false)
        .separator(false)
        .format(function () {
          return (
            '<div style="width:80px;height:55px;background-color: #FFFFFF; display: flex; justify-content:center; align-items: center;"><div style="position: absolute; "><div style="width:26px; height:26px; border-radius:13px; color:#fff; line-height:26px; text-align:center; background:#3479C6; margin: 0 auto;">' +
            this.getData('number') +
            '</div><div style="padding-top:3px;color: #0057B8;text-align:center;">' +
            this.getData('value') +
            '</div></div></div>'
          );
        });
    
      // onclick function for points - redirecting client (based on baseLink variable)
      map.listen('pointClick', function (e) {
        window.open(baseLink + stationsData[e.pointIndex].id, '_self');
        var locationid = [stationsData[e.pointIndex].term_id];
        fetchjobresult(stationsData[e.pointIndex].name,locationid,"location");
      });      
    
      // set container id for the chart
      map.container('map-loc-container');      
      // initiate chart drawing
      map.draw();
    });
  }
 
  // data for the jobresult
  function getData(info) {
  var result_data = new Array(); 
    jQuery.ajax(
      {
        url: "/fetch-getoffice_details", 
        method :'POST',
        dataType: "json",
        async: false,
        success: function (result) {
        if(info == "info1"){
            if(result.output.dataset.length > 0){
                result_data = result.output.dataset;
            }
        }
        else{
            if(result.output.data.length > 0){
                result_data = result.output.data;
            }
        }                   
        }
      }
    ); 
    return result_data;
  }
  
  function customImageMarker(op){
      var image_link = '/modules/custom/hoya_job_openings/images/noun-location.svg';
      return {
        src: image_link,
        mode: 'fit',
        opacity: op
      }  
  }
       
      

  function fetchjobresult(filterdata,filter_id_arr,type){
    jQuery("#result-container").empty();
    jQuery.ajax(
          {
              url: "/fetch-job-result", 
              method :'POST',
              dataType: "json", 
              data : {"filterdata":filterdata, "filter_id":filter_id_arr, "type":type},
              success: function (result) {
                // jQuery("input[name*='jobrole']").prop("disabled", "disabled");
                  if(result.output.length > 0){
                      for(var i=0;i<result.output.length;i++){
                          jQuery("#result-container").append(result.output[i]);
                      }
                  }
                  else{
                    jQuery("#result-container").html("No Result Found");
                  }
                  if(result.available_roles.length > 0){
                    jQuery("[class*=js-form-item-jobrole]").hide();
                    for(var i=0;i<result.available_roles.length;i++){
                       var roleid = result.available_roles[i];
                        // jQuery("input[name*='jobrole'][value='"+roleid+"']").removeAttr("disabled");
                        jQuery(".js-form-item-jobrole-"+roleid).show();
                    }
                  }
                  if(result.dataset.length > 0){
                    result_data = result.dataset;
                    callAnychart(result_data);
                  }
              }
          }
      );
  }