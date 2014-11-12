jQuery(document).ready(function ($) {


  $("#ks_slide_meta").sortable({
    cursor: 'move',
    opacity: 0.9,
    handle: '.move_slide',
    cancel: 'input, textarea, select',
    disable: 'input, textarea, select',
    receive: function (event, ui) {
      $("li.no-results", ul_obj).remove();
    }
  });


  function kcdl_box_autocomplete() {
    $('#ks_slide_meta input[name=ks_slide_title]').autocomplete({
      delay: 333,
      source: $('#ks_slide_meta').attr('ks-location') + "/utils/get_ks_slide.php",
      select: function (event, ui) {
        var slide = $(this).parents(".slide_edit");
        $('.ks_slide_id', slide).val(ui.item.id);
        $('.ks-slide-subtitle', slide).val(ui.item.subtitle);
        $('.ks-slide-learnmore', slide).val(ui.item.learn_more);
        $('.ks-slide-link', slide).val(ui.item.link[0]);
        $('.ks-slide-link-target', slide).val(ui.item.link[1]);
        $('.kcdl_box_img_id', slide).val(ui.item.thumbnail_id);
        $('.slide_edit').find('.img_prev', slide).attr('src',ui.item.thumbnail);
      },
      minLength: 3,
      messages: {
        noResults: null,
        results: function () {

        }
      }
    });
  }

  kcdl_box_autocomplete();


  // $('#add_kcdl_box').live("click", function () {
  //   if ($("#kcdl_box_link_meta .home_box").length < 4) {
  //     $(this).parent().hide();
  //     var template = $("#ks_slide_template").html();
  //     $("#ks_slide_meta").append(template);
  //     kcdl_box_autocomplete();
  //   }
  // });

  // $('.clone_slide').on("click", function (e) {
  //   var slide = $(this).parents('.slide_edit');
  //   slide.clone().insertAfter(slide);
  //   reIndex_btn_ul();

    // if ($("#kcdl_box_link_meta .home_box").length < 4) {
    //   $("#kcdl_box_btn_wrap").hide();
    //   var template = $("#ks_slide_template").html();
    //   $("#ks_slide_meta").append(template);
    //   kcdl_box_autocomplete();
    // }
  // });

  $('#ks_slide_meta').on('click', '.remove_slide', function(e){
    if(confirm($(this).attr('del-confirm'))) {
      $(this).parents('.slide_edit').remove();
    }
  });

  $('#ks_slide_meta').on('click', '.clone_slide', function(e){
    var slide = $(this).parents('.slide_edit');
    var newSlide = slide.clone().insertAfter(slide);
    $("input.ks_slide_id", newSlide).val('');
    kcdl_box_autocomplete();
  });

  $('#ks_slide_meta').on('click', '.save_slide', function(e){
    // var slide_id = $(this).closest('input.ks_slide_id').val();
    save_ks_slide($(this));
  });

  $('#ks_slide_meta').on('change', ':input', function(){
    save_ks_slide($(this));
  });

  function save_ks_slide(elem) {
    var slide = elem.parents(".slide_edit"),
    slider_id = { 'name' : $('#post_ID').attr('name'), 'value' : $('#post_ID').val() },
    fields = slide.find(":input").serializeArray(),
    save_ks_slide_util = $('#ks_slide_meta').attr('ks-location') + "/utils/save_ks_slide.php";
    fields.push(slider_id);

    $.post(save_ks_slide_util, fields)
      .done(function (data) {
        $("input.ks_slide_id", slide).val(data.slide_id);
        $(".slide_messages", slide).html(data.message).fadeIn(333).delay(2500).fadeOut(333);
      });

  }


  function reIndex_btn_ul() {
    $("li:not(.ignore)", "#ks_slide_meta.sortable").each(function (i) {
      $("input, textarea, select", this).attr("name",
        function () {
          return $(this).attr("name").replace(/\d+/g, i);
        });
    });
  }


});
