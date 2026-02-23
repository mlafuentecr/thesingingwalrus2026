<?php

// =============================================================================
// FUNCTIONS.PHP
// -----------------------------------------------------------------------------
// Overwrite or add your own custom functions to Pro in this file.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Parent Stylesheet
//   02. Additional Functions
// =============================================================================

// Enqueue Parent Stylesheet
// =============================================================================

add_filter( 'x_enqueue_parent_stylesheet', '__return_true' );

function singing_theme_scripts(){
    wp_enqueue_script('custom', get_template_directory_uri() . '-child/assets/js/custom.js');
    wp_enqueue_script('paroller', get_template_directory_uri() . '-child/assets/js/plugin/jquery.paroller.js'); 
   
    global $post;  
    if(!empty($post) && $post->post_name=='blog'){
        $nonce = wp_create_nonce("Essential_Grid_actions"); ?>
        <script type="text/javascript">
            setTimeout(function() {          
                essentialgrid_deletecache();
            }, 5000);
            function essentialgrid_deletecache(){
                var objData = {
                    action:"Essential_Grid_request_ajax",
                    client_action: 'delete_full_cache',
                    token:'<?php echo $nonce; ?>',
                };
                jQuery.ajax({
                    type:"post",
                    url:"<?php echo admin_url('admin-ajax.php')?>",
                    dataType: 'json',
                    data:objData
                });
            }
        </script>
    <?php 
    }
} 
add_action( 'wp_enqueue_scripts', 'singing_theme_scripts' );
// Additional Functions
// =============================================================================

require_once ("lib/custom-post-type.php");
 
//Songs list display
function songs_list($atts, $content = '') {
    ob_start();
    get_template_part('songs/list');
    return ob_get_clean(); 
}
add_shortcode('songs-list','songs_list');


//Songs sidebar list display
function songs_sidebar_list($atts, $content = '') {
    ob_start();
    get_template_part('songs/sidebar-lists');
    return ob_get_clean(); 
}
add_shortcode('songs-sidebar-list','songs_sidebar_list');

function example_cats_related_post() {
    get_template_part('songs/related');
}

function wpsites_modify_comment_form_text_area($arg) {
    $arg['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . _x( 'Leave a comment', 'noun' ) . '</label><textarea id="comment" name="comment" placeholder="Your comment..." cols="45" rows="10" aria-required="true"></textarea></p>';
    return $arg;
}

add_filter('comment_form_defaults', 'wpsites_modify_comment_form_text_area');

add_filter( 'get_comment_date', 'wpsites_change_comment_date_format' , 10, 3);	
function wpsites_change_comment_date_format( $date, $d, $comment ) {//$d
   $d = date("d F",strtotime($comment->comment_date));	
    return $d;
}	

// custom wp logout url which logout and redirect to home url
function logout_shortcode( $atts, $content = '', $shortcode_name = '' ) {

	if ( ! is_user_logged_in() ) {
		return '';
	}

	$atts = shortcode_atts( $defaults = [], $atts );

	if ( 'logout_to_home' === $shortcode_name ) {
		$atts['redirect'] = 'home';
	}

	if ( 'home' === $atts['redirect'] ) {
			$atts['redirect'] = home_url();
	}

	return ( wp_logout_url( $atts['redirect'] ) );
}

add_shortcode( 'logout_to_home', 'logout_shortcode' );

//Search result
function hook_javascript() {
    ?>
        <script>
            var bG={
                  search_options: '<?= 'Search Options'?>',
                  search: '<?= 'Search'?>',
                };
        </script>
    <?php
}
add_action('wp_head', 'hook_javascript');

function search_new_excerpt_more($more) {
   global $post;
   return '...';
}

function replace_post_excerpt_filter($exercpt)
{
        $output=get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);
        if($output == '')
        return $exercpt;
        
        return $output;
}

function lower_wpseo_priority( $html ) {
    return 'low';
}
add_filter( 'wpseo_metabox_prio', 'lower_wpseo_priority' );


/**
 * Display a custom taxonomy dropdown in admin
 */
add_action('restrict_manage_posts', 'tsm_filter_post_type_by_taxonomy');
function tsm_filter_post_type_by_taxonomy() {
	global $typenow;
	$post_type = 'songs'; // change to your post type
	$taxonomy  = 'topics'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'show_option_all' => sprintf( __( 'Show all %s', 'textdomain' ), $info_taxonomy->label ),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => true,
		));
	};
}
/**
 * Filter posts by taxonomy in admin
 */
add_filter('parse_query', 'tsm_convert_id_to_term_in_query');
function tsm_convert_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'songs'; // change to your post type
	$taxonomy  = 'topics'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}

//Vimeo video shortcode
function vimeo_video_shortcode( $atts = array() ) {

    // set up default parameters
    extract(shortcode_atts(array(
     'showcase' => 'Vimeo ID here'
    ), $atts));

    return "<div style='padding: 56.25% 0 0 0; position: relative;'><iframe style='position: absolute; top: 0; left: 0; width: 100%; height: 100%;' src='$showcase' frameborder='0' allowfullscreen='allowfullscreen'></iframe></div>";
}
add_shortcode('vimeo_shortcode', 'vimeo_video_shortcode');


/**
 * -----------------------------------------------------------------------------
 * FIXED QUERY FILTER (pre_get_posts)
 * -----------------------------------------------------------------------------
 * Why:
 * - Your original filter was running on ALL queries (including secondary queries
 *   used by plugins/shortcodes/AJAX), which can flip sorting unexpectedly.
 *
 * What this does:
 * - Only affects the MAIN frontend query
 * - Blog page: order by date DESC (most recent first)
 * - Topics taxonomy archive: order by meta "song_order" ASC (numeric)
 * -----------------------------------------------------------------------------
 */
add_action('pre_get_posts', function ($query) {

    // Do not modify admin queries
    if (is_admin()) {
        return;
    }

    // Only target the main query on the frontend
    if (!$query->is_main_query()) {
        return;
    }

    // Prevent interference with nav menu queries
    if ($query->get('post_type') === 'nav_menu_item') {
        return;
    }

    // Blog page: show most recent posts first
    if (is_page('blog')) {
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
    }

    // Topics taxonomy archive: order by song_order (numeric) ascending
    if (is_tax('topics')) {
        $query->set('meta_key', 'song_order');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }

}, 20);


function custom_category_description( $atts, $content = '', $shortcode_name = '' ){
	//$atts = shortcode_atts( $defaults = [], $atts );
	if( isset( $atts['id'] ) ){
		$current_term = get_term_by( 'id', $atts['id'], 'topics' );
		return $current_term->description;
	}
}
add_shortcode( 'custom_category_description', 'custom_category_description' );

function custom_wp_head(){
	echo '<link rel="shortcut icon" href="/wp-content/uploads/2022/04/favicon.png" type="image/x-icon"><link rel="icon" href="/wp-content/uploads/2022/04/favicon.png" type="image/x-icon">';
}
add_action('wp_head','custom_wp_head');
add_action('admin_head','custom_wp_head');

add_filter( 'comments_open', 'my_comments_open', 10, 2 );
function my_comments_open( $open, $post_id ) {
  $post = get_post( $post_id );
  if ( 'songs' == $post->post_type ){
      $open = true;
  }
  return $open;
}

function topics_page_redirect() {
    if ( is_front_page() && is_user_logged_in() && !current_user_can('administrator') ) {
        wp_redirect ( home_url( '/topics/' ) );
        exit();
    }
}
add_action( 'template_redirect', 'topics_page_redirect' );

//remove author link in blog single page
function remove_author_link() {
if(is_single()){?>
	<script>
		jQuery(document).ready(function($) {
			//$('.blog-content .x-row-inner a.x-div').attr('href', "#");
			var authorexists = $('.blog-content .x-row-inner a.x-div').length;
			if(authorexists != 0){
				$('.blog-content .x-row-inner a.x-div').parent().parent().parent().parent().css('display', "none");
				$('.blog-content .x-row-inner a#authorLink').parent().parent().parent().parent().css('display', "block");
			}
		});
	</script>
<?php }
}
add_action('wp_footer', 'remove_author_link');


/*---Function to redirect newly posted comment notification to custom admin email---*/
function custom_notify_postauthor($comment_id) {
    // Get the comment object
    $comment = get_comment($comment_id);

    // Set the custom admin email address
    $custom_admin_email = 'printables@thesingingwalrus.com';

    // Get the post author's email
    $author_email = get_the_author_meta('email', $comment->user_id);

    // Get the comment notification subject and message
    $post_title = get_the_title($comment->comment_post_ID);
    $subject = sprintf(__('[%1$s] New Comment on your post: %2$s'), get_bloginfo('name'), $post_title);

    $message  = sprintf(__('Author : %1$s'), $comment->comment_author) . "\r\n";
    $message .= sprintf(__('Email    : %1$s'), $comment->comment_author_email) . "\r\n";
    $message .= sprintf(__('URL      : %1$s'), $comment->comment_author_url) . "\r\n";
    $message .= sprintf(__('IP       : %1$s'), $comment->comment_author_IP) . "\r\n";
    $message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
    $message .= __('You can see all comments on this post here: ') . "\r\n";
    $message .= get_permalink($comment->comment_post_ID) . "#comments\r\n\r\n";
    $message .= sprintf(__('Permalink: %1$s'), get_comment_link($comment->comment_ID)) . "\r\n\r\n";

	// Add "Trash it" and "Spam it" links
    $message .= sprintf(__('Trash it: %1$s'), admin_url("comment.php?action=trash&c={$comment->comment_ID}")) . "\r\n";
    $message .= sprintf(__('Spam it: %1$s'), admin_url("comment.php?action=spam&c={$comment->comment_ID}")) . "\r\n\r\n";
	
    // Set additional headers
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
    );

    // Use the wp_mail function to send the customized notification
    wp_mail($custom_admin_email, $subject, $message, $headers);

    // Send the default notification to the post author
    wp_notify_postauthor($comment_id);
}

// Override the default notification function
remove_action('comment_post', 'wp_notify_postauthor');
add_action('comment_post', 'custom_notify_postauthor');


add_filter( 'wp_nav_menu_items', 'walrus_dynamic_menu_item_label', 9999, 2 );  
function walrus_dynamic_menu_item_label( $items, $args ) { 
   if ( ! is_user_logged_in() ) { 	  
      $items = str_replace( "My Account", "Join Now", $items ); 
	  $items = str_replace( "My Favorites", "For Educators", $items ); 
	  $items = str_replace( "Blog", "For Parents", $items );
   } 
   return $items; 
} 

function walrus_dynamic_menu_item_label_main_item_rewrite( $items, $args ) {
   if ( ! is_user_logged_in() ) { 	  
		foreach ( $items as $item ) {
			$item->url = str_replace( "my-account", "#siginp-section", $item->url );
			$item->url = str_replace( "my-favorites", "for-educators", $item->url );
			$item->url = str_replace( "blog", "for-parents", $item->url );
		}	
	}
    return $items;
} 
add_filter( 'wp_nav_menu_objects', 'walrus_dynamic_menu_item_label_main_item_rewrite', 10, 2 );


add_filter( 'wp_lazy_loading_enabled', '__return_false' );

/*--Function to Auto clear Essential grid cache when Scehduled post are auto published--*/
function clear_essential_grid_cache($post_id) {
    if (function_exists('essential_grid_clear_elements')) {
        essential_grid_clear_elements();
    }
}

function hook_publish_events() {
    add_action('publish_post', 'clear_essential_grid_cache', 10, 1);
    add_action('publish_future_post', 'clear_essential_grid_cache', 10, 1);
    add_action('wp_insert_post', 'check_and_clear_cache', 10, 3);
}

function check_and_clear_cache($post_id, $post, $update) {
    if ($post->post_status == 'publish' && !$update) {
        clear_essential_grid_cache($post_id);
    }
}

add_action('init', 'hook_publish_events');

/*******Function to publish missed out scheduled posts ******************/
const ACTION = 'wpb_missed_scheduled_posts_publisher';
const BATCH_LIMIT = 20;
const OPTION_NAME = 'wpb-missed-scheduled-posts-publisher-last-run';

// Create a custom endpoint to trigger the missed schedule function
function wpb_register_custom_endpoint() {   
   publish_missed_posts();
}
add_action('init', 'wpb_register_custom_endpoint');
// Function to publish missed scheduled posts
function publish_missed_posts() {
    global $wpdb;
    update_option(OPTION_NAME, time());
    $scheduled_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_date <= %s AND post_status = 'future' LIMIT %d",
            current_time('mysql', 0),
            BATCH_LIMIT
        )
    );
    if (!count($scheduled_ids)) {
        return;
    }
    if (count($scheduled_ids) === BATCH_LIMIT) {
        // There's a bit to do.
        update_option(OPTION_NAME, 0);        
    }   
    array_map('wp_publish_post', $scheduled_ids);
}
/*******Function to publish missed out scheduled posts end******************/

/*******Function to enqueue custom script file to the admin dashboard******************/
function enqueue_custom_admin_script() {
    // Use get_stylesheet_directory_uri() instead of get_template_directory_uri() if you are using a child theme
    wp_enqueue_script('custom-admin-script', get_stylesheet_directory_uri() . '/assets/js/custom-admin-script.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_script');




// Function to get ACF fields from page ID 4508
function get_top_banner_content() {
    $page_id = 4538; // The specific page ID
    $logout_content = get_field('logout_content', $page_id);
    $login_content = get_field('login_content', $page_id);

    return [
        'logout_content' => $logout_content,
        'login_content' => $login_content,
    ];
}

// Action hook to display the top banner content
add_action('wp_body_open', 'display_top_banner');

function display_top_banner() {
    // Get the top banner content
    $top_banner_content = get_top_banner_content();

    // Only display if there is content
    if (is_user_logged_in() && !empty($top_banner_content['login_content'])) {
        ?>
        <div class="Log-top-banner">
            <div class="login-content">
                <?php echo $top_banner_content['login_content']; ?>
            </div>
        </div>
        <?php
    } elseif (!is_user_logged_in() && !empty($top_banner_content['logout_content'])) {
        ?>
        <div class="Log-top-banner">
            <div class="logout-content">
                <?php echo $top_banner_content['logout_content']; ?>
            </div>
        </div>
        <?php
    }
}

include get_stylesheet_directory() . '/Shortcode/resource-shortcode/resource_filter.php';
include get_stylesheet_directory() . '/Shortcode/songs-slider.php';

function enqueue_owl_carousel()
{

    wp_enqueue_style(
        'owl-carousel-css',
        get_stylesheet_directory_uri() . '/assets/OwlCoursel/owl.carousel.min.css'
    );
    wp_enqueue_style(
        'owl-carousel-theme-css',
        get_stylesheet_directory_uri() . '/assets/OwlCoursel/owl.theme.default.min.css'
    );


    wp_enqueue_script(
        'owl-carousel-js',
        get_stylesheet_directory_uri() . '/assets/OwlCoursel/owl.carousel.min.js',
        array('jquery'), // Dependency on jQuery
        '2.3.4', // Version number
        true // Load in footer
    );
}
add_action('wp_enqueue_scripts', 'enqueue_owl_carousel');



function owl_carousel()
{
?>
    <script>
        jQuery(document).ready(function($) {
            var gallery = $(".owl-carousel").owlCarousel({
                loop: true,
                margin: 32,
                nav: true,
                dots: false,
                autoplay: false,
                responsive: {
                    0: {
                        items: 1.75,
                    },
                    400: {
                        items: 2.5,
                    },
                    500: {
                        items: 3.25,
                    },
                    750: {
                        items: 3.5,
                    },
                    1000: {
                        items: 4.75,
                    }
                },
            });
        });
    </script>
<?php
}
add_action('wp_footer', 'owl_carousel');

//Category Combaining from default_post and custom post
function register_shared_categories() {
    register_taxonomy_for_object_type('category', 'songs'); // Attach 'category' to 'songs' post type
    register_taxonomy_for_object_type('topics', 'post'); // Attach 'topics' to 'post' post type

}
add_action('init', 'register_shared_categories');
function hide_custom_post_category_admin_menu() {
    echo '<style>
        #menu-posts-songs ul li:nth-child(4) {
            display: none !important;
        }
        #menu-posts ul li:nth-child(6) {
            display: none;
        }
    </style>';
}
add_action('admin_head', 'hide_custom_post_category_admin_menu');

//Rename The Categories -Custom Post and Default Post
function rename_custom_category( $translated ) {
    //$translated = str_ireplace( 'Category', 'Video Categories', $translated );
    return $translated;
}
add_filter( 'gettext', 'rename_custom_category' );
function rename_default_post_category() {
    global $wp_taxonomies;
    
    if (isset($wp_taxonomies['category'])) {
        $wp_taxonomies['category']->labels->name = 'Teaching Material Categories';       // New Name
        $wp_taxonomies['category']->labels->menu_name = 'Teaching Material Categories'; 
    }
}
add_action( 'init', 'rename_default_post_category' );



// Stop and only redirect to the single post page and display the authentication message
add_filter('mepr-pre-run-rule-redirection', function($redirect, $url, $delim) {
    if (is_singular('post')) {
        return false; // Disable redirection for single posts
    }
    return $redirect; // Preserve default behavior for other content types
}, 10, 3);


//End The Code

function custom_comment_moderation_recipients( $emails, $comment_id ) {
	return array( 'printables@thesingingwalrus.com' );
}
add_filter( 'comment_moderation_recipients', 'custom_comment_moderation_recipients', 10, 2 );
add_filter( 'comment_notification_recipients', 'custom_comment_moderation_recipients', 10, 2 );

add_filter( 'site_transient_update_plugins', function ( $value ) {
    if ( isset( $value->response['related-post/related-post.php'] ) ) {
        unset( $value->response['related-post/related-post.php'] );
    }
    return $value;
});

add_filter('wp_mail', function($args) {
    if (isset($args['subject']) && str_contains($args['subject'], 'Site Recovery Mode')) {
        // Stop sending the recovery email
        return false;
    }
    return $args; // allow other emails
});