<?php

// INPUT - Name: slider_options[slider_fx]
function ks_settings() {
  $inputs = new KwikInputs();
  $options = ks_get_options();
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


  $ks_settings = '';
  $ks_settings .= $inputs->text('slider_options[kwik_slider][speed]', $options['kwik_slider']['speed'], __('Transition Speed', 'kwik'));

  $ks_settings .= '<p><label>' . __('Transition Effect', 'kwik') . ':</label>';
  $ks_settings .= '<select name="slider_options[kwik_slider][fx]" value="' . $options['kwik_slider']['fx'] . '" /></p>';
  foreach ($effects as $k => $v) {
    $ks_settings .= '<option value="' . $k . '" ' . ($options['kwik_slider']['fx'] == $k ? 'selected="selected"' : '') . '>' . $v . '</option>';
  }
  $ks_settings .= '</select></p>';
  $ks_settings .= $inputs->markup('div',$inputs->text('slider_options[kwik_slider][delay]', $options['kwik_slider']['delay'], __('Timeout', 'kwik')));
  $ks_settings .= $inputs->markup('div',$inputs->color('slider_options[kwik_slider_bg]', $options['kwik_slider_bg'], __('Background Color', 'kwik')));

  //$ks_settings .= '<p><label>'.__('<span class="tooltip" title="Ambifade is a KC Design lab original effect for the home slider and page header, check the documentation for details">Ambifade</span>','kwik').':</label><input type="checkbox" name="slider_options[ambifade][]" id="ambifade" value="1" '. checked( 1, $options['ambifade'][0], false ) . ' /></p>';
  //$ks_settings .= '<p><label>'.__('Ambifade to...','kwik').'</label><select name="slider_options[ambifade][]"><option value="background" '.($options['ambifade'][1] == 'background' ? 'selected="selected"': '').'>Background</option><option value="opaque" '.($options['ambifade'][1] == 'opaque' ? 'selected="selected"': '').'>Opaque</option><option value="transparent" '.($options['ambifade'][1] == 'transparent' ? 'selected="selected"': '').'>Transparent</option></select>';
  echo $ks_settings;
}
function ks_get_options() {
  return get_option('slide-settings', ks_default_options());
}
function ks_default_options() {
  $ks_default_options = array(
    'general' => array(
      'section_title' => __('General', 'kwik'),
      'section_desc' => __('Set the main options for the KwikTheme website here.', 'kwik'),
      'options' => array(
        'transition_speed' => array(
          'type' => 'text',
          'title' => __('Transition Speed', 'kwik'),
          'value' => '750'
        )
      )
    )
  );
  return apply_filters('ks_default_options', $ks_default_options);
}



add_action('admin_init', 'ks_settings_init');

function ks_settings_init(){
  $utils = new KwikUtils();

  $default = ks_default_options();
  $utils->settings_init('kwik_slider', $default, 'slide-settings');
}













// Validate user data for some/all of your input fields
function ks_settings_validate($input) {
  $output = $defaults = ks_default_options();
  if (isset($input['kwik_slider_bg']) && preg_match('/^#?([a-f0-9]{3}){1,2}$/i', $input['kwik_slider_bg'])) {
    $input['kwik_slider_bg'] = '#' . strtolower(ltrim($input['kwik_slider_bg'], '#'));
  }

  $output = array(
    'kwik_slider' => array(
      'speed' => intval($input['kwik_slider']['speed']),
      'fx' => wp_filter_nohtml_kses($input['kwik_slider']['fx']),
      'delay' => intval($input['kwik_slider']['delay'])
    ),
    'kwik_slider_bg' => $input['kwik_slider_bg']
    /*    'ambifade' => array(

  (isset( $input['ambifade'][0] ) && true == $input['ambifade'][0] ? true : false ),

  wp_filter_nohtml_kses($input['ambifade'][1]),

  )*/
  );
  return apply_filters('ks_settings_validate', $output, $input, $defaults);
}


function home_slide_settings_page() {
  $utils = new KwikUtils();
  $inputs = new KwikInputs();
  $output = '<div class="wrap">';
    $output .= $inputs->markup('h2', __('Slider Settings', 'kwik'));
    $output .= $inputs->markup('p', __('Change the transition effect, duration and speed here.','kwik'));
    $output .= '<form action="options.php" method="post">';
      $output .= $utils->settings('slide-settings');
    $output .= '</form>';
  $output .= '</div>';

  echo $output;
}