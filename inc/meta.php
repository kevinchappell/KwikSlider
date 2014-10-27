<?php

// Add the kwik slider meta box
function add_ks_metaboxes() {
  add_meta_box('ks_meta', 'Slide Details', 'ks_meta', 'kwik_slider', 'side', 'default');
  add_meta_box('ks_slides', 'Slides', 'ks_slides', 'kwik_slider', 'normal', 'core');
}

// Slider settings for themes, colors and other settings
function ks_meta() {
  global $post;
  $inputs = new KwikInputs();

  // Noncename for security check on data origin
  $ks_meta .= $inputs->nonce(KS_PLUGIN_BASENAME.'_nonce', wp_create_nonce(plugin_basename(__FILE__)));

  // Get the current data
  $ks_slide_link = get_post_meta($post->ID, 'ks_slide_link', true);
  $ks_slide_link_target = get_post_meta($post->ID, 'ks_slide_link_target', true);
  $ks_learn_more = get_post_meta($post->ID, 'ks_learn_more', true);

  // meta fields
  $ks_meta = $inputs->text('ks_slide_link', $ks_slide_link, __('Link', 'kwik'));
  $ks_meta .= $inputs->select('ks_slide_link_target', $ks_slide_link_target, $inputs->target(), __('Target'));
  $ks_meta .= $inputs->text('ks_learn_more', $ks_learn_more, __('Learn More Text', 'kwik'));
  echo $ks_meta;
}

// Drag and drop slide configuration for `kwik_slider` post type
// allows you to create and edit slides (`kwik_slide`)
function ks_slides() {
    global $post;
  $inputs = new KwikInputs();
  $kwik_slides = get_post_meta($post->ID, '_page_meta_boxes', false);
  $kwik_slides = $kwik_slides[0];

  $output = '';
    // Noncename for security check on data origin
    $output .= $inputs->nonce(KS_PLUGIN_BASENAME.'_nonce', wp_create_nonce(plugin_basename(__FILE__)));
// $output .= $inputs->markup('span', __('Add Slide', 'kwik'), (object)array('id'=>'add_box_btn'));


$slide_edit_template = '';
$title_val = get_post( $kwik_slide );
$title_val = (!empty($subtitle) ? $subtitle : "");

// toolbar
$move_slide           = $inputs->markup('span', '&varr;', array('class'=>'move_slide', 'title'=> __('Re-order Slide', 'kwik')));
$clone_slide          = $inputs->markup('span', '',    array('class'=>'clone_slide dashicons-plus', 'title'=>__('Clone Slide', 'kwik')));
$remove_slide         = $inputs->markup('span', '',   array('class'=>'remove_slide dashicons-minus', 'title'=>__('Remove Slide', 'kwik')));
$clear_slide          = $inputs->markup('span', '',    array('class'=>'clear_slide dashicons-trash', 'title'=>__('Clear Slide', 'kwik')));
$toolbar              = $inputs->markup('div', $move_slide.$clone_slide.$remove_slide.$clear_slide, array('class'=>'ks_slide_toolbar dashicons'));

//slide settings
$title_input          = $inputs->text(KS_PREFIX.'slide_title', '', '',        array('placeholder'=>__('Title','kwik')));
$subtitle_input       = $inputs->text(KS_PREFIX.'slide_subtitle', '', '',     array('placeholder'=>__('Subtitle/Caption','kwik')));
$learnmore_input      = $inputs->text(KS_PREFIX.'slide_learnmore', '', '',    array('placeholder'=>__('Learn More','kwik')));
$link_input           = $inputs->link(KS_PREFIX.'slide_link', '', '',         array('placeholder'=>__('Link','kwik')));
$img_input            = $inputs->img(KS_PREFIX.'slide_img', '', '',           array('class'=>'dashicons slide_img_prev'));
$slide_id_input       = $inputs->input(array( 'name'=> KS_PREFIX.'slide_id',  'type'=>'hidden'));

$slide_inputs =
  $slide_id_input.
  $title_input.
  $subtitle_input.
  $learnmore_input.
  $link_input;

$slide_details = $inputs->markup('div', $slide_inputs, array('class'=>'slide_details'));
$slide_template = $inputs->markup('li', $img_input.$slide_details.$toolbar,array('id'=>'kcdl_box_template', 'class'=>'slide_edit'));

$output .= '<ul id="ks_slide_meta" class="clear">';
  // $output .= '<div class="page_meta meta_wrap">';


$output .= $slide_template;

  // if(!empty($kwik_slides)){


  //   foreach ($kwik_slides as $kwik_slide){

  //     $box = get_post( $kwik_slide );
  //     $subtitle = get_post_meta($box->ID, '_subtitle', true);
  //     $link = get_post_meta($box->ID, '_link', true);
  //     $continue = get_post_meta($box->ID, '_continue', true);
  //     $img_id = get_post_thumbnail_id( $box->ID );

  //     $output .= '<li class="slide_edit">
  //       <header class="hb_header"><input placeholder="'.__('Title','kwik').'" value="'.$box->post_title.'" name="kcdl_box_ttl" class="h3"/><input type="hidden" value="'.$box->ID.'" name="kcdl_box_id[]" class="kcdl_box_id"/><input type="hidden" value="'.(!empty($img_id) ? $img_id : "").'" name="kcdl_box_img_id" class="kcdl_box_img_id"/>
  //         <input placeholder="'.__('Enter subtitle here','kwik').'" value="'.(!empty($subtitle) ? $subtitle : "").'" name="kcdl_box_subtitle" class="subtitle"/> <input placeholder="'.__('Learn More &gt;','kwik').'" value="'.(!empty($continue) ? $continue : "").'" name="kcdl_box_learn_more" class="learn_more"/></h4>
  //       </header><div class="box_link_wrap"><input type="text" class="box_link" value="'.(!empty($link) ? $link : "").'" name="box_link" /></div>
  //       <div class="img_prev_wrap"><div class="img_prev">'.get_the_post_thumbnail($box->ID, 'thumbnail', array("class"=>"img_prev")).'</div><span class="clear_img tooltip" title="Remove Box">×</span></div>
  //     </li>';
  //   }

  // } else {

  //     $output .= '<li id="kcdl_box_btn_wrap" style="clear:both"><strong id="add_kcdl_box">'.__('Add Meta Box','kwik').'</strong></li>';

  // }// is_array

$output .= '</ul>';

  // $output .= '</div>';

  echo  $output;

}



// Save the Metabox Data
function save_ks_meta($post_id, $post) {
  if ($post->post_type == 'kwik_slider') {
    $options = get_option(KS_PLUGIN_SETTINGS);
    // make sure there is no conflict with other post save function and verify the noncename
    if (isset($_POST[KS_PLUGIN_BASENAME.'_nonce']) && !wp_verify_nonce($_POST[KS_PLUGIN_BASENAME.'_nonce'], plugin_basename(__FILE__))) {
      return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID)) {
      return $post->ID;
    }

    if ($post->post_status != 'auto-draft') {
      $ks_meta = array(
        'ks_slide_link' => wp_filter_nohtml_kses($_POST['ks_slide_link']),
        'ks_slide_link_target' => wp_filter_nohtml_kses($_POST['ks_slide_link_target']),
        'ks_learn_more' => wp_filter_nohtml_kses($_POST['ks_learn_more'])
      );
      // Add values of $ks_meta as custom fields
      foreach ($ks_meta as $key => $value) {
        if ($post->post_type == 'revision') {return;
        }

        __update_post_meta($post->ID, $key, $value);
      }
    } else {
      return;
    }
  } else {
    return;
  }
}
add_action('save_post', 'save_ks_meta', 1, 2);