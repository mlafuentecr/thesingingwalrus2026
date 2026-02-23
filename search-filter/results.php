<?php
/**
 * Search & Filter Pro 
 *
 * Sample Results Template
 * 
 * @package   Search_Filter
 * @author    Ross Morsali
 * @link      https://searchandfilter.com
 * @copyright 2018 Search & Filter
 * 
 * Note: these templates are not full page templates, rather 
 * just an encaspulation of the your results loop which should
 * be inserted in to other pages by using a shortcode - think 
 * of it as a template part
 * 
 * This template is an absolute base example showing you what
 * you can do, for more customisation see the WordPress docs 
 * and using template tags - 
 * 
 * http://codex.wordpress.org/Template_Tags
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('excerpt_more', 'search_new_excerpt_more');

add_filter( 'get_the_excerpt', 'replace_post_excerpt_filter' );
 

if ( $query->have_posts() )
{
    
	?>
	
<div class='search-filter-results-list'>
	
	
	
	<?php
	while ($query->have_posts())
	{
		$query->the_post();
		
		?>
		<div class="search-results-item songs-details-section">
            
            <a href="<?php the_permalink();?>" class="search-hove-link"><?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() ) {  the_post_thumbnail('full', array("class" => "aligncenter img-responsive")); } ?>
            </a>
            <?php 
                $postcat = get_the_category_list( '|','',$post->ID );
            ?>
            <?php 
            //echo get_the_term_list( $post->ID, 'topics', 'Category: ', ', ' );
            ?>

            <?php if($postcat): ?>
            <p class="cat-name">Category: <?= $postcat; ?></p>
            <?php else: ?>
            <p class="cat-name-song">Category: <a href="/topics">Songs</a></p>
            <?php endif; ?>
            <a href="<?php the_permalink();?>">
			<h2 class="entry-title"><?php the_title(); ?></h2>
            </datalist>
            

			
		</div>
		
	
		<?php
	}
	?>
	</div>
	<?php
}
else
{
    if(!isset($_GET['sf_paged']))
    echo '<div class="no-result">';
	echo ("No Results Found");
    echo '</div>';
}
?>

<script>
jQuery(".cat-name a").attr("href", "/blog");
</script>