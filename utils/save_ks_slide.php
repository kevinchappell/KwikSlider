<?php

define('WP_USE_THEMES', false);
include $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

function save_ks_slide($post)
{

    global $user_ID;

    $ks_slide = array(
        'post_title' => htmlentities($post['ks_slide_title']),
        'post_content' => '',
        'post_status' => 'publish',
        'post_author' => $user_ID,
        'post_type' => 'ks_slide',
    );

    if ($post["ks_slide_id"] !== "" && !is_null($post["ks_slide_id"])) {
        $ks_slide["ID"] = $post["ks_slide_id"][0];
    }

    // Insert the post into the database
    $post_id = wp_insert_post($ks_slide);

    if ($post["ks_slide_img"] != "") {
        set_post_thumbnail($post_id, $post["ks_slide_img"]);
    }

    $ks_slide_meta = array(
        '_slide_subtitle' => $post[KS_PREFIX . 'slide_subtitle'],
        '_slide_link' => $post[KS_PREFIX . 'slide_link'],
        '_slide_learnmore' => $post[KS_PREFIX . 'slide_learnmore'],
    );

    // Add values of $ks_slide_meta as custom fields
    foreach ($ks_slide_meta as $key => $value) {
        if ($post->post_type == 'revision') {
            return;
        }

        KwikUtils::update_meta($post_id, $key, $value);
    }

    $json = array(
        'slide_id' => $post_id,
        'message' => __('Slide saved', 'kwik'),
    );

    header("Content-type: application/json");
    echo json_encode($json);
}

if (current_user_can('edit_post', $_POST["post_ID"])) {
    save_ks_slide($_POST);
} else {
    echo "no!";
}
