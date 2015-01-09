jQuery(document).ready(function($) {
  'use strict' ;

  $('#ks_slide_meta').sortable({
    opacity: 0.9,
    handle: '.move_slide',
    cancel: 'input, textarea, select',
    disable: 'input, textarea, select'
  });

  function ksSlideAutocomplete() {
    $('#ks_slide_meta input[name=ks_slide_title]')
      .bind('keydown', function(event) {
        if (event.keyCode === $.ui.keyCode.TAB &&
          $(this).data('autocomplete').menu.active) {
          event.preventDefault();
        }
      })
      .autocomplete({
        delay: 333,
        source: $('#ks_slide_meta').attr('ks-location') + '/utils/get_ks_slide.php',
        select: function(event, ui) {
          var slide = $(this).parents('.slide_edit');
          $('.ks_slide_id', slide).val(ui.item.id);
          $('.ks-slide-subtitle', slide).val(ui.item.subtitle);
          $('.ks-slide-learnmore', slide).val(ui.item.learn_more);
          $('.ks-slide-link', slide).val(ui.item.link[0]);
          $('.ks-slide-link-target', slide).val(ui.item.link[1]);
          $('.kcdl_box_img_id', slide).val(ui.item.thumbnail_id);
          $('.img_prev', slide).attr('src', ui.item.thumbnail);
        },
        minLength: 3,
        messages: {
          noResults: null,
          results: function() {

          }
        },
        create: function() {
          $(this)._renderItem = function(ul, item) {
            console.log(item);
            var innerHTML = '<a class="clear">' + item.item.thumbnail + '</a>';
            return $('<li></li>').data('item.autocomplete', item).append(innerHTML).appendTo(ul);
          };
        }
      });
  }


  ksSlideAutocomplete();


  // $('#add_kcdl_box').live("click", function () {
  //   if ($("#kcdl_box_link_meta .home_box").length < 4) {
  //     $(this).parent().hide();
  //     var template = $("#ks_slide_template").html();
  //     $("#ks_slide_meta").append(template);
  //     ksSlideAutocomplete();
  //   }
  // });

  // $('.clone_slide').on("click", function () {
  //   var slide = $(this).parents('.slide_edit');
  //   slide.clone().insertAfter(slide);
  //   reIndex_btn_ul();

  // if ($("#kcdl_box_link_meta .home_box").length < 4) {
  //   $("#kcdl_box_btn_wrap").hide();
  //   var template = $("#ks_slide_template").html();
  //   $("#ks_slide_meta").append(template);
  //   ksSlideAutocomplete();
  // }
  // });

  $('#ks_slide_meta').on('click', '.remove_slide', function() {
    var slide = $(this).parents('.slide_edit');
    if ($('.slide_edit').length > 1) {
      if (confirm($(this).attr('del-confirm'))) {
        slide.remove();
      }
    } else {
      $('.slide_messages', slide).addClass('error').html('Minimum of 1 slide required').fadeIn(333).delay(2500).fadeOut(333, function() {
        $(this).removeClass('error');
      });
    }
  });

  $('#ks_slide_meta').on('click', '.clone_slide', function() {
    var slide = $(this).parents('.slide_edit');
    var newSlide = slide.clone().insertAfter(slide);
    $('input.ks_slide_id', newSlide).val('');
    ksSlideAutocomplete();
  });

  $('#ks_slide_meta').on('click', '.save_slide', function() {
    // var slide_id = $(this).closest('input.ks_slide_id').val();
    saveKsSlide($(this));
  });

  $('#ks_slide_meta').on('change', ':input', function() {
    saveKsSlide($(this));
  });

  function saveKsSlide(elem) {
    var slide = elem.parents('.slide_edit'),
      sliderID = {
        'name': $('#post_ID').attr('name'),
        'value': $('#post_ID').val()
      },
      fields = slide.find(':input').serializeArray(),
      saveKsSlideUtil = $('#ks_slide_meta').attr('ks-location') + "/utils/saveKsSlide.php";
    fields.push(sliderID);

    $.post(saveKsSlideUtil, fields)
      .done(function(data) {
        $('input.ks_slide_id', slide).val(data.slide_id);
        $('.slide_messages', slide).html(data.message).fadeIn(333).delay(2500).fadeOut(333);
      });

  }


});
