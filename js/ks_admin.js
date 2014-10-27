jQuery(document).ready(function ($) {

  $('#sortable-table tbody').sortable({
    axis: 'y',
    handle: '.column-order img',
    placeholder: 'ui-state-highlight',
    forcePlaceholderSize: true,
    update: function (event, ui) {
      var theOrder = $(this).sortable('toArray');

      var data = {
        action: 'home_slide_update_post_order',
        postType: $(this).attr('data-post-type'),
        order: theOrder
      };

      $.post(ajaxurl, data);
    }
  }).disableSelection();



  $("#ks_slide_meta").sortable({
    cursor: 'move',
    opacity: 0.9,
    cancel: 'input, textarea, select',
    receive: function (event, ui) {
      $("li.no-results", ul_obj).remove();
    }
  }).disableSelection();


  function kcdl_box_autocomplete() {
    $('#ks_slide_meta input[name=kcdl_box_ttl]').autocomplete({
      delay: 333,
      source: $('#wp-admin-bar-site-name a').attr('href') + "wp-content/themes/kuusakoski/utils/get_kcdl_boxes.php",
      select: function (event, ui) {
        var element = $(this);
        element.closest('.kcdl_box_id').val(ui.item.id);
        // element.closest('.kcdl_box_id').val(ui.item.id);
        element.siblings('.subtitle').val(ui.item.subtitle);
        element.siblings('.learn_more').val(ui.item.learn_more);
        element.siblings('.box_link').val(ui.item.link);
        element.siblings('.kcdl_box_id').val(ui.item.id);
        element.parents(".home_box").find('.kcdl_box_img_id').val(ui.item.thumbnail_id);

        element.parents(".home_box").find('.img_prev').first().empty().append($("<img>", {
          "src": ui.item.thumbnail,
          "class": "img_prev"
        }), '<span class="clear_img tooltip" title="Remove Box">×</span>');
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


  $('#add_kcdl_box').live("click", function () {
    if ($("#kcdl_box_link_meta .home_box").length < 4) {
      $(this).parent().hide();
      var template = $("#kcdl_box_template").html();
      $("#ks_slide_meta").append(template);
      kcdl_box_autocomplete();
    }
  });

  $('#add_box_btn').live("click", function () {
    if ($("#kcdl_box_link_meta .home_box").length < 4) {
      $("#kcdl_box_btn_wrap").hide();
      var template = $("#kcdl_box_template").html();
      $("#ks_slide_meta").append(template);
      kcdl_box_autocomplete();
    }
  });


  $('.home_box input').live("change", function () {
    elem = $(this);
    save_kcdl_box(elem);
  });


  function save_kcdl_box(elem) {

    var kcdl_box_id = elem.parents(".home_box").find("input.kcdl_box_id"),
      inputs = elem.parents(".home_box").find("input"),
      input_array = {},
      site_url = $("#wp-admin-bar-site-name a").first().attr("href"),
      save_kcdl_box_util = site_url + "wp-content/themes/kuusakoski/utils/save_kcdl_box.php";

    inputs.each(function () {
      input_array[$(this).attr("name")] = $(this).val();
    });

    $.post(save_kcdl_box_util, input_array)
      .done(function (data) {
        kcdl_box_id.val(data);
      });

  }

  $('.home_box .clear_img').live("click", function () {
    $(this).parents(".home_box").first().remove();
    if ($("#kcdl_box_link_meta .home_box").length <= 1) {
      $("#kcdl_box_btn_wrap").show();
    }
  });

});
