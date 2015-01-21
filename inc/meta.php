<?php

// Add the kwik slider meta box
function add_ks_meta_boxes()
{
    add_meta_box('slider_settings', 'Slider Settings', 'slider_settings', 'kwik_slider', 'side', 'default');
    add_meta_box('pager_settings', 'Pager Settings', 'pager_settings', 'kwik_slider', 'side', 'default');
    add_meta_box('ks_slides', 'Slides', 'ks_slides', 'kwik_slider', 'normal', 'core');
}

// Slider settings for themes, colors and other settings
function slider_settings()
{
    global $post;
    $inputs = new KwikInputs();
    $options = ks_get_options();
    $defaults = ks_default_options();
    $ks_meta = '';

    // Noncename for security check on data origin
    $ks_meta .= $inputs->nonce(KS_PLUGIN_BASENAME . '_nonce', wp_create_nonce(plugin_basename(__FILE__)));

    // Get the current data
    $ks_slider_settings = get_post_meta($post->ID, '_ks_slider_settings');
    $ks_slider_settings = $ks_slider_settings[0];

    $ks_slider_fx = $ks_slider_settings[fx] ? $ks_slider_settings[fx] : $options['transition_effect'];
    $ks_slider_speed = $ks_slider_settings[speed] ? $ks_slider_settings[speed] : $options['transition_speed'];
    $ks_slider_timeout = $ks_slider_settings[timeout] ? $ks_slider_settings[timeout] : $options['transition_timeout'];
    $ks_slider_size = $ks_slider_settings[slide_size] ? $ks_slider_settings[slide_size] : $options['slide_size'];
    $ks_slider_theme = $ks_slider_settings[theme] ? $ks_slider_settings[theme] : $options['theme'];

    $ks_meta .= $inputs->select('ks_slider_settings[fx]', $ks_slider_fx, __('Effect', 'kwik'), null, $defaults['behavior']['settings']['transition_effect']['options']);
    $ks_meta .= $inputs->spinner('ks_slider_settings[speed]', $ks_slider_speed, __('Speed', 'kwik'), array('max' => '2000', 'min' => '0'));
    $ks_meta .= $inputs->spinner('ks_slider_settings[timeout]', $ks_slider_timeout, __('Delay', 'kwik'), array('max' => '12000', 'min' => '0'));
    $ks_meta .= $inputs->markup('label', __('Slider Size'));
    // $ks_meta .= $inputs->multi('ks_slider_settings[slide_size]', $ks_slider_size, $defaults['appearance']['settings']['slide_size']);
    $ks_meta .= $inputs->select('ks_slider_settings[theme]', $ks_slider_theme, __('Theme', 'kwik'), null, $defaults['appearance']['settings']['theme']['options']);

    echo $ks_meta;
}

function pager_settings()
{
    global $post;
    $inputs = new KwikInputs();
    $options = ks_get_options();
    $defaults = ks_default_options();
    $output = '';

    // Noncename for security check on data origin
    $output .= $inputs->nonce(KS_PLUGIN_BASENAME . '_nonce', wp_create_nonce(plugin_basename(__FILE__)));

    // Get the current data
    $ks_pager_settings = get_post_meta($post->ID, '_ks_pager_settings');
    $ks_pager_settings = $ks_pager_settings[0];

    $ks_pager_style = $ks_pager_settings[style] ? $ks_pager_settings[style] : $options['pager_style'];
    $ks_pager_speed = $ks_pager_settings[fx] ? $ks_pager_settings[fx] : $options['transition_fx'];
    $ks_pager_position = $ks_pager_settings[position] ? $ks_pager_settings[position] : $options['pager_position'];
    $ks_pager_size = $ks_pager_settings[pager_size] ? $ks_pager_settings[pager_size] : $options['pager_size'];
    $ks_pager_spacing = $ks_pager_settings[pager_spacing] ? $ks_pager_settings[pager_spacing] : $options['pager_spacing'];
    $ks_pager_color = $ks_pager_settings[pager_color] ? $ks_pager_settings[pager_color] : $options['pager_color'];
    $ks_pager_color_active = $ks_pager_settings[pager_color_active] ? $ks_pager_settings[pager_color_active] : $options['pager_color_active'];

    $output .= $inputs->select('ks_pager_settings[style]', $ks_pager_style, __('Style', 'kwik'), null, $defaults['appearance']['settings']['pager_style']['options']);
    $output .= $inputs->select('ks_pager_settings[fx]', $ks_pager_speed, __('Effect', 'kwik'), null, $defaults['behavior']['settings']['transition_effect']['options']);
    $output .= $inputs->select('ks_pager_settings[position]', $ks_pager_position, __('Position', 'kwik'), null, $defaults['appearance']['settings']['pager_position']['options']);
    $output .= $inputs->spinner('ks_pager_settings[pager_spacing]', $ks_pager_spacing, __('Spacing: ', 'kwik'), array('min' => '1', 'max' => '100'));
    $output .= $inputs->color('ks_pager_settings[pager_color]', $ks_pager_color, __('Color: ', 'kwik'));
    $output .= $inputs->color('ks_pager_settings[pager_color_active]', $ks_pager_color_active, __('Active Color: ', 'kwik'));
    $output .= $inputs->markup('label', __('Pager Size'));
    $output .= $inputs->multi('ks_pager_settings[pager_size]', $ks_pager_size, $defaults['appearance']['settings']['pager_size']);

    echo $output;
}

// Drag and drop slide configuration for `kwik_slider` post type
// allows you to create and edit slides (`kwik_slide`)
function ks_slides()
{
    global $post;
    $inputs = new KwikInputs();
    $kwik_slides = get_post_meta($post->ID, '_ks_slides');
    $kwik_slides = $kwik_slides[0];

    $output = '';
    // Noncename for security check on data origin
    $output .= $inputs->nonce(KS_PLUGIN_BASENAME . '_nonce', wp_create_nonce(plugin_basename(__FILE__)));

    if (!empty($kwik_slides)) {
        foreach ($kwik_slides as $kwik_slide_id) {
            $output .= get_slide_inputs($kwik_slide_id);
        }
    } else {
        $output .= get_slide_inputs();
    }

    echo $inputs->markup('ul', $output, array("id" => KS_PREFIX . "slide_meta", "class" => "kf_form clear", "ks-location" => KS_PLUGIN_URL));
}

function get_slide_inputs($slide_id = null)
{
    $inputs = new KwikInputs();

    // current slide
    $slide = get_post($slide_id);

    $subtitle_val = get_post_meta($slide->ID, '_slide_subtitle', true);
    $learnmore_val = get_post_meta($slide->ID, '_slide_learnmore', true);
    $link_val = get_post_meta($slide->ID, '_slide_link', true);
    $img_val = get_post_thumbnail_id($slide_id);
    $title_val = $slide_id ? $slide->post_title : '';

    //slide settings
    $slide_inputs = array(
        'title_input' => $inputs->text(KS_PREFIX . 'slide_title', $title_val, null, array('placeholder' => __('Title', 'kwik'))),
        'subtitle_input' => $inputs->text(KS_PREFIX . 'slide_subtitle', $subtitle_val, null, array('placeholder' => __('Subtitle/Caption', 'kwik'))),
        // 'learnmore_input' =>  $inputs->text(KS_PREFIX.'slide_learnmore',$learnmore_val, NULL, array('placeholder'=>__('Learn More','kwik'))),
        'link_input' => $inputs->link(KS_PREFIX . 'slide_link', $link_val, null, array('placeholder' => __('Link', 'kwik'))),
        'slide_id_input' => $inputs->input(array('value' => $slide_id, 'name' => KS_PREFIX . 'slide_id[]', 'type' => 'hidden', 'class' => 'ks_slide_id')),
    );

    // slide_edit layout
    $slide_content = array(
        'img_input' => $inputs->img(KS_PREFIX . 'slide_img', $img_val, null, array('img_size' => 'medium')),
        'slide_details' => $inputs->markup('div', $slide_inputs, array('class' => 'slide_details')),
        'footer' => $inputs->markup('div', array(
            'toolbar' => get_slide_toolbar(),
            'message' => $inputs->markup('div', null, array('class' => 'slide_messages')),
        ), array('class' => 'slide_footer')),
    );

    return $inputs->markup('li', $slide_content, array('class' => 'clear slide_edit'));
}

function get_slide_toolbar()
{
    $inputs = new KwikInputs();
    $buttons = array(
        'remove_slide' => $inputs->markup('span', '', array('del-confirm' => __('Remove slide?', 'kwik'), 'class' => 'remove_slide dashicons-trash', 'title' => __('Remove Slide', 'kwik'))),
        'clone_slide' => $inputs->markup('span', '', array('class' => 'clone_slide dashicons-admin-page', 'title' => __('Clone Slide', 'kwik'))),
        'move_slide' => $inputs->markup('span', '&varr;', array('class' => 'move_slide', 'title' => __('Re-order Slide', 'kwik'))),
        // 'save_slide'   => $inputs->markup('span', '', array('class'=>'dashicons-yes save_slide', 'title'=>__('Save Slide', 'kwik')))
    );

    return $inputs->markup('div', $buttons, array('class' => 'ks_slide_toolbar dashicons'));
}

// Save the Metabox Data
function save_ks_meta($post_id, $post)
{
    if ($post->post_type == 'kwik_slider') {
        // make sure there is no conflict with other post save function and verify the noncename
        if (isset($_POST[KS_PLUGIN_BASENAME . '_nonce']) && !wp_verify_nonce($_POST[KS_PLUGIN_BASENAME . '_nonce'], plugin_basename(__FILE__))) {
            return $post->ID;
        }
        // Is the user allowed to edit the post or page?
        if (!current_user_can('edit_post', $post->ID)) {
            return $post->ID;
        }

        // if ($post->post_status != 'auto-draft') {

        $slide_ids = $_POST[KS_PREFIX . 'slide_id'];

        //TODO add meta validation
        $ks_slides = array(
            '_ks_slides' => $slide_ids,
            '_ks_slider_settings' => $_POST['ks_slider_settings'],
            '_ks_pager_settings' => $_POST['ks_pager_settings'],
        );

        // Add values of $ks_slides as custom fields
        foreach ($ks_slides as $key => $value) {
            if ($post->post_type == 'revision') {
                return;
            }
            KwikUtils::update_meta($post->ID, $key, $value);
        }
        // } else {
        //   return;
        // }
    } else {
        return;
    }
}

add_action('save_post', 'save_ks_meta', 1, 2);
