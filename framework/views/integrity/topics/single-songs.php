<?php

// =============================================================================
// VIEWS/INTEGRITY/WP-INDEX.PHP
// -----------------------------------------------------------------------------
// Index page output for Integrity.
// =============================================================================

get_header();
?>

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

<!-- Bread Crumb -->
<div class="x-row x-container max width">
<div class="x-row-inner max width post-details-section">
<div class="topics-bread-crumb">
<ul>
    <li><a href="<? echo home_url(); ?>">Home</a></li>
    <li><a href="<? echo home_url(); ?>/topics">Topics</a></li>
    <?
    $term = get_term_by("slug", get_query_var("term"), get_query_var("taxonomy") );
    $parent = get_term($term->parent, get_query_var("taxonomy"));
    while ($parent->term_id) {
        echo "<li><a href=\"" . home_url() . "/songs_category/" . $parent->slug . "\">" . $parent->name . "</a></li>";
        $parent = get_term($parent->parent, get_query_var("taxonomy"));
    }?>
    <li> <?php the_title(); ?> </li>
    
</ul>
</div>
</div>
</div>
<!-- Bread Crumb -->


<div class="single-topic-wrappers">
<div class="x-row x-container max width">
<div class="x-row-inner max width page-container">
<div class="x-column x-sm x-1-3">
<div class="songs-category-list">
    <?php get_sidebar(); ?>
</div>
</div>

<div class="x-column x-sm x-2-3">
<div class="<?php x_main_content_class(); ?>" role="main"> 
<?php while ( have_posts() ) : the_post(); ?>
<?php if ( is_user_logged_in() ): ?>
<div class="my-fav">
    <?php echo do_shortcode('[ccc_my_favorite_select_button text="Save to Favourites" style=""]'); ?>
</div>
<?php endif; ?>
<div class="topic-detail-content-wrapper">
    <h3><?= the_title(); ?></h3>
    <p><?php the_content();?></p>
</div>

<div class="vimeo-video">
    <?php if (get_field('video')):?>
        <img src="<?= the_field('video');?>"/>
    <?php endif; ?>
</div>

<div class="topic-tabs">
    <?php x_get_view( x_get_stack(), 'topics/tabs');?>
</div>
<?php endwhile; ?>
<?php if ( !is_user_logged_in() ): ?>
<div class="single-login-link">
<p>You must be <a href="/login/">logged in</a> and have an active subscription to view all premium content. <a href="/#signup-section">Subscribe today</a> if you do not have a subscription</p> 
</div>
<?php endif; ?>

<div class="related-posts">
    <h3>Related</h3>
   <?php echo do_shortcode( '[related_post]' ); ?>
</div>

<?php if ( is_user_logged_in() ): ?>
<div class="comment-section-bg">
    <p class="comm_head test">Your Comments</p>
    <p class="test">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse mattis congue faucibus. Curabitur mollis in mi eu sodales. Phasellus egestas sed ex non elementum. </p>
</div>  
<div class="comments-list">
<?php comments_template( '', true ); ?>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>

<div class="join-now-global">
    <?php echo do_shortcode('[cs_gb name="join-now"]'); ?>
</div>

</div>
<?php get_footer(); 