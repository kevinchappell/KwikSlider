<?php

class KwikSliderAdmin extends KwikSlider
{
  /**
     * Main constructor
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'ks_add_admin_menu'));
        add_action('admin_init', array($this, 'ks_settings_init'));
        add_action('admin_enqueue_scripts', array($this, 'ks_admin_js_css'));
        add_action('admin_head', array($this, 'js_utils_path'));
        new KwikSliderEditor();
        new KwikSliderMeta();
        new KwikSliderHelpers();
    }

    public function ks_add_admin_menu()
    {
        add_submenu_page("edit.php?post_type=kwik_slider", __('Kwik Slider Settings', 'kwik'), __('Settings', 'kwik'), 'manage_options', 'kwik_slider', array($this, KS_PLUGIN_SETTINGS));
    }

    public function ks_settings_init()
    {
        $kwik_settings = new KwikSettings();
        $defaultSettings = parent::ks_default_options();
        $kwik_settings->settings_init(KS_PLUGIN_BASENAME, KS_PLUGIN_SETTINGS, $defaultSettings);
    }

    public function kwik_slider_settings()
    {
        $settings = parent::ks_get_options();
        $inputs = new KwikInputs();
        echo '<div class="wrap">';
        echo $inputs->markup('h2', __('Slider Settings', 'kwik'));
        echo $inputs->markup('p', __('Set the defaults to be used by the sliders. Here you can define transition effects, pagers and themes.', 'kwik'));
        echo '<form action="options.php" method="post">';
        settings_fields(KS_PLUGIN_SETTINGS);
        echo KwikSettings::settings_sections(KS_PLUGIN_SETTINGS, $settings);
        echo '</form>';
        echo '</div>';
    }

    public function ks_admin_js_css($hook)
    {
        $screen = get_current_screen();
        // make these settings
        $hooks_array = array(
            'post.php',
            'post-new.php'
        );

        // Check screen hook and current post type
        if (in_array($hook, $hooks_array)) {
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('kwik-slider-admin', KS_PLUGIN_URL . '/js/' . KS_PREFIX . 'admin.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-autocomplete'), null, true);
            wp_enqueue_style('kwik-slider-admin', KS_PLUGIN_URL . '/css/' . KS_PREFIX . 'admin.css', false, '2014-10-28');
        } elseif ('widgets.php' == $hook) {
            wp_enqueue_script('ks-admin-widgets-js', KS_PLUGIN_URL . '/js/' . KS_PREFIX . 'widgets_admin.js', array('jquery', 'kf_admin_js', 'jquery-ui-autocomplete'), null, true);
            wp_enqueue_style('ks-admin-widgets-css', KS_PLUGIN_URL . '/css/' . KS_PREFIX . 'admin_widgets.css', false, '2014-10-28');
        }
    }

    // Adds the URL path to the utilities dir.
    // @todo remove this and find a better way.
    public function js_utils_path()
    {
        echo '<span style="display:none;" id="ks_js_utils_path">' . KS_PLUGIN_URL . '/utils</span>';
    }


}
