<?php

/**
 * KwikSliderHelpers Class
 * @category    Helpers
 * @package     KwikSliderHelpers
 * @author      Kevin Chappell <kevin.b.chappell@gmail.com>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link        http://kevin-chappell.com/kwik-slider/docs/inc/class.kwik-slider-helpers.php/
 * @since       KwikSlider 1.0
 */

class KwikSliderHelpers extends KwikSlider
{
    /**
     * Main class constructor
     */
    function __construct()
    {
        // $this->name = "KwikSliderHelpers";
        add_filter('gettext', array(&$this, 'ks_slider_text_filter'), 20, 3);
    }

    /**
     * Returns the thumbnail for a slider
     * @param  [int]   $slider_id   id for the slider we need the thumb for
     * @param  integer $slide_index index of the slide we want the thumb for
     * @return [int]                media id for the thumbnail
     */
    public function get_slider_thumb($slider_id, $slide_index = 0)
    {
        $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($slider_id), 'thumbnail');
        if ($thumb) {
            $thumb = $thumb[0];
        } else {
            $kwik_slides = get_post_meta($slider_id, '_ks_slides', false);
            $kwik_slides = $kwik_slides[0];
            $slide_id = intval($kwik_slides[$slide_index]);
            $index = $slide_index+1;
            $thumb = KwikSliderHelpers::get_slider_thumb($slide_id, $index);
        }
        return $thumb;
    }

    /**
    * Change the text displayed for this custom post type
    * @param  [string] $translated_text   text after translation
    * @param  [string] $untranslated_text text before translation
    * @param  [string] $domain            namespace domain for this translation
    * @return [string]                    translated text
    */
    public function ks_slider_text_filter( $translated_text, $untranslated_text, $domain )
    {
        global $typenow;


        if (is_admin() && 'kwik_slider' == $typenow) {

            switch( $untranslated_text ) {

            case 'Insert into post':
                $translated_text = __('Set Slider Image', 'kwik');
                break;

            case 'Enter title here':
                $translated_text = __('Enter Slider title here', 'kwik');
                break;

            //add more items

            }
        }
        return $translated_text;
    }

    public static function effects(){
        return array(
            'blindX' => __('Blind X', 'kwik'),
            'blindY' => __('Blind Y', 'kwik'),
            'blindZ' => __('Blind Z', 'kwik'),
            'cover' => __('Cover', 'kwik'),
            'curtainX' => __('Curtain X', 'kwik'),
            'curtainY' => __('Curtain Y', 'kwik'),
            'fade' => __('Fade', 'kwik'),
            'fadeZoom' => __('Fade Zoom', 'kwik'),
            'growX' => __('Grow X', 'kwik'),
            'growY' => __('Grow Y', 'kwik'),
            'none' => __('None', 'kwik'),
            'scrollUp' => __('Scroll Up', 'kwik'),
            'scrollDown' => __('Scroll Down', 'kwik'),
            'scrollLeft' => __('Scroll Left', 'kwik'),
            'scrollRight' => __('Scroll Right', 'kwik'),
            'scrollHorz' => __('Scroll Horizontal', 'kwik'),
            'scrollVert' => __('Scroll Vertical', 'kwik'),
            'shuffle' => __('Shuffle', 'kwik'),
            'slideX' => __('Slide X', 'kwik'),
            'slideY' => __('Slide Y', 'kwik'),
            'tiles' => __('Tiles', 'kwik'),
            'toss' => __('Toss', 'kwik'),
            'turnUp' => __('Turn Up', 'kwik'),
            'turnDown' => __('Turn Down', 'kwik'),
            'turnLeft' => __('Turn Left', 'kwik'),
            'turnRight' => __('Turn Right', 'kwik'),
            'uncover' => __('Uncover', 'kwik'),
            'wipe' => __('Wipe', 'kwik'),
            'zoom' => __('Zoom', 'kwik')
        );
    }

    public static function behavior_fields($name){
        return array(
            $name.'_speed' => array(
                'type' => 'spinner',
                'title' => __('Transition Speed', 'kwik'),
                'value' => '750',
                'attrs' => array('min' => '0', 'max' => '9001'),
            ),
            $name.'_timeout' => array(
                'type' => 'spinner',
                'title' => __('Transition Timeout', 'kwik'),
                'value' => '3000',
                'attrs' => array('min' => '0', 'max' => '9001')
            ),
            $name.'_effect' => array(
                'type' => 'select',
                'title' => __('Transition Effect', 'kwik'),
                'value' => 'fade',
                'options' => self::effects()
            )
        );
    }

}
