<?php
/*
Plugin Name: Kwik Slider
Plugin URI: http://kevin-chappell.com/kwik-slider
Description: A slide show plugin perfect for homepage sliders
Author: Kevin Chappell
Version: .5.1
Author URI: http://kevin-chappell.com
 */

/**
 * @todo refactor whole plugin to be OOPHP
 */

define( 'KS_PLUGIN_BASENAME', basename(dirname(__FILE__)));
define( 'KS_PLUGIN_SETTINGS', preg_replace( '/-/', '_', KS_PLUGIN_BASENAME) . '_settings');
define( 'KS_PLUGIN_URL', untrailingslashit(plugins_url( '', __FILE__)));
define( 'KS_PLUGIN_PATH', untrailingslashit(dirname(__FILE__)));
define( 'KS_PREFIX', 'ks_');

include KS_PLUGIN_PATH . '/inc/class.kwik-slider.php';

new KwikSlider();

// Add shortcode
add_shortcode( 'kwik_slider', 'kwik_slider_shortcode');
function kwik_slider_shortcode($attr) {
	return get_slider($attr['slider_id']);
}
// TODO: add "Insert Slider" button to the editor.

function get_slider($slider_id) {
	$output = '';
	$inputs = new KwikInputs();
	$kwik_slides = KwikMeta::get_meta_array($slider_id, 'ks_slides');
	$slider_settings = array(
		'slider' => KwikMeta::get_meta_array($slider_id, 'ks_slider_settings'),
		'pager' => KwikMeta::get_meta_array($slider_id, 'ks_pager_settings'),
	);
	wp_enqueue_style( 'ks-admin-widgets-css', KS_PLUGIN_URL . '/css/themes/' . $slider_settings['slider']['theme'] . '.css', false, '2014-10-28');
	if (!empty($kwik_slides)) {
		$slider_style = slider_style($kwik_slides, $slider_settings);
		foreach ($kwik_slides as $i => $v) {
			$output .= get_slide($v, $i);
		}
		$slider_attrs = format_slider_settings($slider_settings, $slider_id);
		$output = $inputs->markup( 'div', $output, $slider_attrs);
		$output .= get_pager($kwik_slides, $slider_settings['pager'], $slider_id);
	} else {
		$no_slides_content = array(
			'title' => $inputs->markup( 'h2', __( 'No Slides', 'kwik')),
			'text' => $inputs->markup( 'p', __( 'This slider currently does not have any slides.', 'kwik')),
			'edit' => $inputs->markup( 'a', __( 'Add Slides', 'kwik'), array( 'href' => get_edit_post_link($slider_id), 'target' => '_blank', 'title' => __( 'Edit this slider', 'kwik'))),
		);
		$output .= $inputs->markup( 'div', $no_slides_content, array( 'class' => 'no_slides'));
	}

	return $inputs->markup( 'div', $slider_style . $output, array( 'class' => 'ks-slider-wrap'));
}

function get_slide($slide_id, $i = 0) {
	$inputs = new KwikInputs();
	$slide = get_post($slide_id);

	$subtitle_val = get_post_meta($slide->ID, '_slide_subtitle', true);
	$learnmore_val = get_post_meta($slide->ID, '_slide_learnmore', true);
	$link_val = get_post_meta($slide->ID, '_slide_link', true);
	$img_val = get_post_thumbnail_id($slide->ID);
	$title_val = $slide->post_title;
	$link_attrs = array();

	$img = get_the_post_thumbnail($slide->ID, 'kwik_slider');

	if ($link_val['url']) {
		$link_attrs = array("href" => $link_val['url'], "target" => $link_val['target'], "title" => $title_val);
		$img = $inputs->markup( 'a', $img, $link_attrs);
		$link_attrs['class'] = 'full_slide_link';
		// $full_slide_link = $inputs->markup( 'a', null, $link_attrs);
		unset($link_attrs['class']);
		$title_val = $inputs->markup( 'a', $title_val, $link_attrs);
	} else {

	}

	$slide_info = array(
		'title' => $inputs->markup( 'h2', $title_val),
	);

	if (isset($subtitle_val)) {
		$slide_info['subtitle'] = $inputs->markup( 'p', $subtitle_val);
	}

	if ( '' !== $learnmore_val) {
		$link_attrs['class'] = 'learn_more';
		$slide_info['button'] = $inputs->markup( 'a', $learnmore_val, $link_attrs);
	}

	$slide_contents = array(
		// 'full_slide_link' => isset($full_slide_link) ? $full_slide_link : null,
 		'img' => $img,
		'slide_info' => $inputs->markup( 'div', $slide_info, array( 'class' => 'ks_slide_info')),
	);

	$slide_wrap = $inputs->markup( 'div', $slide_contents, array("class" => array("ks_slide", "ks_slide_" . $i)));

	return $slide_wrap;
}

function slide_info_vals($slide_id) {
	$inputs = new KwikInputs();
	$button_val = get_post_meta($slide_id, '_slide_learnmore', true);
	$link_val = get_post_meta($slide_id, '_slide_link', true);

	$slide_info = array(
		'title' => get_the_title(),
		'subtitle' => get_post_meta($slide_id, '_slide_subtitle', true),
		'link' => $link_val,
		'button' => '' !== $button_val ? $inputs->markup( 'a', $button_val) : null,
		'img' => get_the_post_thumbnail($slide_id, 'kwik_slider'),
	);

	return $slide_info;
}

function format_slider_settings($slider_settings, $slider_id) {
	$slider_attrs = array();

	$slider_attrs['data-cycle-speed'] = $slider_settings['slider']['slider_speed'];
	$slider_attrs['data-cycle-timeout'] = $slider_settings['slider']['slider_timeout'];
	$slider_attrs['data-cycle-effect'] = $slider_settings['slider']['slider_effect'];
	$slider_attrs['data-cycle-slides'] = '.ks_slide';
	$slider_attrs['data-cycle-pager'] = '#ks-pager-' . $slider_id;
	$slider_attrs['data-cycle-pager-template'] = '';
	$slider_attrs['data-cycle-log'] = 'false';

	$slider_attrs['class'] = array(
		'ks-slider',
		// 'cycle-slideshow',
 		'clear',
	);

	return $slider_attrs;
}

function get_pager($slides, $pager_settings, $slider_id) {
	$inputs = new KwikInputs();
	$output = '';

	foreach ($slides as $i => $v) {
		$pager_attrs = array();
		$pager_attrs['class'] = array(
			'slide-index',
			'slide-index-' . $i,
		);
		$output .= $inputs->markup( 'span', null, $pager_attrs);
	}

	$output = $inputs->markup( 'div', $output, array("class" => "ks-pager style-" . $pager_settings['style'], "id" => "ks-pager-" . $slider_id));

	return $output;
}

// TODO: refactor the below ugly code. maybe build a stylesheet generator
function slider_style($slides, $settings) {
	$inputs = new KwikInputs();
	$output = '';
	$output .= '.ks-pager{';
	$output .= $settings['pager']['position'];
	$output .= '}';

	$output .= '.ks-slider{';
	if (!empty($settings['slider']['slide_size'])) {
		$output .= 'width: ' . $settings['slider']['width'] . 'px;';
		$output .= 'height: ' . $settings['slider']['height'] . 'px;';
	}
	$output .= '}';

	if ( 'circle' === $settings['pager']['style']) {
		$radius = (intval($settings['pager']['width']) / 2);
		$output .= '.ks-pager.style-circle span{';
		$output .= '-webkit-border-radius: ' . $radius . 'px;';
		$output .= 'border-radius: ' . $radius . 'px;';
		$output .= '}';
	}

	$output .= '.ks-pager .slide-index{';
	$output .= 'background-color: ' . $settings['pager']['pager_color'] . ';';
	$output .= 'width: ' . $settings['pager']['width'] . 'px;';
	$output .= 'height: ' . $settings['pager']['height'] . 'px;';
	$output .= 'margin: ' . (intval($settings['pager']['pager_spacing']) / 2) . 'px;';
	$output .= '}';
	$output .= '.ks-pager .cycle-pager-active{';
	$output .= 'background-color: ' . $settings['pager']['pager_color_active'] . ';';
	$output .= '}';
	$output .= '.ks_slide_info .learn_more{';
	$output .= 'background-color: ' . $settings['pager']['pager_color'] . ';';
	$output .= '}';
	$output .= '.ks_slide_info .learn_more:hover{';
	$output .= 'background-color: ' . $settings['pager']['pager_color_active'] . ';';
	$output .= '}';

	foreach ($slides as $i => $v) {
		$output .= '.slide-index-' . $i . '{';
		if ( 'image' === $settings['pager']['style']) {
			$img = get_the_post_thumbnail($v, 'thumbnail');
			$output .= 'background-image: url( ' . $img . ');';
			$output .= 'background-repeat: no-repeat;';
			$output .= 'background-position: 50% 50%;';
			$output .= 'background-size: 100% 100%;';
		}
		$output .= '}';

	}

	return $inputs->markup( 'style', $output, array( 'type' => 'text/css', 'scoped' => null));
}
