<?php

  // define('WP_USE_THEMES', false);
  include($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

  function save_ks_slide($post){

    // global $wpdb;
    global $user_ID;

    $ks_slide = array(
             'post_title' => htmlentities($post['ks_slide_ttl']),
           'post_content' => '',
           'post_status' => 'publish',
           'post_author' => $user_ID,
           'post_type' => 'ks_slide'
        );

    if($post["ks_slide_id"] !== ""){
      $ks_slide["ID"] = $post["ks_slide_id"][0];
    }

    // Insert the post into the database
    $post_id = wp_insert_post( $ks_slide );

    if($post["ks_slide_img_id"] != ""){
      set_post_thumbnail( $post_id, $post["ks_slide_img_id"]);
    }

    $ks_slide_meta = array(
      '_subtitle' => $post['ks_slide_subtitle'],
      '_link' => $post['box_link'],
      '_continue' => $post['ks_slide_learn_more']
      );

      // Add values of $ks_slide_meta as custom fields
      foreach ($ks_slide_meta as $key => $value) {
          if( $post->post_type == 'revision' ) return;
          __update_post_meta( $post_id, $key, $value );
      }

      echo $post_id;

  }


  if (current_user_can('edit_post', $_POST["ks_slide_id"])) {
        save_ks_slide($_POST);
    } else {
      echo "no!";
    }