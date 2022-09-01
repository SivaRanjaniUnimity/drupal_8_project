(function ($) {
   /* jQuery("input[name*='jobrole']").click(function(){
        jQuery(".job-opening-result").empty();
        var thisid=this.id;
        var IsChecked = jQuery('#'+thisid+':checkbox:checked').length > 0;
        var designation = jQuery("label[for='" + this.id + "']").text();
        var jobrole_array = new Array();
        jQuery('input[name*="jobrole"]:checked').each(function(i) {
            jobrole_array[i] = this.value;
        });
        if(jobrole_array.length > 0){
            fetchjobresult(designation,jobrole_array,"designation");
        }
    });


    jQuery("input[name*='job_location']").click(function(){
        jQuery(".job-opening-result").empty();
        var thisid=this.id;
        var IsChecked = jQuery('#'+thisid+':checkbox:checked').length > 0;
        var location = jQuery("label[for='" + this.id + "']").text();
        var joblocation_array = new Array();
        jQuery('input[name*="job_location"]:checked').each(function(i) {
            joblocation_array[i] = this.value;
        });
        if(joblocation_array.length > 0){
            fetchjobresult(location,joblocation_array,"location");
        }
    });
*/

    function fetchjobresult(filterdata,filter_id_arr,type){
      jQuery(".job-opening-result").empty();
        $.ajax(
            {
                url: "/fetch-job-result", 
                method :'POST',
                dataType: "json", 
                data : {"filterdata":filterdata, "filter_id":filter_id_arr, "type":type},
                success: function (result) {
                  // console.log(result);
                  jQuery("input[name*='jobrole']").prop("disabled", "disabled");
                    if(result.output.length > 0){
                        for(var i=0;i<result.output.length;i++){
                            jQuery(".job-opening-result").append(result.output[i]);
                        }
                    }
                    else{
                      jQuery(".job-opening-result").html("No Result Found");
                    }
                    if(result.available_roles.length > 0){
                      for(var i=0;i<result.available_roles.length;i++){
                         var roleid = result.available_roles[i];
                          jQuery("input[name*='jobrole'][value='"+roleid+"']").removeAttr("disabled");
                      }
                  }
                }
            }
        );
    }
    anychart.onDocumentReady(function () {
        var stationsData = getData("info2");      
        var baseLink = '#';
      
        // create map chart
        var map = anychart.map();      
        map.unboundRegions().enabled(true).fill('#93B5D9').stroke('#0057B8');    

        var data1 = getData("info1");
        var dataSet = anychart.data.set(data1);      
      
        // settings for map chart
        map
          .padding([10, 0, 10, 10])
          .geoData(anychart.maps.world);
        // map.interactivity().selectionMode('none');
         map.background('#0057B8');
        
        // create marker series for map chart
        var mapSeries = map.marker(anychart.data.set(stationsData));
        
        // specify the chart type and set the series 
          var series1 = map.choropleth(dataSet);
          
        // set map color settings
        series1.colorScale(anychart.scales.linearColor('#93B5D9'));
        series1.hovered().fill('yellow');
        series1.selected().fill('green');
      
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
          var image_link = '/modules/custom/hoya_job_openings/images/noun-location-1224134.png';
          
          // set the images for the Marker series
          mapSeries.normal().stroke(null);
          mapSeries.hovered().stroke(null);
          mapSeries.selected().stroke(null);
          mapSeries.normal().fill(customImageMarker(1)).size(10);
          mapSeries.hovered().fill(customImageMarker(1)).size(10);
          mapSeries.selected().fill(customImageMarker(1)).size(10);
          
          
          
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
          fetchjobresult(stationsData[e.pointIndex].name,stationsData[e.pointIndex].term_id,"location");
        });      
      
        // set container id for the chart
        map.container('map-loc-container');      
        // initiate chart drawing
        map.draw();
      });
      

      // data for the sample
      function getData(info) {
          var result_data = new Array();

        $.ajax(
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
          var image_link = '/modules/custom/hoya_job_openings/images/noun-location-1224134.png';
            return {
        src: image_link,
          mode: 'fit',
          opacity: op
      }  
      }
      
})(jQuery);