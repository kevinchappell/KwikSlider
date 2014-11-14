<?php
add_action( 'admin_menu', 'ks_add_admin_menu' );
add_action( 'admin_init', 'ks_settings_init' );

function ks_add_admin_menu(  ) {
  add_submenu_page( 'edit.php?post_type=kwik_slider', __('Kwik Slider Settings', 'kwik'), __('Settings', 'kwik'), 'manage_options', 'kwik_slider', KS_PLUGIN_SETTINGS );
}

function ks_settings_init() {
  $utils = new KwikUtils();
  $defaultSettings = ks_default_options();
  $utils->settings_init(KS_PLUGIN_BASENAME, KS_PLUGIN_SETTINGS, $defaultSettings);
}

function kwik_slider_settings() {
  $utils = new KwikUtils();
  $inputs = new KwikInputs();
  $settings = ks_get_options();

  echo '<div class="wrap">';
    echo $inputs->markup('h2', __('Slider Settings', 'kwik'));
    echo $inputs->markup('p', __('Change the transition effect, duration and speed here.','kwik'));
    echo '<form action="options.php" method="post">';
      settings_fields(KS_PLUGIN_SETTINGS);
      echo $utils->settings_sections(KS_PLUGIN_SETTINGS, $settings);
    echo '</form>';
  echo '</div>';

}

function ks_get_options() {
  return get_option(KS_PLUGIN_SETTINGS, ks_default_options());
}
function ks_default_options() {
    $effects = array(
    'blindX' => __('Blind X', 'kwik'),
    'blindY' => __('Blind Y', 'kwik'),
    'blindZ' => __('Blind Z', 'kwik'),
    'cover' => __('Cover', 'kwik'),
    'curtainX' => __('Curtain X', 'kwik'),
    'curtainY' => __('Curtain Y', 'kwik'),
    'fade' => __('Fade', 'kwik'),
    'fadeZoom' => __('Fade Zoom', 'kwik'),
    'growX' => __('Grow X', 'kwik'),
    'growY' => __('Grow Y', 'kwik'),
    'none' => __('None', 'kwik'),
    'scrollUp' => __('Scroll Up', 'kwik'),
    'scrollDown' => __('Scroll Down', 'kwik'),
    'scrollLeft' => __('Scroll Left', 'kwik'),
    'scrollRight' => __('Scroll Right', 'kwik'),
    'scrollHorz' => __('Scroll Horizontal', 'kwik'),
    'scrollVert' => __('Scroll Vertical', 'kwik'),
    'shuffle' => __('Shuffle', 'kwik'),
    'slideX' => __('Slide X', 'kwik'),
    'slideY' => __('Slide Y', 'kwik'),
    'tiles' => __('Tiles', 'kwik'),
    'toss' => __('Toss', 'kwik'),
    'turnUp' => __('Turn Up', 'kwik'),
    'turnDown' => __('Turn Down', 'kwik'),
    'turnLeft' => __('Turn Left', 'kwik'),
    'turnRight' => __('Turn Right', 'kwik'),
    'uncover' => __('Uncover', 'kwik'),
    'wipe' => __('Wipe', 'kwik'),
    'zoom' => __('Zoom', 'kwik')
  );

  $ks_default_options = array(
    'general' => array(
      'section_title' => __('General', 'kwik'),
      'section_desc' => __('Set the default options for the Kwik Slider here. Many settings can be overriden on the slider edit page.', 'kwik'),
      'settings' => array(
        'transition_speed' => array(
          'type' => 'spinner',
          'title' => __('Transition Speed', 'kwik'),
          'value' => '750',
          'attrs' => array('min'=>'0', 'max'=>'9001')
        ),
        'transition_delay' => array(
          'type' => 'spinner',
          'title' => __('Transition Delay', 'kwik'),
          'value' => '3000',
          'attrs' => array('min'=>'0', 'max'=>'9001')
        ),
        'transition_effect' => array(
          'type' => 'select',
          'title' => __('Transition Effect', 'kwik'),
          'value' => 'fade',
          'options' => $effects
        )
      )
    )
  );

  return apply_filters('ks_default_options', $ks_default_options);
}
