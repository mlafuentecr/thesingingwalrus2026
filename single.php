<?php

// =============================================================================
// VIEWS/INTEGRITY/WP-INDEX.PHP
// -----------------------------------------------------------------------------
// Index page output for Integrity.
// =============================================================================

get_header();
?>

<!-- Stripper -->
<div class="products-single-wrapper" data-paroller-factor="-0.1" data-paroller-type="foreground" data-paroller-direction="vertical">
<div class="songs-bg-layer" data-paroller-factor="-0.1" data-paroller-type="foreground" data-paroller-direction="vertical">
<div class="x-row x-container max width">
    <div class="stripper blog-stripper"> 
        <h1 class="blog-title-width"><?php the_title(); ?></h1> 
        <p class="blog-date"><?php echo get_the_date(); ?></p>
    </div>
</div>

<div class="song-lower-img">
    <img src="/wp-content/uploads/2021/09/secondary-cloud-white.png"/>
</div>
<div class="mobile-song-lower-img hide-xl hide-lg">
    <img src="/wp-content/uploads/2021/10/Skype_Picture_2021_10_12T13_58_35_208Z.png"/>
</div>
</div>
</div>
<!-- End Stripper -->

<?php 
 //echo do_shortcode('[rev_slider alias="single-blog"][/rev_slider]');
?>


<div class="bog-wrapper">
<!-- <div class="x-row x-container max width blog-breadcrub-position">
<div class="topics-bread-crumb">
<ul>
    <li><a href="<?php // echo home_url(); ?>">Home</a></li>
    <li><a href="<?php // echo home_url(); ?>/blog">Blog</a></li>
    
</ul>
</div>
</div> -->
<div class="x-row x-container max width">

<div class="<?php x_main_content_class(); ?>" role="main"> 
<?php while ( have_posts() ) : the_post(); ?>
<?php $optional_image = get_field('blog_optional_image'); ?>
<?php if ($optional_image): ?>
<div class="blog-featured-img">
<?php 
    echo '<img src = "'.$optional_image.'">';
?>
</div>
<?php endif; ?>

<div class="blog-content">
    <?php the_content();?>
	<?php if ( !is_user_logged_in() ) {
        echo do_shortcode('[cs_gb id=4652]');
    } ?>
	<?php echo do_shortcode('[cs_gb name="blog-author"]'); ?>
</div>  
	
	<!-- Comment section for logged in users -->
<?php if ( is_user_logged_in() ): ?>
<div class="comment-section-bg">
    <p class="comm_head">Hello Walrus friends!</p>
    <p class="comm-desc">Our comment section is for all you amazing parents and educators out there who would like to share their thoughts, their experience, and their passion for education with the community. </p>
    <p class="comm-desc">We look forward to hearing from you!</p>
</div>

<div class="comment-section-bg-mobile">
<div class="test">
</div>
<div class="test1">
    <p class="comm_head-nobile">Hello Walrus friends!</p>
    <p class="comm-desc-mobile">Our comment section is for all you amazing parents and educators out there who would like to share their thoughts, their experience, and their passion for education with the community. We look forward to hearing from you!</p>
 </div>   
</div>

<div class="comments-list">
<?php comments_template( '', true ); ?>
</div>
<?php endif; ?>
<!-- End Comment section for logged in users -->
	
	
<div class="related-posts">
    <h2>Here is more!</h2>
   <?php //echo do_shortcode( '[related_post]' ); 
	$post_id = get_the_ID();
	$related_post = get_post_meta($post_id, 'related_post_ids', true);
	if(!empty($related_post)){
		$related_post_ids = implode(",",$related_post);
		echo do_shortcode( '[related_post post_ids="'.$related_post_ids.'"]' );
	}
	?>
</div>
       

<?php endwhile; ?>

	
</div>
</div>
</div>
<div class="blog-lower-section">
    <img src="/wp-content/uploads/2021/10/footer-clouds.png"/>
</div>

<?php get_footer(); ?>


<!--<script>
if (window.matchMedia('(min-width: 1200px)').matches){
jQuery('.parallax-window-two').parallax({
    imageSrc: '/wp-content/uploads/2021/09/secondary-cloud-blue.png',
    position: 'center center'
});
}
if (window.matchMedia('(min-width: 1200px)').matches){
jQuery('.parallax-window').parallax({
    imageSrc: '/wp-content/uploads/2021/11/secondary-cloud-orange.jpg'
});
}
</script>-->

<script>
if (window.matchMedia('(min-width: 1200px)').matches){
jQuery(".products-single-wrapper").paroller();
}
</script>