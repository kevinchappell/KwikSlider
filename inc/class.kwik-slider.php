<?php

/**
 * KwikSlider Class
 * @category    Plugin
 * @package     KwikSlider
 * @author      Kevin Chappell <kevin.b.chappell@gmail.com>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link        http://kevin-chappell.com/kwik-slider/docs/inc/class.kwik-slider.php/
 * @since       KwikSlider 1.0
 */

class KwikSlider
{
    /**
     * Main constructor
     */
    public function __construct()
    {
        foreach (glob(dirname(__FILE__).'/class.kwik-slider-*.php') as $inc_filename) {
            include $inc_filename;
        }
        add_action('init', array(&$this, 'ks_cpt_init'));
        $this->widgets();
        if (is_admin()) {
            $this->admin();
        } else {
            add_action('wp_enqueue_scripts', array(&$this, 'ks_js_and_css'));
        }
    }

    public function ks_cpt_init()
    {
        $settings = $this->ks_get_options();

        register_post_type('kwik_slider', array(
            'labels' => array(
                'name' => __('Kwik Sliders', 'kwik'),
                'singular_name' => __('Slider', 'kwik'),
                'add_new' => __('Create Slider', 'kwik'),
                'edit_item' => __('Edit Slider', 'kwik'),
                'add_new_item' => __('Create New Slider', 'kwik'),
                'not_found' => __('No Sliders found', 'kwik')
            ),
            'menu_icon' => 'dashicons-slides',
            'menu_position' => 3,
            'register_meta_box_cb' => array('KwikSliderMeta', 'add_ks_meta_boxes'),
            'supports' => array(
                'title',
                // 'editor',
                // 'author',
                // 'thumbnail'
            ),
            'public' => true,
            'exclude_from_search' => true,
            'has_archive' => false,
            'rewrite' => array('slug' => 'kwik-slider')
        ));

        register_post_type('ks_slide', array(
            'labels' => array(
                'name' => __('Slides', 'kwik'),
                'singular_name' => __('Slide', 'kwik'),
                'add_new' => __('Create Slide', 'kwik'),
                'edit_item' => __('Edit Slide', 'kwik'),
                'add_new_item' => __('Create New Slide', 'kwik'),
            ),
            'menu_icon' => 'dashicons-format-image',
            'menu_position' => 3,
            // 'register_meta_box_cb' => 'add_ks_metaboxes',
            'supports' => array(
                'title',
                'editor',
                'author',
                'thumbnail',
            ),
            'show_ui' => false,
            'public' => true,
            'exclude_from_search' => true,
            'has_archive' => false,
        ));
        if (isset($settings['slide_size'])) {
            $slide_size = $settings['slide_size'];
            $cropped = isset($slide_size['cropped']) ? true : false;
            add_image_size('kwik_slider', $slide_size['width'], $slide_size['height'], $cropped);
        }
    }

    public function admin()
    {
        new KwikSliderAdmin();
        add_action('init', array('KwikSliderEditor', 'add_editor_button'));
    }

    public function widgets()
    {
        foreach (glob(KS_PLUGIN_PATH . '/widgets/*.php') as $inc_filename) {
            include $inc_filename;
        }
    }

    /**
     * Enqueue scripts and styles for front-end.
     *
     * @since KwikSlider 1.0
     */
    public function ks_js_and_css()
    {

        wp_enqueue_script('jquery-cycle', 'http://malsup.github.io/min/jquery.cycle2.min.js', array('jquery'));
        // wp_enqueue_script('jquery-cycle', KS_PLUGIN_URL . '/js/jquery-cycle2.js', array('jquery'));
        wp_enqueue_style('ks-main-css', KS_PLUGIN_URL . '/css/' . KS_PREFIX . 'main.css', false, '2014-10-28');

    }

    public static function ks_get_options()
    {
        return get_option(KS_PLUGIN_SETTINGS, array(&$this, 'ks_default_options'));
    }

    public static function ks_default_options()
    {
        $themes = array(
            'katrina' => 'Katrina',
            // 'kevin' => 'Kevin',
        );

        $pagers = array(
            'circle' => 'Circle',
            'square' => 'Square',
            'thumbnail' => 'Thumbnail',
            'number' => 'Number',
            'text' => 'Text',
        );

        $ks_default_options = array(
            'slider' => array(
                'section_title' => __('Slider', 'kwik'),
                'section_desc' => __('Set the default options for sliders here.', 'kwik'),
                'settings' => array(
                    'slider_behavior' => array(
                        'title' => __('Behavior', 'kwik'),
                        'desc' => __('How does the slider move and interact with user?', 'kwik'),
                        'fields' => KwikSliderHelpers::behavior_fields('slider')
                    ),
                    'slider_appearance' => array(
                        'title' => __('Appearance', 'kwik'),
                        'desc' => __('How does the slider look?', 'kwik'),
                        'fields' => array(
                            'theme' => array(
                                'type' => 'select',
                                'title' => __('Default Theme', 'kwik'),
                                'value' => 'Katrina',
                                'options' => $themes
                            ),
                            'slide_size' => array(
                                'title' => __('Slider Size', 'kwik'),
                                'desc' => __('This option will create a cropped custom image size with using these dimensions.', 'kwik'),
                                'fields' => array(
                                    'width' => array('type' => 'spinner', 'title' => 'Width: ', 'value' => '920', 'attrs' => array('min' => '0', 'max' => '1280')),
                                    'height' => array('type' => 'spinner', 'title' => 'Height: ', 'value' => '300', 'attrs' => array('min' => '0', 'max' => '800')),
                                    'cropped' => array('type' => 'toggle', 'title' => ' Cropped', 'value' => 'cropped'),
                                )
                            )
                        )
                    )
                )
            ),
            'pager' => array(
                'section_title' => __('Pager', 'kwik'),
                'section_desc' => __('Set default theme and colors.', 'kwik'),
                'settings' => array(
                    'pager_behavior' => array(
                        'title' => __('Behavior', 'kwik'),
                        'desc' => __('How does the pager move and interact with user? Time in milliseconds.', 'kwik'),
                        'fields' => KwikSliderHelpers::behavior_fields('pager')
                    ),
                    'pager_appearance' => array(
                        'title' => __('Appearance', 'kwik'),
                        'desc' => __('Note: these can be overridden by individual slider settings.', 'kwik'),
                        'fields' => array(
                            'style' => array(
                                'type' => 'select',
                                'title' => __('Pager Style', 'kwik'),
                                'value' => 'circle',
                                'options' => $pagers,
                            ),
                            'position' => array(
                                'type' => 'select',
                                'title' => __('Pager Position', 'kwik'),
                                'value' => 'circle',
                                'options' => array(
                                    'left:0;top:0;text-align: left;' => 'Top Left',
                                    'left:50%;top:0;margin-left:-250px;text-align: center;' => 'Top Center',
                                    'right:50%;top:0;margin-right:-250px;text-align: right;' => 'Top Right',
                                    'left:50%;top:50%;margin-left:-250px;text-align: center;' => 'Center Center',
                                    'left:0;bottom:0;text-align: left;' => 'Bottom Left',
                                    'left:50%;bottom:0;margin-left:-250px;text-align: center;' => 'Bottom Center',
                                    'right:0;bottom:0;text-align: right;' => 'Bottom Right',
                                ),
                            ),
                            'pager_color' => array(
                                'type' => 'color',
                                'title' => __('Pager Color', 'kwik'),
                                'value' => '#ffffff',
                            ),
                            'pager_color_active' => array(
                                'type' => 'color',
                                'title' => __('Pager Color Active', 'kwik'),
                                'value' => '#990000',
                            ),
                            'pager_size' => array(
                                'title' => __('Pager Size', 'kwik'),
                                'desc' => __('Set the size of the pager buttons in pixels', 'kwik'),
                                'fields' => array(
                                    'width' => array(
                                        'type' => 'spinner',
                                        'title' => 'Width: ',
                                        'value' => '20',
                                        'attrs' => array(
                                            'min' => '1',
                                            'max' => '100',
                                        ),
                                    ),
                                    'height' => array(
                                        'type' => 'spinner',
                                        'title' => 'Height: ',
                                        'value' => '20',
                                        'attrs' => array(
                                            'min' => '1',
                                            'max' => '100',
                                        ),
                                    ),
                                )
                            ),
                            'pager_spacing' => array(
                                'type' => 'spinner',
                                'title' => __('Spacing: ', 'kwik'),
                                'value' => '10',
                                'attrs' => array('min' => '1', 'max' => '100'),
                            )
                        )
                    )
                )
            )
        );

        return apply_filters('ks_default_options', $ks_default_options);
    }
}
