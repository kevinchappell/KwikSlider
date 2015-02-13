<?php

class KwikSliderMeta extends KwikSlider
{
    /**
     * Main constructor
     */
    public function __construct()
    {
        add_action('save_post', array($this, 'save_ks_meta'), 1, 2);
        add_action('wp_ajax_save_ks_meta', array($this, 'save_ks_meta'));
    }
    // Add the kwik slider meta box
    public static function add_ks_meta_boxes()
    {
        $kwik_slider_settings = parent::ks_default_options();

        // Slider Settings
        $slider_behavior = $kwik_slider_settings['slider']['settings']['slider_behavior']['fields'];
        $slider_appearance = $kwik_slider_settings['slider']['settings']['slider_appearance']['fields'];
        $slider_settings_fields = array_merge($slider_behavior, $slider_appearance);
        set_transient('ks_slider_settings', $slider_settings_fields, WEEK_IN_SECONDS );

        // Pager Settings
        $pager_behavior = $kwik_slider_settings['pager']['settings']['pager_behavior']['fields'];
        $pager_appearance = $kwik_slider_settings['pager']['settings']['pager_appearance']['fields'];
        $pager_settings_fields = array_merge($pager_behavior, $pager_appearance);
        set_transient('ks_pager_settings', $pager_settings_fields, WEEK_IN_SECONDS );


        add_meta_box('slider_settings', 'Slider Settings', array('KwikSliderMeta', 'slider_settings'), 'kwik_slider', 'side', 'default');
        add_meta_box('pager_settings', 'Pager Settings', array('KwikSliderMeta', 'pager_settings'), 'kwik_slider', 'side', 'default');
        add_meta_box('ks_slides', 'Slides', array('KwikSliderMeta', 'ks_slides'), 'kwik_slider', 'normal', 'core');
    }

    // Slider settings for themes, colors and other settings
    public static function slider_settings($post)
    {
        $meta = new KwikMeta();
        echo $meta->get_fields($post, 'ks_slider_settings');
    }

    public static function pager_settings($post)
    {
        $meta = new KwikMeta();
        echo $meta->get_fields($post, 'ks_pager_settings');
    }

    // Drag and drop slide configuration for `kwik_slider` post type
    // allows you to create and edit slides (`kwik_slide`)
    public static function ks_slides()
    {
        global $post;
        $inputs = new KwikInputs();
        $kwik_slides = KwikMeta::get_meta_array($post->ID, 'ks_slides');
        $output = '';
        // Noncename for security check on data origin
        $output .= $inputs->nonce(KS_PLUGIN_BASENAME . '_nonce', wp_create_nonce(plugin_basename(__FILE__)));

        if (!empty($kwik_slides)) {
            foreach ($kwik_slides as $kwik_slide_id) {
                $output .= self::get_slide_inputs($kwik_slide_id);
            }
        } else {
            $output .= self::get_slide_inputs();
        }

        echo $inputs->markup('ul', $output, array("id" => KS_PREFIX . "slide_meta", "class" => "kf_form clear", "ks-location" => KS_PLUGIN_URL));
    }

    public static function get_slide_inputs($slide_id = null)
    {
        $inputs = new KwikInputs();

        // current slide
        $slide = get_post($slide_id);

        $subtitle_val = get_post_meta($slide->ID, '_slide_subtitle', true);
        $learnmore_val = get_post_meta($slide->ID, '_slide_learnmore', true);
        $link_val = get_post_meta($slide->ID, '_slide_link', true);
        $img_val = get_post_thumbnail_id($slide_id);
        $title_val = $slide_id ? $slide->post_title : '';

        // slide settings
        $slide_inputs = array(
            'title_input' => $inputs->text(KS_PREFIX . 'slide_title', $title_val, null, array('placeholder' => __('Title', 'kwik'))),
            'subtitle_input' => $inputs->text(KS_PREFIX . 'slide_subtitle', $subtitle_val, null, array('placeholder' => __('Subtitle/Caption', 'kwik'))),
            'learnmore_input' =>  $inputs->text(KS_PREFIX.'slide_learnmore',$learnmore_val, NULL, array('placeholder'=>__('Learn More','kwik'))),
            'link_input' => $inputs->link(KS_PREFIX . 'slide_link', $link_val, null, array('placeholder' => __('Link', 'kwik'))),
            'slide_id_input' => $inputs->input(array('value' => $slide_id, 'name' => KS_PREFIX . 'slide_id[]', 'type' => 'hidden', 'class' => 'ks_slide_id')),
        );

        // slide_edit layout
        $slide_content = array(
            'img_input' => $inputs->img(KS_PREFIX . 'slide_img', $img_val, null, array('img_size' => 'medium')),
            'slide_details' => $inputs->markup('div', $slide_inputs, array('class' => 'slide_details')),
            'footer' => $inputs->markup('div', array(
                'toolbar' => self::get_slide_toolbar(),
                'message' => $inputs->markup('div', null, array('class' => 'slide_messages')),
            ), array('class' => 'slide_footer')),
        );

        return $inputs->markup('li', $slide_content, array('class' => 'clear slide_edit'));
    }

    public static function get_slide_toolbar()
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
    public function save_ks_meta($post_id)
    {
        $post = $_POST;

        // Checks if this is an AJAX save
        if(isset($post['data'])){
            parse_str($post['data'], $post);
        } else if(empty($post)){
            return;
        }
        if (!isset($post['post_status']) || $post['post_status'] === 'auto-draft') return;
        if ($post['post_type'] === 'kwik_slider') {
            // make sure there is no conflict with other post save function and verify the noncename
            if (isset($post[KS_PLUGIN_BASENAME . '_nonce']) && !wp_verify_nonce($post[KS_PLUGIN_BASENAME . '_nonce'], plugin_basename(__FILE__))) {
                return $post['post_ID'];
            }
            // Is the user allowed to edit the post or page?
            if (!current_user_can('edit_post', $post['post_ID'])) {
                return $post['post_ID'];
            }

            $slide_ids = $post[KS_PREFIX . 'slide_id'];
            //TODO add meta validation
            $ks_slides = array(
                'ks_slides' => $slide_ids,
                'ks_slider_settings' => $post['ks_slider_settings'],
                'ks_pager_settings' => $post['ks_pager_settings'],
            );

            // Add values of $ks_slides as custom fields
            foreach ($ks_slides as $key => $value) {
                KwikMeta::update_meta($post['post_ID'], $key, $value);
            }

        } else {
            return;
        }
    }

}
