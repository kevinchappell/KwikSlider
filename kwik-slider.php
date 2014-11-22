<?php
/*
Plugin Name: Kwik Slider
Plugin URI: http://kevin-chappell.com/kwik-slider
Description: A slide show plugin perfect for homepage sliders
Author: Kevin Chappell
Version: .1
Author URI: http://kevin-chappell.com
 */



define('KS_PLUGIN_BASENAME', basename(dirname( __FILE__ )));
define('KS_PLUGIN_SETTINGS', preg_replace('/-/', '_', KS_PLUGIN_BASENAME).'_settings');
define('KS_PLUGIN_URL', untrailingslashit(plugins_url('', __FILE__)));
define('KS_PLUGIN_PATH', untrailingslashit( dirname( __FILE__ ) ) );
define('KS_PREFIX', 'ks_' );

foreach (glob(KS_PLUGIN_PATH . "/inc/*.php") as $inc_filename) {
  include $inc_filename;
}
foreach (glob(KS_PLUGIN_PATH . "/widgets/*.php") as $inc_filename) {
  include $inc_filename;
}

add_action('init', 'ks_cpt_init');
function ks_cpt_init() {
	register_post_type('kwik_slider', array(
		'labels' => array(
			'name' => __('Kwik Sliders', 'kwik'),
			'singular_name' => __('Slider', 'kwik'),
			'add_new' => __('Create Slider', 'kwik'),
			'add_new_item' => __('Create New Slider', 'kwik'),
      'not_found' => __('No Sliders found', 'kwik')
		),
		'menu_icon' => 'dashicons-slides',
		'menu_position' => 3,
		'register_meta_box_cb' => 'add_ks_metaboxes',
		'supports' => array(
			'title',
			// 'editor',
			// 'author',
			// 'thumbnail'
		),
		'public' => true,
		'exclude_from_search' => true,
		'has_archive' => false,
    'rewrite' => array( 'slug' => 'kwik-slider' )
	));

  register_post_type('ks_slide', array(
    'labels' => array(
      'name' => __('Slides', 'kwik'),
      'singular_name' => __('Slide', 'kwik'),
      'add_new' => __('Create Slide', 'kwik'),
      'add_new_item' => __('Create New Slide', 'kwik')
    ),
    'menu_icon' => 'dashicons-format-image',
    'menu_position' => 3,
    // 'register_meta_box_cb' => 'add_ks_metaboxes',
    'supports' => array(
      'title',
      'editor',
      'author',
      'thumbnail'
    ),
    'show_ui' => true,
    'public' => true,
    'exclude_from_search' => true,
    'has_archive' => false
  ));
	add_image_size('kwik_slider', 920, 230, true);
}

// add_action('admin_init', 'slider_options_init');
// function slider_options_init() {
// 	register_setting('slider_options', 'slider_options', 'ks_settings_validate');
// }

function ks_admin_js_css($hook) {
	$screen = get_current_screen();
	// make these settings
	$post_types_array = array(
		"kwik_slider",
		"page",
		"kwik_slider_page_slide-order",
	);

	$hooks_array = array(
		"post.php",
		"post-new.php",
	);

	// Check screen hook and current post type
	if (in_array($screen->post_type, $post_types_array)) {
		wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('ks-admin-edit-js', KS_PLUGIN_URL . '/js/' . KS_PREFIX . 'admin.js', array('jquery', 'jquery-ui-sortable'), NULL, true);
    wp_enqueue_style('ks-admin-css', KS_PLUGIN_URL . '/css/' . KS_PREFIX . 'admin.css', false, '2014-10-28');
  } elseif ('widgets.php' == $hook ) {
    wp_enqueue_script('ks-admin-widgets-js', KS_PLUGIN_URL . '/js/' . KS_PREFIX . 'widgets_admin.js', array('jquery'), NULL, true);
    wp_enqueue_style('ks-admin-widgets-css', KS_PLUGIN_URL . '/css/' . KS_PREFIX . 'admin_widgets.css', false, '2014-10-28');
	}
}
add_action('admin_enqueue_scripts', 'ks_admin_js_css');


function js_utils_path(){
  echo '<span style="display:none;" id="ks_js_utils_path">'.KS_PLUGIN_URL.'/utils</span>';
}
add_action('admin_head', 'js_utils_path');


/**
 * Enqueue scripts and styles for front-end.
 *
 * @since KwikSlider 1.0
 */
function ks_scripts_and_styles() {

  wp_enqueue_script('jquery-cycle', 'http://malsup.github.io/min/jquery.cycle2.min.js', array('jquery'));
  wp_enqueue_style('ks-main-css', KS_PLUGIN_URL . '/css/' . KS_PREFIX . 'main.css', false, '2014-10-28');

}
add_action('wp_enqueue_scripts', 'ks_scripts_and_styles');




function get_slider($slider_id){
  $output = '';
  $inputs = new KwikInputs();
  $kwik_slides = get_post_meta($slider_id, '_ks_slides')[0];
  $ks_settings = get_post_meta($slider_id, '_ks_slider_settings')[0];

  if(!empty($kwik_slides)){
    foreach ($kwik_slides as $i => $v){
      $output .= get_slide($v, $i);
    }
  }

  wp_enqueue_style('ks-admin-widgets-css', KS_PLUGIN_URL . '/css/themes/'.$ks_settings['theme'].'.css', false, '2014-10-28');


  $slider_settings = array();
  foreach ($ks_settings as $sk => $sv){
    $slider_settings['data-cycle-'.$sk] = $sv;
  }
  $slider_settings['data-cycle-slides'] = '.ks_slide';

  $slider_settings['class'] = array(
    'ks_slider_wrap',
    'cycle-slideshow'
  );

  $output = $inputs->markup('div', $output, $slider_settings);

  return $output;
}

function get_slide($slide_id, $i=0){
  $inputs = new KwikInputs();
  $slide = get_post( $slide_id );


  $subtitle_val   =  get_post_meta($slide->ID,  '_slide_subtitle', true) ;
  $learnmore_val  =  get_post_meta($slide->ID,  '_slide_learnmore', true);
  $link_val       =  get_post_meta($slide->ID,  '_slide_link', true);
  $img_val        =  get_post_thumbnail_id( $slide->ID );
  $title_val      =  $slide->post_title;

  if($subtitle_val){
    $subtitle = $inputs->markup('p', $subtitle_val);
  }

  $img = get_the_post_thumbnail($slide->ID, 'kwik_slider');

  if($link_val){
    $link_attrs = array("href" => $link_val[url], "target" => $link_val[target], "title" => $title_val);
    $img = $inputs->markup('a', $img, $link_attrs);
    $title_val = $inputs->markup('a', $title_val, $link_attrs);
  } else {

  }

  $slide_info = array(
    'title' => $inputs->markup('h2', $title_val),
    'subtitle' => $subtitle
    );

  $slide_info = $inputs->markup('div', $slide_info, array('class'=>'ks_slide_info'));

  $slide_wrap = $inputs->markup('div', $img.$slide_info, array("class" => array("ks_slide", "ks_slide_".$i)));


  return $slide_wrap;
}


function get_pager($theme, $pager_settings){
  $output = '';

  var_dump($pager_settings);


  return $output;
}