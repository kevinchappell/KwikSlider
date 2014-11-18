<?php

class Kwik_Slider {
  private $ks_slider = NULL;


  public function __construct( $ks_slider ) {
    $this->ks_slider = $ks_slider;
    define('KS_PLUGIN_BASENAME', basename(dirname( __FILE__ )));
    define('KS_PLUGIN_SETTINGS', preg_replace('/-/', '_', KS_PLUGIN_BASENAME).'_settings');
    define('KS_PLUGIN_URL', untrailingslashit(plugins_url('', __FILE__)));
    define('KS_PLUGIN_PATH', untrailingslashit( dirname( __FILE__ ) ) );
    define('KS_PREFIX', 'ks_' );

    add_action( 'admin_init', array( $this , 'admin_init' ) );
    add_action( 'created_term', array( $this , 'edited_term' ), 5, 3 );
    add_action( 'edited_term', array( $this , 'edited_term' ), 5, 3 );
    add_action( 'delete_term', array( $this , 'delete_term' ), 5, 4 );

    if ( is_admin() ){
      $this->admin();
      $this->yboss();
    }
  }

  // a singleton for the admin object
  public function admin() {
    if ( ! $this->admin ) {
      require_once __DIR__ . '/' .KS_PREFIX. '-admin.php';
      $this->admin = new KS_Admin( $this );
    }
    return $this->admin;
  } // END admin

} // end Kwik_Slider