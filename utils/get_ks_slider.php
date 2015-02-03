<?php

// @todo refactor this into proper Utils class

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
      $slider_settings = get_post_meta($result->slider_id, "_ks_slider_settings");
      $slider_settings = $slider_settings[0];
      $kwik_slides = get_post_meta($result->slider_id, "_ks_slides");
      $kwik_slides = $kwik_slides[0];

      $json = array();
      $json['label'] =        $result->name;
      $json['id'] =           $result->slider_id;
      $json['theme'] =        $slider_settings[theme];
      $json['slide_count'] =  sizeof($kwik_slides);

      $json['thumbnail'] =    KwikSliderHelpers::get_slider_thumb($result->slider_id);
      $data[] = $json;
    }

    header("Content-type: application/json");
    echo json_encode($data);
  }

  // TODO check if user can edit
  if($_GET['term'] && current_user_can('edit_pages')){
    ks_slide_autocompleter($_GET['term']);
  }
