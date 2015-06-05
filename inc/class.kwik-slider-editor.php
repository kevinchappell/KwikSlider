<?php

/**
 * KwikSlider Editor Class
 * @category    Editor
 * @package     KwikSlider
 * @subpackage  KwikSliderEditor
 * @author      Kevin Chappell <kevin.b.chappell@gmail.com>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link        http://kevin-chappell.com/kwik-slider/docs/inc/class.kwik-slider-editor.php/
 * @since       KwikSlider 1.0
 */

class KwikSliderEditor {
	/**
	 * Main constructor
	 */
	public function __construct() {
		add_shortcode('add_kwik_slider', 'add_kwik_slider');
		add_action('wp_ajax_add_kwik_slider_popup', 'add_kwik_slider_popup');
	}

	function add_kwik_slider($atts, $content = null) {
	  return "\n" . '<div class="kwik-slider-preview" style="width:200px;height:200pc;background:#000"></div>'."\n";
	}

	/**
	 * Checks permissions and adds buttons to the editor
	 * @return  filtered mce_external_plugins and mce_buttons
	 */
	public static function add_editor_button() {
		// check permissions
		if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
			// if in Visual mode
			if (get_user_option('rich_editing')) {
				add_filter('mce_external_plugins', array('KwikSliderEditor', 'add_custom'));
				add_filter('mce_buttons', array('KwikSliderEditor', 'register_button'));
			}
		}
	}

	/**
	 * adds the button js to the list of editor javascripts
	 * @param [type] $plugin_array [description]
	 */
	public static function add_custom($plugin_array) {
		$plugin_array['add_kwik_slider'] = KS_PLUGIN_URL . '/js/kwik-slider-editor.js';
		return $plugin_array;
	}
	//Add button to the button array.
	public static function register_button($buttons) {
		array_push($buttons, "add_kwik_slider");
		return $buttons;
	}


	public function add_kwik_slider_popup()	{
		?>
	<!DOCTYPE html>
	<head>
		<title>Add slider to post content</title>
	<script type="text/javascript" src="<?php bloginfo('url')?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory')?>/js/service_box_dialog.js"></script>
	</head>
	<body>
	<form onsubmit="KwikSlider.update();return false;" action="#">
	  <table border="0" cellpadding="4" cellspacing="0" role="presentation">
		<tr>
		  <td colspan="2" class="title" id="app_title">Add Service Box</td>
		</tr>
		<tr>
		  <td class="nowrap"><label for="anchorName">Title:</label></td>
		  <td><input name="anchorName" type="text" class="mceFocus" id="anchorName" value="" style="width: 200px" aria-required="true" /></td>
		</tr>
		<tr>
		  <td class="nowrap"><label for="anchorName">Content:</label></td>
		  <td><textarea name="" class="mceFocus" cols="37" id="service_box_content"></textarea></td>
		</tr>
	  </table>

	  <div class="mceActionPanel">
		<input type="submit" id="insert" name="insert" value="{#update}" />
		<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
	  </div>
	</form>
	</html>
	<?php

	}


}
