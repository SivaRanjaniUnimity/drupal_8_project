(function ($, Drupal) {
  "use strict";

  Drupal.behaviors.AnalyticsDatalayer = {
    attach: function (context, settings) {

      // Check to see if dataLayer is available.
      if (typeof dataLayer !== 'undefined') {

        $('[data-analytics-layer]').once('coh-js-analytics-layer-init').each(function () {

          // Save the element reference.
          var element = $(this);

          // Decode the analytics JSON from the data attribute.
          var events = JSON.parse($(this).attr('data-analytics-layer'));

          // Loop through this attribute object, creating an event for each entry.
          events.forEach(function (event) {

            element.bind(event.trigger, function (e, inView) {

              // inview trigger fired, but element left the viewport.
              if (e.type === 'inview' && !inView) {
                return;
              }

              // Create object with event key value.
              var obj = {};
              obj[event.key] = event.value;

              // Push the data.
              dataLayer.push(obj);
            });
          });
        });
      }
      // Data layer is not defined.
      else {
        console.warn('Data layer is not available, but Data layer events have been defined.');
      }
    }
  };

})(jQuery, Drupal);
