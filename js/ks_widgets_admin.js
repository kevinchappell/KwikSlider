jQuery(document).ready(function($) {

  function ks_slider_autocomplete() {
    $('.ks_ac', '#widgets-right').autocomplete({
      delay: 333,
      source: $('#ks_js_utils_path').text() + "/get_ks_slider.php",
      select: function(event, ui) {
        console.log(ui.item);
        var slide = $(this).parents(".slide_edit");
        $('.ks_slide_id', slide).val(ui.item.id);
        $('.slide_edit').find('.img_prev', slide).attr('src', ui.item.thumbnail);
      },
      minLength: 3,
      messages: {
        noResults: null,
        results: function() {

        }
      }
    });
  }

  ks_slider_autocomplete();

  $("div.widgets-sortables").bind("sortstop", function(event, ui) {

    //reload autocomplete when widget is added
    setTimeout(function() {
      ks_slider_autocomplete();
    }, 2000);

  });

});
