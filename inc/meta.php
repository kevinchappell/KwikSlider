<?php

// Add the kwik slider meta box
function add_ks_metaboxes() {
  add_meta_box('ks_meta', 'Slide Details', 'ks_meta', 'kwik_slider', 'side', 'default');
}
// The Kwik Slider meta box
function ks_meta() {
  global $post;
  $inputs = new KwikInputs();
  $ks_meta = '';
  // Noncename for security check on data origin
  $ks_meta .= $inputs->nonce(KS_BASENAME.'_nonce', wp_create_nonce(plugin_basename(__FILE__)));
  // $ks_meta .= '<input type="hidden" name="home_slide_noncename" id="home_slide_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
  // Get the current data
  $ks_slide_link = get_post_meta($post->ID, 'ks_slide_link', true);
  $ks_slide_link_target = get_post_meta($post->ID, 'ks_slide_link_target', true);
  $ks_learn_more = get_post_meta($post->ID, 'ks_learn_more', true);
  $ks_meta .= '<label>Link:</label>';
  $ks_meta .= $inputs->text('ks_slide_link', $ks_slide_link, __('Link', 'kwik'));
  $ks_meta .= '<input type="text" name="ks_slide_link" value="' . $ks_slide_link . '" class="widefat" />';
  $ks_meta .= '<br/>';
  $ks_meta .= $inputs->select('ks_slide_link_target', $ks_slide_link_target, $inputs->target(), __('Target'));
  $ks_meta .= '<br/>';
  $ks_meta .= '<label>Learn More Text:</label>';
  $ks_meta .= '<input type="text" name="ks_learn_more" value="' . $ks_learn_more . '" class="widefat" />';
  echo $ks_meta;
}
// Save the Metabox Data
function save_ks_meta($post_id, $post) {
  if ($post->post_type == 'kwik_slider') {
    $options = get_option('slide-settings');
    // make sure there is no conflict with other post save function and verify the noncename
    if (isset($_POST[KS_BASENAME.'_nonce']) && !wp_verify_nonce($_POST[KS_BASENAME.'_nonce'], plugin_basename(__FILE__))) {
      return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID)) {
      return $post->ID;
    }

    if ($post->post_status != 'auto-draft') {
      $ks_meta = array(
        'ks_slide_link' => wp_filter_nohtml_kses($_POST['ks_slide_link']),
        'ks_slide_link_target' => wp_filter_nohtml_kses($_POST['ks_slide_link_target']),
        'ks_learn_more' => wp_filter_nohtml_kses($_POST['ks_learn_more'])
      );
      // Add values of $ks_meta as custom fields
      foreach ($ks_meta as $key => $value) {
        if ($post->post_type == 'revision') {return;
        }

        __update_post_meta($post->ID, $key, $value);
      }
    } else {
      return;
    }
  } else {
    return;
  }
}
add_action('save_post', 'save_ks_meta', 1, 2);