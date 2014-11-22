<?php

/*
 * Change the text in the admin for my custom post type
 *
**/

class KS_MISC {
   function __construct() {
      $this->name = "KS_MISC";
      $this->getSliderThumb = $this->getSliderThumb;
      add_filter('gettext', array($this, 'ks_slider_text_filter'), 20, 3);
   }

   function __destruct() {
      // do garbage cleanup here if needed
   }

  private function loadWP(){
    define('WP_USE_THEMES', false);
    include($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
  }

  public function getSliderThumb($slider_id, $slide_index = 0){
    // if(!function_exists('wp_get_attachment_image_src')){
    //   KS_MISC::loadWP();
    // }
    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($slider_id), 'thumbnail' );
    if($thumb){
      $thumb = $thumb[0];
    } else {
      $kwik_slides = get_post_meta($slider_id, '_ks_slides', false)[0];
      $slide_id = intval($kwik_slides[$slide_index]);
      $index = $slide_index+1;
      $thumb = KS_MISC::getSliderThumb($slide_id, $index);
    }
    return $thumb;
  }

  public function ks_slider_text_filter( $translated_text, $untranslated_text, $domain ) {
    global $typenow;

    if( is_admin() && 'kwik_slider' === $typenow )  {

      switch( $untranslated_text ) {

          case 'Insert into post':
            $translated_text = __( 'Set Slider Image','kwik' );
          break;

          case 'Enter title here':
            $translated_text = __( 'Enter Slider title here','kwik' );
          break;

          //add more items
       }
     }
     return $translated_text;
  }
}

// Temporary, to be called by parent class when we have one
new KS_MISC();