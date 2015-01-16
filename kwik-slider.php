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

  $settings = ks_get_options();

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
    'show_ui' => false,
    'public' => true,
    'exclude_from_search' => true,
    'has_archive' => false
  ));
  if(isset($settings['slide_size'])){
    $slide_size = $settings['slide_size'];
    $cropped = isset($slide_size['cropped']) ? TRUE : FALSE;
  	add_image_size('kwik_slider', $slide_size['width'], $slide_size['height'], $cropped);
  }
}

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

  wp_enqueue_script('jquery-ui-autocomplete');
  // Check screen hook and current post type
  if (in_array($screen->post_type, $post_types_array)) {
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('ks-admin-edit-js', KS_PLUGIN_URL . '/js/' . KS_PREFIX . 'admin.js', array('jquery', 'jquery-ui-sortable'), NULL, true);
    wp_enqueue_style('ks-admin-css', KS_PLUGIN_URL . '/css/' . KS_PREFIX . 'admin.css', false, '2014-10-28');
  } elseif ('widgets.php' == $hook ) {
    wp_enqueue_script('ks-admin-widgets-js', KS_PLUGIN_URL . '/js/' . KS_PREFIX . 'widgets_admin.js', array('jquery', 'kf_admin_js'), NULL, true);
    wp_enqueue_style('ks-admin-widgets-css', KS_PLUGIN_URL . '/css/' . KS_PREFIX . 'admin_widgets.css', false, '2014-10-28');
	}
}
add_action('admin_enqueue_scripts', 'ks_admin_js_css');

// Adds the URL path to the utilities dir.
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
  // wp_enqueue_script('jquery-cycle', KS_PLUGIN_URL . '/js/jquery-cycle2.js', array('jquery'));
  wp_enqueue_style('ks-main-css', KS_PLUGIN_URL . '/css/' . KS_PREFIX . 'main.css', false, '2014-10-28');

}
add_action('wp_enqueue_scripts', 'ks_scripts_and_styles');


// Add shortcode
add_shortcode('kwik_slider', 'kwik_slider_shortcode');
function kwik_slider_shortcode($attr){
  return get_slider($attr['slider_id']);
}
// TODO: add "Insert Slider" button to the editor.


function get_slider($slider_id){
  $output = '';
  $inputs = new KwikInputs();
  $kwik_slides = get_post_meta($slider_id, '_ks_slides');
  $kwik_slides = $kwik_slides[0];
  $ks_settings = get_post_meta($slider_id, '_ks_slider_settings');
  $ks_settings = $ks_settings[0];
  $ks_pager_settings = get_post_meta($slider_id, '_ks_pager_settings');
  $ks_pager_settings = $ks_pager_settings[0];
  wp_enqueue_style('ks-admin-widgets-css', KS_PLUGIN_URL . '/css/themes/'.$ks_settings['theme'].'.css', false, '2014-10-28');

  if(!empty($kwik_slides)){
    $ks_pager_settings['theme'] = $ks_settings['theme'];
    if(isset($ks_settings['slide_size'])){
      $ks_pager_settings['slide_size'] = $ks_settings['slide_size'];
    }
    $slider_style = slider_style($kwik_slides, $ks_pager_settings);

    foreach ($kwik_slides as $i => $v){
      $output .= get_slide($v, $i);
    }
    $slider_settings = format_slider_settings($ks_settings, $slider_id);

  } else {
    $no_slides_content = array(
      'title' => $inputs->markup('h2', __('No Slides', 'kwik')),
      'text'  => $inputs->markup('p', __('This slider currently does not have any slides.', 'kwik')),
      'edit'  => $inputs->markup('a', __('Add Slides', 'kwik'), array('href' => get_edit_post_link( $slider_id), 'target'=>'_blank', 'title'=>__('Edit this slider', 'kwik')))
      );
    $output .= $inputs->markup('div', $no_slides_content, array('class'=>'no_slides'));
  }

  $output = $inputs->markup('div', $output, $slider_settings);
    $output .= get_pager($kwik_slides, $ks_pager_settings, $slider_id);

  return $inputs->markup('div', $slider_style.$output, array('class'=>'ks_slider_wrap'));
}

function get_slide($slide_id, $i=0){
  $inputs = new KwikInputs();
  $slide = get_post( $slide_id );

  $subtitle_val   =  get_post_meta($slide->ID,  '_slide_subtitle', true) ;
  $learnmore_val  =  get_post_meta($slide->ID,  '_slide_learnmore', true);
  $link_val       =  get_post_meta($slide->ID,  '_slide_link', true);
  $img_val        =  get_post_thumbnail_id( $slide->ID );
  $title_val      =  $slide->post_title;

  $img = get_the_post_thumbnail($slide->ID, 'kwik_slider');
  if($link_val['url']){
    $link_attrs = array("href" => $link_val[url], "target" => $link_val[target], "title" => $title_val);
    $img = $inputs->markup('a', $img, $link_attrs);
    $link_attrs['class'] = 'full_slide_link';
    $full_slide_link = $inputs->markup('a', NULL, $link_attrs);
    unset($link_attrs['class']);
    $title_val = $inputs->markup('a', $title_val, $link_attrs);
  } else {

  }

  $slide_info = array(
    'title' => $inputs->markup('h2', $title_val),
  );

  if(isset($subtitle_val)){
    $slide_info['subtitle'] = $inputs->markup('p', $subtitle_val);
  }

  $slide_contents = array(
    'full_slide_link' => isset($full_slide_link) ? $full_slide_link : NULL,
    'img' => $img,
    'slide_info' => $inputs->markup('div', $slide_info, array('class'=>'ks_slide_info'))
  );


  $slide_wrap = $inputs->markup('div', $slide_contents, array("class" => array("ks_slide", "ks_slide_".$i)));

  return $slide_wrap;
}

function format_slider_settings($ks_settings, $slider_id){
  $slider_settings = array();
  foreach ($ks_settings as $sk => $sv){
    $slider_settings['data-cycle-'.$sk] = $sv;
  }

  $slider_settings['data-cycle-slides'] = '.ks_slide';
  $slider_settings['data-cycle-pager'] = '#ks_pager-'.$slider_id;
  $slider_settings['data-cycle-pager-template'] = '';
  $slider_settings['data-cycle-log'] = 'false';

  $slider_settings['class'] = array(
    'ks_slider',
    'cycle-slideshow',
    'clear'
  );

  return $slider_settings;
}


function get_pager($slides, $pager_settings, $slider_id){
  $inputs = new KwikInputs();
  $output = '';

  foreach ($slides as $i => $v){
    $pager_attrs = array();
    $pager_attrs['class'] = array(
      'slide-index',
      'slide-index-'.$i
      );
    $output .= $inputs->markup('span', NULL, $pager_attrs);
  }

  $output = $inputs->markup('div', $output, array("class" => "ks_pager style-".$pager_settings['style'], "id" => "ks_pager-".$slider_id));


  return $output;
}

// TODO: refactor the below ugly code. maybe build a stylesheet generator
function slider_style($slides, $settings){
  $inputs = new KwikInputs();
  $output = '';
  $output .= '.ks_pager{';
  $output .= $settings['position'];
  $output .= '}';

  $output .= '.ks_slider{';
    // if(!empty($settings['slide_size'])){
    //   $output .= 'width: '.$settings['slide_size']['width'].'px;';
    //   $output .= 'height: '.$settings['slide_size']['height'].'px;';
    // }
  $output .= '}';

  if($settings['style']==='circle'){
    $output .= '.ks_pager.style-circle span{';
      $output .= '-webkit-border-radius: '.(intval($settings['pager_size']['width'])/2).'px;';
      $output .= 'border-radius: '.(intval($settings['pager_size']['width'])/2).'px;';
    $output .= '}';
  }

  $output .= '.ks_pager .slide-index{';
    $output .= 'background-color: '.$settings['pager_color'].';';
    $output .= 'width: '.$settings['pager_size']['width'].'px;';
    $output .= 'height: '.$settings['pager_size']['height'].'px;';
    $output .= 'margin: '.(intval($settings['pager_spacing'])/2).'px;';
  $output .= '}';
  $output .= '.ks_pager .cycle-pager-active{';
    $output .= 'background-color: '.$settings['pager_color_active'].';';
  $output .= '}';


  foreach ($slides as $i => $v){
    $output .= '.slide-index-'.$i.'{';
    if($settings['style']==='image'){
      $img = get_the_post_thumbnail($v, 'thumbnail');
      $output .= 'background-image: url('.$img.');';
      $output .= 'background-repeat: no-repeat;';
      $output .= 'background-position: 50% 50%;';
      $output .= 'background-size: 100% 100%;';
    }
    $output .= '}';

  }

  return $inputs->markup('style', $output, array('type' => 'text/css', 'scoped' => NULL));
}
