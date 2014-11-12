<?php

add_filter('gettext', 'ks_slider_text_filter', 20, 3);
/*
 * Change the text in the admin for my custom post type
 *
**/
function ks_slider_text_filter( $translated_text, $untranslated_text, $domain ) {
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