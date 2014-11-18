<?php

  define('WP_USE_THEMES', false);
  include($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

  function ks_slide_autocompleter($term){

    global $wpdb;

    $input = $term;
    $data = array();
    $table_name = $wpdb->prefix."posts";
    $query = "\n";
    $query .= "SELECT concat( post_title ) name, 1 cnt, ID as slider_id FROM $table_name \n";
    $query .= "WHERE post_status='publish'\n";
    $query .= "AND post_type='kwik_slider'\n";
    // $query .= "AND post_date < NOW()\n";
    $query .= "AND post_title LIKE '%$input%'\n";
    $query .= "ORDER BY post_title\n";
    $query .= "LIMIT 10";

    $query_results = $wpdb->get_results($query, OBJECT);


    foreach ($query_results as $result) {

      $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($row['slider_id']), 'thumbnail' );

      $json = array();
      $json['label'] =        $result->name;
      $json['id'] =           $result->slider_id;
      // $json['subtitle'] =     get_post_meta($result->slider_id, "_slide_subtitle", true);
      // $json['learn_more'] =   get_post_meta($result->slider_id, "_slide_learnmore", true);
      // $json['link'] =         get_post_meta($result->slider_id, "_slide_link", true);
      // $json['thumbnail_id'] = get_post_thumbnail_id( $result->slider_id );


      // var_dump($json['thumbnail_id']);

      $json['thumbnail'] =    getThumbnail($result->slider_id);
// var_dump($json);
      $data[] = $json;
    }

    header("Content-type: application/json");
    echo json_encode($data);
  }

  // TODO check if user can edit
  if($_GET['term'] && current_user_can('edit_pages')){
    ks_slide_autocompleter($_GET['term']);
  }


function getThumbnail($slider_id, $slide_index = 0){
  // var_dump($slider_id);
  $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($slider_id), 'thumbnail' );
  if($thumb){
    return $thumb[0];
  } else {
    $kwik_slides = get_post_meta($slider_id, '_ks_slides', false)[0];
    $slide_id = intval($kwik_slides[$slide_index]);
    $index = $slide_index+1;
    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($slide_id), 'thumbnail' );
    return getThumbnail($slide_id, $index);
  }
}