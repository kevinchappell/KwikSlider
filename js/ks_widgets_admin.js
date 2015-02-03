// @todo refactor this to make use of ajaxUrl

jQuery(document).ready(function($) {
  'use strict';

  function ksSliderAutocomplete() {
    $('.ks_ac', '#widgets-right').autocomplete({
      delay: 333,
      source: $('#ks_js_utils_path').text() + '/get_ks_slider.php',
      select: function(event, ui) {
        console.log(ui.item, $(this));
        var slide = $(this).parents('.widget-content');
        $('.ks_slide_id', slide).val(ui.item.id);
        // $('.ks_slide_theme', slide).val(ui.item.theme);
        // $('.ks_prev', slide).attr('src', ui.item.thumbnail);
      },
      minLength: 3
    });
  }

  ksSliderAutocomplete();

  $('div.widgets-sortables').bind('sortstop', function() {
    //reload autocomplete when widget is added
    setTimeout(function() {
      ksSliderAutocomplete();
    }, 2000);
  });

});
