<?php
/*
Plugin Name: Kwik Slider
Plugin URI: http://kevin-chappell.com/kwik-slider
Description: A slide show plugin perfect for homepage sliders
Author: Kevin Chappell
Version: 1.1
Author URI: http://kevin-chappell.com
 */



define('KS_PLUGIN_BASENAME', basename(dirname( __FILE__ )));
define('KS_PLUGIN_URL', untrailingslashit(plugins_url('', __FILE__)));
define('KS_PLUGIN_PATH', untrailingslashit( dirname( __FILE__ ) ) );
define('KS_PREFIX', untrailingslashit( dirname( __FILE__ ) ) );

foreach (glob(KS_PLUGIN_PATH . "/inc/*.php") as $inc_filename) {
  include $inc_filename;
}



add_action('init', 'home_slide_cpt_init');
function home_slide_cpt_init() {
	register_post_type('kwik_slider', array(
		'labels' => array(
			'name' => __('Kwik Sliders', 'kwik'),
			'singular_name' => __('Slider', 'kwik'),
			'add_new' => __('Create Slider', 'kwik'),
			'add_new_item' => __('Create New Slider', 'kwik'),
      'not_found' => __('No Sliders found', 'kwik')
		),
		'menu_icon' => 'dashicons-slides',
		'menu_position' => 3,
		'register_meta_box_cb' => 'add_ks_metaboxes',
		'supports' => array(
			'title',
			'editor',
			'author',
			'thumbnail'
		),
		'public' => true,
		'exclude_from_search' => true,
		'has_archive' => false
	));

  register_post_type('ks_slide', array(
    'labels' => array(
      'name' => __('Slides', 'kwik'),
      'singular_name' => __('Slide', 'kwik'),
      'add_new' => __('Create Slide', 'kwik'),
      'add_new_item' => __('Create New Slide', 'kwik')
    ),
    'menu_icon' => 'dashicons-format-image',
    'menu_position' => 3,
    // 'register_meta_box_cb' => 'add_ks_metaboxes',
    'supports' => array(
      'title',
      'editor',
      'author',
      'thumbnail'
    ),
    'show_ui' => false,
    'public' => true,
    'exclude_from_search' => true,
    'has_archive' => false
  ));
	add_image_size('kwik_slider', 920, 230, true);
}

add_action('admin_init', 'slider_options_init');
function slider_options_init() {
	register_setting('slider_options', 'slider_options', 'ks_settings_validate');
}

function ks_admin_js_css($hook) {
	$screen = get_current_screen();

	// make these settings
	$post_types_array = array(
		"kwik_slider",
		"page",
		"kwik_slider_page_slide-order",
	);

	$hooks_array = array(
		"post.php",
		"post-new.php",
	);

	// Check screen hook and current post type
	if (in_array($screen->post_type, $post_types_array)) {
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('kwik-slider', KS_PLUGIN_URL . '/js/' . KS_PLUGIN_BASENAME . '-admin.js', array('jquery', 'jquery-ui-sortable'), NULL, true);

	} elseif ('edit.php' == $hook && 'kwik_slider' == $screen->post_type) {

	}
}
add_action('admin_enqueue_scripts', 'ks_admin_js_css');


/**
 * Enqueue scripts and styles for front-end.
 *
 * @since KwikSlider 1.0
 */
function ks_scripts_and_styles() {

  wp_enqueue_script('jquery-cycle', 'http://malsup.github.io/min/jquery.cycle2.min.js', array('jquery'));

}
add_action('wp_enqueue_scripts', 'ks_scripts_and_styles');



add_action('admin_menu', 'ks_admin_menu');
function ks_admin_menu() {
	add_submenu_page('edit.php?post_type=kwik_slider', 'Order Slides', 'Order', 'edit_pages', 'slide-order', 'home_slide_order_page');
	add_submenu_page('edit.php?post_type=kwik_slider', 'Slider Settings', 'Settings', 'edit_pages', 'slide-settings', 'home_slide_settings_page');
}


function home_slide_order_page() {
	?>
<div class="wrap">

    <h2>Sort Home Page Slides</h2>

    <p>Simply drag the slide up or down and they will be saved in that order.</p>

<?php
$slides = new WP_Query(array(
		'post_type' => 'kwik_slider',
		'posts_per_page' => -1,
		'order' => 'ASC',
		'orderby' => 'menu_order',
	));
	?>

<?php
if ($slides->have_posts()):
	?>
<table class="wp-list-table widefat fixed posts" id="sortable-table">
      <thead>
        <tr>
          <th class="column-order">Order</th>
          <th class="column-thumbnail">Thumbnail</th>
          <th class="column-title">Title</th>
        </tr>
      </thead>
      <tbody data-post-type="slide">
<?php while ($slides->have_posts()):$slides->the_post();
		?>

		        <tr id="post-<?php the_ID();?>">

		          <td class="column-order"><img src="<?php echo get_stylesheet_directory_uri() . '/images/icons/move.png';?>" title="" alt="Move Slide" width="30" height="30" class="" /></td>
		          <td class="column-thumbnail"><?php the_post_thumbnail('thumbnail');?></td>
		          <td class="column-title">
		                        <strong><?php the_title();?></strong>
		                        <div class="excerpt"><?php the_excerpt();?></div>
		                    </td>

		        </tr>

	<?php endwhile;?>
</tbody>
      <tfoot>
        <tr>
          <th class="column-order">Order</th>
          <th class="column-thumbnail">Thumbnail</th>
          <th class="column-title">Title</th>
        </tr>
      </tfoot>
    </table>
<?php else:?>
<p>No slides found, why not <a href="post-new.php?post_type=home_slide">create one?</a></p>
<?php endif;?>

<?php wp_reset_postdata();// Don't forget to reset again! ?>
<style>

    /* Dodgy CSS ^_^ */

    #sortable-table td { background: white; }

    #sortable-table .column-order { padding: 3px 10px; width: 50px; }

      #sortable-table .column-order img { cursor: move; }

    #sortable-table td.column-order { vertical-align: middle; text-align: center; }

    #sortable-table .column-thumbnail { width: 160px; }

  </style>



  </div><!-- .wrap -->



<?php
}
add_action('wp_ajax_home_slide_update_post_order', 'home_slide_update_post_order');
function home_slide_update_post_order() {
	global $wpdb;
	$post_type = $_POST['postType'];
	$order = $_POST['order'];
	/**
	 *    Expect: $sorted = array(
	 *                menu_order => post-XX
	 *            );
	 */
	foreach ($order as $menu_order => $post_id) {
		$post_id = intval(str_ireplace('post-', '', $post_id));
		$menu_order = intval($menu_order);
		wp_update_post(array(
			'ID' => $post_id,
			'menu_order' => $menu_order,
		));
	}
	die('1');
}
?>
