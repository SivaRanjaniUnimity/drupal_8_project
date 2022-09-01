
/* *
 * @license
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.CohesionRowForColumns = {

    attach: function (context, settings) {

      $.each($('[data-coh-row-match-heights]', context).once('coh-row-match-heights-init'), function () {

        var targets = $(this).data('cohRowMatchHeights');

        $('> .coh-row-inner', this).cohesionContainerMatchHeights({
          'targets': targets,
          'context': context
        });
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
