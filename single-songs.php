<?php

// =============================================================================
// VIEWS/INTEGRITY/WP-INDEX.PHP
// -----------------------------------------------------------------------------
// Index page output for Integrity.
// =============================================================================

get_header();
?>

<!-- Header strip -->
<div class="topiscs-single-wrapper">
<div class="x-row x-container max width">
    <div class="stripper">
        <p><?php the_title(); ?></p>
    </div>
</div>
<div class="mobile-song-lower-img hide-xl hide-lg">
    <img src="/wp-content/uploads/2021/10/Skype_Picture_2021_10_12T13_58_35_208Z.png"/>
</div>
</div>
<!-- End Header strip -->



<!-- Bread Crumb -->
<!-- <div class="x-row x-container max width">
<div class="x-row-inner max width post-details-section">
<div class="topics-bread-crumb">
<ul>
    <li><a href="<? //echo home_url(); ?>">Home</a></li>
    <?php //if (( $post->post_type == 'songs' ) && has_term( 'free-stuff', 'topics' )): ?>
    <li><a href="<? //echo home_url(); ?>/free-stuff">Free Stuff</a></li>
    <?php //else: ?>
    <li><a href="<? //echo home_url(); ?>/topics">Topics</a></li>
    <?php //endif; ?>
    <?
    /*$term = get_term_by("slug", get_query_var("term"), get_query_var("taxonomy") );
    $parent = get_term($term->parent, get_query_var("taxonomy"));
    while ($parent->term_id) {
        echo "<li><a href=\"" . home_url() . "/songs_category/" . $parent->slug . "\">" . $parent->name . "</a></li>";
        $parent = get_term($parent->parent, get_query_var("taxonomy"));
    }?>
    <li> <?php the_title();*/ ?> </li>
    
</ul>
</div>
</div>
</div> -->
<!-- Bread Crumb -->


<div class="single-topic-wrappers">
<div class="x-row x-container max width">
<div class="x-row-inner max width page-container">
<div class="x-column x-sm x-1-3">
<!-- Sidebar -->
<div class="songs-category-list">
    <!-- <?php get_sidebar(); ?> -->
       <ul class="songs-icon view-all-song-resource"> 
            <li>
                <a href="/resources/">View All Resources</a>
            </li>
        <ul>

</div>
<!-- End Sidebar -->
</div>

<div class="x-column x-sm x-2-3">
<div class="<?php x_main_content_class(); ?>" role="main"> 
<?php while ( have_posts() ) : the_post(); ?>
<?php if ( is_user_logged_in() ): ?>

<!-- Favoourite icon -->
<div class="my-fav">
    <?php echo do_shortcode('[ccc_my_favorite_select_button text="Save to Favourites" style=""]'); ?>
</div>
<!-- End Favoourite icon -->

<?php endif; ?>
<div class="topic-detail-content-wrapper">
    <h2><?= the_title(); ?></h2>
    <p><?php the_content();?></p>
</div>

<!-- Vimeo video embeded code -->
<?php $vimeo = get_field('video'); ?>
<?php if ( (( $post->post_type == 'songs' ) && has_term( 'free-stuff', 'topics' )) || is_user_logged_in() && $vimeo ): ?>
<div class="vimeo-video one-div">
<?php
    //echo $vimeo; 
?>
<?php
            // Check if the oEmbed output includes an iframe
            echo preg_replace('/<iframe(.*?)>/i', '<iframe$1 allow="autoplay; fullscreen" allowfullscreen>', $vimeo);
        ?>
</div>
<?php endif; ?>

<?php $iframe = get_field('video'); ?>
<?php if ( !(( $post->post_type == 'songs' ) && has_term( 'free-stuff', 'topics' )) && !is_user_logged_in() && $iframe ): ?>

<div class="vimeo-video two-div">
<?php

// Load value.

// Use preg_match to find iframe src.
preg_match('/src="(.+?)"/', $iframe, $matches);
$src = $matches[1];

// Add extra parameters to src and replcae HTML.
$params = array(
    'controls'  => 0,
    'hd'        => 1,
    'autohide'  => 1
);
$new_src = add_query_arg($params, $src);
$iframe = str_replace($src, $new_src, $iframe);

// Add extra attributes to iframe HTML.
$attributes = 'frameborder="0"';
$iframe = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframe);

// Display customized HTML.
echo $iframe;
?>
</div>
<?php endif; ?>



<?php $second_vimeo = get_field('second_video_for_logged_in_user');
      $third_vimeo = get_field('third_video_for_logged_in_user');
?>
<?php if ( is_user_logged_in() ): ?>
<?php //if($second_vimeo && $third_vimeo): ?>
<div class="vimeo-video-logged-user">
<div class="x-row-inner max width page-container">
	<?php if($second_vimeo): ?>
<div class="x-column x-sm x-1-2">
    <div class="second-vimeo">
        <?php

            echo $second_vimeo; 
        
        ?>
    </div>
</div>
	<?php endif; ?>
	<?php if($third_vimeo): ?>
<div class="x-column x-sm x-1-2">
    <div class="third-vimeo">
        <?php
        
            echo $third_vimeo; 
        
        ?>
    </div>
</div>
	<?php endif; ?>
</div>
</div>
<?php //endif; ?>
<?php endif; ?>
<!-- End Vimeo video embeded code -->

<!-- Tabs -->
<?php if (get_field('tabs')): ?>
<div class="topic-tabs">
    <?php x_get_view( x_get_stack(), 'topics/tabs');?>
</div>
<?php endif; ?>
<!-- End Tabs -->

<!-- Login link for logged out users -->
<?//php if ( !is_user_logged_in() && !(( $post->post_type == 'songs' ) && has_term( 'free-stuff', 'topics' ) )): ?>
<!--<div class="single-login-link">
<p>You must be <a href="/login/">logged in</a> and have an active subscription to view all premium content. <a href="/#signup-section">Subscribe today</a> if you do not have a subscription</p> 
</div>-->
<?//php endif; ?>
<!-- End Login link for logged out users -->

<!-- Related Posts-->
<?php if ( is_user_logged_in() ): ?>
<div class="related-posts">
    <h2>Related</h2>
   <?php echo do_shortcode( '[related_post]' ); ?>
</div>
<?php endif; ?>
<!-- End Related Posts-->

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
<?php endwhile; ?>
</div>
</div>
</div>
</div>

<!-- Registered User for non logged in users -->
<?php if ( !is_user_logged_in() ): ?>
<div class="join-now-global">
    <?php echo do_shortcode('[cs_gb name="join-now"]'); ?>
</div>
<?php endif; ?>
<!-- End Registered User for non logged in users -->


<?php if ( is_user_logged_in() ): ?>
<div class="cat-song-lower-img">
    <img src="/wp-content/uploads/2021/10/footer-clouds.png"/>
</div> 
<?php endif; ?>
</div>

<?php get_footer(); ?>

<script>
jQuery(document).ready(function(){

    jQuery('.related-post').each(function(){  
        var highestBox = 0;

        jQuery(this).find('.item').each(function(){
            if(jQuery(this).height() > highestBox){  
                highestBox = jQuery(this).height();  
            }
        })

        jQuery(this).find('.item').height(highestBox);
    });    


});
</script>