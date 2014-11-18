<?php

class KS_Admin extends Kwik_Slider{
  private $ks = NULL;
  public function __construct( $ks )  {
    $this->ks = $ks;

    add_action( 'admin_init', array( $this , 'admin_init' ) );
  }

  public function admin_init() {
    // add any JS or CSS for the needed for the dashboard
    add_action('admin_enqueue_scripts', 'ks_admin_js_css');

  }

  public function ks_admin_js_css($hook) {
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
      wp_enqueue_script('kwik-slider-js', KS_PLUGIN_URL . '/js/' . KS_PREFIX . 'admin.js', array('jquery', 'jquery-ui-sortable'), NULL, true);
      wp_enqueue_style('kwik-slider-css', KS_PLUGIN_URL . '/css/' . KS_PREFIX . 'admin.css', false, '2014-10-28');

    } elseif ('edit.php' == $hook && 'kwik_slider' == $screen->post_type) {

    }
  }


} // end KS_Admin