<?php

<?php
/**
 * Widget Name: Kwik Contact Widget
 * Description: A tiny contact form block
 * Version: 0.2
 *
 */

add_action( 'widgets_init', 'ks_contact_load_widgets' );

function ks_contact_load_widgets() {
  register_widget( 'KF_Contact_Widget' );
}



class KF_Contact_Widget extends WP_Widget {

  function __construct() {
    $widget_ops = array('classname' => 'contact_widget', 'description' => __('Mini Contact Form'));
    $control_ops = array('width' => 400, 'height' => 350);
    parent::__construct('contact', __('Contact'), $widget_ops, $control_ops);
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
      data-cycle-fx="<?php echo $options['home_slider']['fx']?>"
      data-cycle-speed=<?php echo $options['home_slider']['speed']?>
      data-cycle-timeout=<?php echo $options['home_slider']['delay']?>
      data-cycle-auto-height=container
      data-cycle-swipe=true
      data-cycle-slides="div.slide"
      >';
    $form .= '<form id="ks_contact_widget" name="ks_contact_widget" method="post" enctype="multipart/form-data" action="'.get_bloginfo('template_directory').'/forms/widget_form_processor.php" >';
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
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'conf_message' => 'Thank you for your submission', 'to_email' => get_option('admin_email'), 'cc_email' => '', 'error_message' => 'There was an error submitting your message', 'success_message' => 'Your message was successfully sent.' ) );
    $title = $instance['title'];
    $to_email = $instance['to_email'];
    $cc_email = $instance['cc_email'];
    $conf_message = esc_textarea($instance['conf_message']);
    $error_message = esc_textarea($instance['error_message']);
    $success_message = esc_textarea($instance['success_message']);
?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('to_email'); ?>"><?php _e('To Email:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('to_email'); ?>" name="<?php echo $this->get_field_name('to_email'); ?>" type="text" value="<?php echo esc_attr($to_email); ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('cc_email'); ?>"><?php _e('CC Email:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('cc_email'); ?>" name="<?php echo $this->get_field_name('cc_email'); ?>" type="text" value="<?php echo esc_attr($cc_email); ?>" /></p>

    <textarea class="widefat" rows="4" cols="20" id="<?php echo $this->get_field_id('conf_message'); ?>" name="<?php echo $this->get_field_name('conf_message'); ?>"><?php echo $conf_message; ?></textarea>
        <textarea class="widefat" rows="4" cols="20" id="<?php echo $this->get_field_id('error_message'); ?>" name="<?php echo $this->get_field_name('error_message'); ?>"><?php echo $error_message; ?></textarea>
        <textarea class="widefat" rows="4" cols="20" id="<?php echo $this->get_field_id('success_message'); ?>" name="<?php echo $this->get_field_name('success_message'); ?>"><?php echo $success_message; ?></textarea>

<?php
  }
}

