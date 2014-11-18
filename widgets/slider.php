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
    $success = apply_filters( 'widget_success', empty( $instance['success_message'] ) ? '' : $instance['success_message'], $instance );
    $error = apply_filters( 'widget_error', empty( $instance['error_message'] ) ? '' : $instance['error_message'], $instance );

    if(!empty($title)) echo $before_title . $title . $after_title;


    $form = '';
    $slider = '';
    $slider .= '
    <div class="cycle-slideshow"
      data-cycle-fx="<?php echo $options[\'home_slider\'][\'fx\']?>"
      data-cycle-speed=<?php echo $options[\'home_slider\'][\'speed\']?>
      data-cycle-timeout=<?php echo $options[\'home_slider\'][\'delay\']?>
      data-cycle-auto-height=container
      data-cycle-swipe=true
      data-cycle-slides="div.slide"
      >';
    $form .= '<form id="ks_slider_widget" name="ks_slider_widget" method="post" enctype="multipart/form-data" action="'.get_bloginfo('template_directory').'/forms/widget_form_processor.php" >';
    $form .= '<input type="text" class="text_field" name="user_name" placeholder="'.__('Name','op').'" id="user_name" />';
    $form .= '<input type="text" class="text_field" name="user_phone" placeholder="'.__('Phone','op').'" id="user_phone" />';
    $form .= '<input type="text" class="text_field" name="user_email" placeholder="'.__('Email','op').'" id="user_email" />';
    $form .= '<textarea placeholder="'.__('Message','op').'" name="user_message"></textarea>';
    $form .= '<input type="hidden" name="url_main" value="'. currentPageURL() .'" />';
        $form .= '<input type="hidden" name="user_ip" value="'. getRealIp() .'" />';
    $form .= '<div class="inner"><span class="arrow"></span><input type="submit" name="user_submit" id="user_submit" value="'.__('Submit','op').'"></div>';
    $form .= '</form>';
    $form .= '<div id="ks_contact_error" class="form_message error_message">'.__($error,'op').'</div>';
    $form .= '<div id="ks_contact_success" class="form_message success_message">'.__($success,'op').'</div>';
    $form .= '<div id="ks_contact_warning" class="form_message warning_message"></div>';

    echo $form;

    echo $after_widget;
  }

  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['to_email'] = $new_instance['to_email'];
    $instance['cc_email'] = $new_instance['cc_email'];
    $instance['conf_message'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['conf_message']) ) ); // wp_filter_post_kses() expects slashed
    $instance['error_message'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['error_message']) ) );
    $instance['success_message'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['success_message']) ) );

    return $instance;
  }

  function form( $instance ) {
    $inputs = new KwikInputs();
    $output = '';

    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'slider' => '' ) );

    $output .= $inputs->text($this->get_field_name('title'), $instance['title'], __('Title:', 'kwik'), array("id" => $this->get_field_id('title'), "class" => "widefat"));
    $output .= $inputs->text($this->get_field_name('slider'), $instance['slider'], __('Slider:', 'kwik'), array("id" => $this->get_field_id('slider'), "class" => "widefat ks_ac"));
    // $preview = $inputs->markup('span', )
    $output .= $inputs->markup('div', NULL, array('class'=>'slider_preview'));

    echo $output;

  }
}

