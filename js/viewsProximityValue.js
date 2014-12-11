(function ($, Drupal) {
  Drupal.behaviors.viewsProximityValue = {
    attach: function () {
      if (!$('body').hasClass('page-admin-structure-views-nojs')) {
        $('#edit-options-source-change').hide();
      }
      $('#edit-options-source').change(function() {
        $('#edit-options-source-change').mousedown();
        $('#edit-options-source-change').submit();
      });
    }
  };
})(jQuery, Drupal);
