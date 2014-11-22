<?php
/**
 * Widget Name: Kwik Slider
 * Description: The Kwik Slider Widget
 * Version: 0.1
 *
 */

add_action( 'widgets_init', 'ks_load_widget' );

function ks_load_widget() {
  register_widget( 'KS_Slider_Widget' );
}

class KS_Slider_Widget extends WP_Widget {

  function __construct() {
    $widget_ops = array('classname' => 'slider_widget', 'description' => __('Kwik Sldiers for your sidebars'));
    $control_ops = array('height' => 350, 'id_base' => 'kwik_slider' );
    parent::__construct('kwik_slider', __('Kwik Slider'), $widget_ops, $control_ops);
  }

  function widget( $args, $instance ) {
    extract($args);

    echo $before_widget;

    $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

    if(!empty($title)) echo $before_title . $title . $after_title;
    $slider = get_slider($instance['slider_id']);

    echo $slider;

    echo $after_widget;
  }

  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['slider_id'] = strip_tags($new_instance['slider_id']);
    $instance['slider'] = $new_instance['slider'];

    return $instance;
  }

  function form( $instance ) {
    $inputs = new KwikInputs();
    $defaults = array(
      'title' => '',
      'slider' => '',
      'slider_id'=> '' );
    $output = '';

    //get thumbnail
    $slider_id = intval($instance['slider_id']);

    $kwik_slides = get_post_meta($slider_id, '_ks_slides', false)[0];
    $theme = get_post_meta($slider_id, '_ks_slider_settings', false)[0][theme];

    $slide_index = 0;
    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($kwik_slides[$slide_index]), 'kwik_slider' );

    $instance = wp_parse_args( (array) $instance, $defaults);

    $output .= $inputs->text($this->get_field_name('title'), $instance['title'], __('Title:', 'kwik'), array("id" => $this->get_field_id('title'), "class" => "widefat"));
    $output .= $inputs->text($this->get_field_name('slider'), get_the_title($instance['slider_id']), __('Slider:', 'kwik'), array("id" => $this->get_field_id('slider'), "class" => "widefat ks_ac"));
    $output .= $inputs->text($this->get_field_name('slider_id'), $instance['slider_id'], NULL, array("id" => $this->get_field_id('slider_id'), "class" => "widefat ks_slide_id", "type" => "hidden"));
    $label = $inputs->markup('label', __('Preview','kwik'));
    $hide = $slider_id ? '' : 'hide';
    $preview = $inputs->markup('img', NULL, array("class" => "slider_preview", "src" => $thumb[0]));
    $preview_class = array(
      'slider_preview_wrap',
      'ks_theme_'.$theme,
      $hide
      );
    $output .= $inputs->markup('div', $label.$preview, array('class'=>$preview_class,));

    echo $output;

  }

}

