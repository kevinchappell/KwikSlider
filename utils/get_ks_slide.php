<?php

  define('WP_USE_THEMES', false);
  include($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

  function ks_slide_autocompleter($term){

    global $wpdb;

    // for dev, remove from production
    // $servername = "localhost";
    // $username = "kevin_db";
    // $password = "1avamarie";

    // Create connection
    // $conn = mysqli_connect($servername, $username, $password);

    $input = $term;
    $data = array();
    $table_name = $wpdb->prefix."posts";
    $query = "\n";
    $query .= "SELECT concat( post_title ) name, 1 cnt, ID as slide_id FROM $table_name \n";
    $query .= "WHERE post_status='publish'\n";
    $query .= "AND post_type='ks_slide'\n";
    // $query .= "AND post_date < NOW()\n";
    $query .= "AND post_title LIKE '%$input%'\n";
    $query .= "ORDER BY post_title\n";
    $query .= "LIMIT 10";



    // $query_results = mysql_query($query);
    $query_results = $wpdb->get_results($query, OBJECT);

    // $query_results = $conn->query('SELECT * FROM "kbc_posts" LIMIT 0 , 30');
    foreach ($query_results as $result) {

      $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($row['slide_id']), 'thumbnail' );

      $json = array();
      $json['label'] = $result->name;
      $json['id'] = $result->slide_id;
      $json['subtitle'] = get_post_meta($result->slide_id, "_slide_subtitle", true);
      $json['learn_more'] = get_post_meta($result->slide_id, "_slide_learnmore", true);
      $json['link'] = get_post_meta($result->slide_id, "_slide_link", true);
      $json['thumbnail_id'] = get_post_thumbnail_id( $result->slide_id );
      $json['thumbnail'] = $thumb[0];

      $data[] = $json;
    }

    header("Content-type: application/json");
    echo json_encode($data);
  }

  // TODO check if user can edit
  if($_GET['term'] && current_user_can('edit_pages')){
    ks_slide_autocompleter($_GET['term']);
  }

