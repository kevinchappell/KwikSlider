<?php
add_action( 'admin_menu', 'ks_add_admin_menu' );
add_action( 'admin_init', 'ks_settings_init' );

function ks_add_admin_menu() {
  add_submenu_page( 'edit.php?post_type=kwik_slider', __('Kwik Slider Settings', 'kwik'), __('Settings', 'kwik'), 'manage_options', 'kwik_slider', KS_PLUGIN_SETTINGS );
}

function ks_settings_init() {
  $utils = new KwikUtils();
  $defaultSettings = ks_default_options();
  $utils->settings_init(KS_PLUGIN_BASENAME, KS_PLUGIN_SETTINGS, $defaultSettings);
}

function kwik_slider_settings() {
  $settings = ks_get_options();
  echo '<div class="wrap">';
    echo KwikInputs::markup('h2', __('Slider Settings', 'kwik'));
    echo KwikInputs::markup('p', __('Set the defaults to be used by the sliders. Here you can define transition effects, pagers and themes.','kwik'));
    echo '<form action="options.php" method="post">';
      settings_fields(KS_PLUGIN_SETTINGS);
      echo KwikUtils::settings_sections(KS_PLUGIN_SETTINGS, $settings);
    echo '</form>';
  echo '</div>';
  echo KwikInputs::markup('div', $output, array('class'=>'wrap'));
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

  $themes = array(
  'katrina' => 'Katrina',
  'kevin' => 'Kevin'
  );

  $pagers = array(
  'circle' => 'Circle',
  'square' => 'Square',
  'thumbnail' => 'Thumbnail',
  'number' => 'Number',
  'text' => 'Text'
  );

  $ks_default_options = array(
    'behavior' => array(
      'section_title' => __('Behavior', 'kwik'),
      'section_desc' => __('Set the default options for the Kwik Slider here. Many settings can be overriden on the slider edit page.', 'kwik'),
      'settings' => array(
        'transition_speed' => array(
          'type' => 'spinner',
          'title' => __('Transition Speed', 'kwik'),
          'value' => '750',
          'attrs' => array('min'=>'0', 'max'=>'9001')
        ),
        'transition_timeout' => array(
          'type' => 'spinner',
          'title' => __('Transition Timeout', 'kwik'),
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
    ),
    'appearance' => array(
      'section_title' => __('Appearance', 'kwik'),
      'section_desc' => __('Set default theme and colors.', 'kwik'),
      'settings' => array(
        'theme' => array(
          'type' => 'select',
          'title' => __('Default Theme', 'kwik'),
          'value' => 'Katrina',
          'options' => $themes
        ),
        'pager_style' => array(
          'type' => 'select',
          'title' => __('Pager Style', 'kwik'),
          'value' => 'circle',
          'options' => $pagers
        ),
        'pager_position' => array(
          'type' => 'select',
          'title' => __('Pager Position', 'kwik'),
          'value' => 'circle',
          'options' => KwikInputs::positions()
        ),
        'pager_color' => array(
          'type' => 'color',
          'title' => __('Pager Color', 'kwik'),
          'value' => '#ffffff'
        ),
        'slide_size' => array(
          'title' => __('Slider Size', 'kwik'),
          'desc' => __('This option will create a cropped custom image size with using these dimensions.', 'kwik'),
          'fields' => array(
            'width' => array('type'=>'spinner', 'title'=>'Width:','value'=>'920', 'attrs'=>array('min' => '0', 'max' => '1280')),
            'height' => array('type'=>'spinner', 'title'=>'Height:', 'value'=>'300', 'attrs'=>array('min' => '0', 'max' => '800')),
            'cropped' => array('type'=>'toggle', 'title'=>'Cropped:', 'value'=>'cropped')
            )
        )
      )
    )
  );

  return apply_filters('ks_default_options', $ks_default_options);
}
