(function ($) {
  // Drupal.behaviors.hoya_job_openings = {
  //     attach: function(context,settings) {    
        
      
        // $("body",context).once('hoya_job_openings').on("click", ".readmore-content", function(){
        //   jQuery("body .visible-content-detail").addClass("active");
        // });

        $("body").on("click", "input[name*='job_location']", function(){
        // jQuery("input[name*='job_location']").click(function(){
          var thisid=this.id;
          var result_data = new Array();
      var IsChecked = jQuery('#'+thisid+':checkbox:checked').length > 0;
      var location = jQuery("label[for='" + this.id + "']").text();
      var joblocation_array = new Array();
      jQuery('input[name*="job_location"]:checked').each(function(i) {
          joblocation_array[i] = this.value;
      });
      if(joblocation_array.length > 0){
              // fetchjobresult(designation,jobrole_array,"designation");
              jQuery.ajax(
                {
                    url: "/fetch-job-result", 
                    method :'POST',
                    dataType: "json", 
                    async: false,
                    data : {"filterdata":location, "filter_id":joblocation_array, "type":"location"},
                    success: function (result) {
                      // console.log(result);
                      if(result.termarray.length > 0){
                        result_data = result.termarray;
                        callanychart(result_data);
                        // console.log(result_data);
                        // for(var i=0;i<result.termarray.length;i++){
                        //    var jobid = result.termarray[i].term_id;
                        //    var jobname = result.termarray[i].name;

                        //    console.log(datainfo);
                        //     console.log(roleid);
                        // }
                    }
                    }
                  });
          }
        });

        // callanychart(); 
        function callanychart(dataset3){
          console.log(dataset3);
        // once('#map-loc-container', 'html', context).forEach( function (element) {
          anychart.onDocumentReady(function () {
            jQuery("#map-loc-container").empty();
          console.log(dataset3);
        // jQuery(document).one('ready',function(){
          anychart.onDocumentReady(function () {
            var stationsData = getData("info2");      
            var baseLink = '#';
            
            // create map chart
            var map = anychart.map();      
            map.unboundRegions().enabled(true).fill('#93B5D9').stroke('#0057B8');    
            
            var datainfo = getData("info1");
            // console.log(datainfo);
            var dataSet = anychart.data.set(datainfo);      
          
            // settings for map chart
            map
              .padding([10, 0, 10, 10])
              .geoData(anychart.maps.world);
            // map.interactivity().selectionMode('none');
             map.background('#0057B8');
            
            // create marker series for map chart
            var mapSeries = map.marker(anychart.data.set(stationsData));
            
            // specify the chart type and set the series 
              // var series1 = map.choropleth(dataSet);
              
            // set map color settings
            // series1.colorScale(anychart.scales.linearColor('#93B5D9'));
            // series1.hovered().fill('yellow');
            // series1.selected().fill('green');
          

            if(dataset3 != undefined){
              console.log(dataset3);
           
           // set map color settings
           var dataset2 = anychart.data.set(dataset3);
           // var dataset2 = anychart.data.set(dataset2);
           var sereis2 = map.choropleth(dataset2);
           sereis2.colorScale(anychart.scales.linearColor('#93B5D9'));
           
           sereis2.fill('#53e2e2');
           
           sereis2.hovered().fill('green');
           
           sereis2.selected().fill('green');
           
            }

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
            // series1
            //   .tooltip()
            //   .useHtml(true)
            //   .title(false)
            //   .separator(false)
            //   .format(function () {
            //     return (
            //       '<div style="width:80px;height:55px;background-color: #FFFFFF; display: flex; justify-content:center; align-items: center;"><div style="position: absolute; "><div style="width:26px; height:26px; border-radius:13px; color:#fff; line-height:26px; text-align:center; background:#3479C6; margin: 0 auto;">' +
            //       this.getData('number') +
            //       '</div><div style="padding-top:3px;color: #0057B8;text-align:center;">' +
            //       this.getData('value') +
            //       '</div></div></div>'
            //     );
            //   });
          
            // onclick function for points - redirecting client (based on baseLink variable)
            map.listen('pointClick', function (e) {
            //  console.log(stationsData[e.pointIndex].id);

            //  stationsData[e.pointIndex].id.selected().fill('green');
             
              window.open(baseLink + stationsData[e.pointIndex].id, '_self');
              // stationsData.selected().fill('orange');
              fetchjobresult(stationsData[e.pointIndex].name,stationsData[e.pointIndex].term_id,"location");
              // console.log(series1);
            });      
          // series1.colorScale(anychart.scales.linearColor('#FFEBD6','#C40A0A'));
            // set container id for the chart
            map.container('map-loc-container');      
            // initiate chart drawing
            map.draw();
          });
        });
      }

//       }
// };

})(jQuery);

// load_all_data();

// function load_all_data(){
//   jQuery(".job-opening-result").empty();
//        jQuery.ajax(
//            {
//                url: "/fetch-job-result", 
//                method :'POST',
//                dataType: "json", 
//                data : {"filterdata":"", "filter_id":"", "type":"All"},
//                success: function (result) {
//                 //  console.log(result);
//                    if(result.output.length > 0){
//                        for(var i=0;i<result.output.length;i++){
//                            jQuery(".job-opening-result").append(result.output[i]);
//                        }
//                    }
//                    else{
//                      jQuery(".job-opening-result").html("No Result Found");
//                    }
//                }
//            }
//        );
// }
 
   function fetchjobresult(filterdata,filter_id_arr,type){
     jQuery(".job-opening-result").empty();
       jQuery.ajax(
           {
               url: "/fetch-job-result", 
               method :'POST',
               dataType: "json", 
               data : {"filterdata":filterdata, "filter_id":filter_id_arr, "type":type},
               success: function (result) {
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
         var image_link = '/modules/custom/hoya_job_openings/images/noun-location-1224134.png';
           return {
       src: image_link,
         mode: 'fit',
         opacity: op
     }  
     }
     
