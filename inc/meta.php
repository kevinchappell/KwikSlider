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
  $kwik_slides = get_post_meta($post->ID, '_ks_slides', false);
  $kwik_slides = $kwik_slides[0];

  // var_dump($kwik_slides);

  $output = '';
  // Noncename for security check on data origin
  $output .= $inputs->nonce(KS_PLUGIN_BASENAME.'_nonce', wp_create_nonce(plugin_basename(__FILE__)));

  $output .= '<ul id="ks_slide_meta" class="kf_form clear" ks-location="'.KS_PLUGIN_URL.'">';

  if(!empty($kwik_slides)){
    foreach ($kwik_slides as $kwik_slide_id){
      $output .= get_slide_inputs($kwik_slide_id);
    }
  } else {
    $output .= get_slide_inputs();
  }// is_array

  echo  $output;
}

function get_slide_inputs($slide_id = NULL){
  $inputs = new KwikInputs();

  // current slide
  $slide = get_post( $slide_id );

  $subtitle_val   =  get_post_meta($slide->ID,  '_slide_subtitle', true) ;
  $learnmore_val  =  get_post_meta($slide->ID,  '_slide_learnmore', true);
  $link_val       =  get_post_meta($slide->ID,  '_slide_link', true);
  $img_val        =  get_post_thumbnail_id( $slide_id );
  $title_val      =  $slide_id ? $slide->post_title : '';


  //slide settings
  $slide_inputs = array(
    'title_input' =>      $inputs->text(KS_PREFIX.'slide_title',    $title_val,     NULL, array('placeholder'=>__('Title','kwik'))),
    'subtitle_input' =>   $inputs->text(KS_PREFIX.'slide_subtitle', $subtitle_val,  NULL, array('placeholder'=>__('Subtitle/Caption','kwik'))),
    'learnmore_input' =>  $inputs->text(KS_PREFIX.'slide_learnmore',$learnmore_val, NULL, array('placeholder'=>__('Learn More','kwik'))),
    'link_input' =>       $inputs->link(KS_PREFIX.'slide_link',     $link_val,      NULL, array('placeholder'=>__('Link','kwik'))),
    'slide_id_input' =>   $inputs->input(array( 'value'=> $slide_id, 'name'=> KS_PREFIX.'slide_id[]',  'type'=>'hidden', 'class' => 'ks_slide_id'))
    );

  // slide_edit layout
  $slide_content = array(
    'img_input'     => $inputs->img(KS_PREFIX.'slide_img', $img_val, NULL, array('img_size'=>'medium')),
    'slide_details' => $inputs->markup('div', $slide_inputs, array('class'=>'slide_details')),
    'footer'        => $inputs->markup('div', array(
      'toolbar'=> get_slide_toolbar(),
      'message' => $inputs->markup('div', NULL, array('class' => 'slide_messages'))
      ), array('class' => 'slide_footer'))
    );

  return $inputs->markup('li', $slide_content, array('class'=>'clear slide_edit'));
}

function get_slide_toolbar(){
  $inputs = new KwikInputs();
  $buttons = array(
    'move_slide'   => $inputs->markup('span', '&varr;',     array('class'=>'move_slide', 'title'=> __('Re-order Slide', 'kwik'))),
    'clone_slide'  => $inputs->markup('span', '',           array('class'=>'clone_slide dashicons-plus', 'title'=>__('Clone Slide', 'kwik'))),
    'remove_slide' => $inputs->markup('span', '',           array('del-confirm' => __('Remove slide?', 'kwik'), 'class'=>'remove_slide dashicons-trash', 'title'=>__('Remove Slide', 'kwik'))),
    'save_slide'   => $inputs->markup('span', '', array('class'=>'dashicons-yes save_slide', 'title'=>__('Save Slide', 'kwik')))
    );

  return $inputs->markup('div', $buttons, array('class'=>'ks_slide_toolbar dashicons'));
}

// Save the Metabox Data
function save_ks_meta($post_id, $post) {
  if ($post->post_type == 'kwik_slider') {

    // make sure there is no conflict with other post save function and verify the noncename
    if (isset($_POST[KS_PLUGIN_BASENAME.'_nonce']) && !wp_verify_nonce($_POST[KS_PLUGIN_BASENAME.'_nonce'], plugin_basename(__FILE__))) {
      return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID)) {
      return $post->ID;
    }

    // if ($post->post_status != 'auto-draft') {

      $slide_ids = $_POST[KS_PREFIX.'slide_id'];

      //TODO add meta validation

      $ks_slides = array(
        '_ks_slides' => $slide_ids,
        '_ks_slider_settings' => wp_filter_nohtml_kses($_POST['ks_slider_settings'])
      );

      // Add values of $ks_slides as custom fields
      foreach ($ks_slides as $key => $value) {
        if ($post->post_type == 'revision') {
          return;
        }
        __update_post_meta($post->ID, $key, $value);
      }
    // } else {
    //   return;
    // }
  } else {
    return;
  }
}

add_action('save_post', 'save_ks_meta', 1, 2);
