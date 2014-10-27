<?php


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
