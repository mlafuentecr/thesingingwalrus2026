<?php


get_header();


?>

<?php
$term = get_queried_object();

$child_terms = get_terms( array(
    'taxonomy' => 'topics',
    'hide_empty' => false,
) );
//$child_terms = get_terms( $current_term->taxonomy, $args );
?>
<!-- Stripper -->

<div class="products-single-wrapper" data-paroller-factor="-0.1" data-paroller-type="foreground" data-paroller-direction="vertical">
<div class="songs-bg-layer" data-paroller-factor="-0.1" data-paroller-type="foreground" data-paroller-direction="vertical">
<div class="x-row x-container max width">
    <div class="stripper">
        <h1><?php single_cat_title(); ?></h1>
        <p><?= category_description(); ?></p>
        
        <?php
    	$desc = get_field('secondary_description',$term);
    	?>
        <?php if($desc): ?>
        <p class="sec-desc"><?php echo $desc; ?></p>
        <?php endif; ?>
       
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
<br />


<!-- End Stripper -->
<div class="category-page-template custom-taxonomy-padding">
<!-- Bread Crumb -->
<!-- <div class="x-row x-container max width">
<div class="x-row-inner max width post-details-section">
<div class="topics-bread-crumb">
<ul>
    <li><a href="<? /*echo home_url(); ?>">Home</a></li>
    <li><a href="<? echo home_url(); ?>/topics">Topics</a></li>
    <?
    $term = get_term_by("slug", get_query_var("term"), get_query_var("taxonomy") );
    $parent = get_term($term->parent, get_query_var("taxonomy"));
    while ($parent->term_id) {
        echo "<li><a href=\"" . home_url() . "/songs_category/" . $parent->slug . "\">" . $parent->name . "</a></li>";
        $parent = get_term($parent->parent, get_query_var("taxonomy"));
    }
    echo "<li> $term->name </li>";*/
    ?>
</ul>
</div>
</div>
</div> -->
<!-- Bread Crumb -->

<!-- Content wrapper -->

<div class="x-row x-container max width">
<div class="x-row-inner max width post-details-section">
    <div class="x-column x-sm x-1-3">
    <div class="songs-category-list">
        <?php get_sidebar(); ?>
    </div>
    </div>
    
    <div class="x-column x-sm x-2-3">
    <div class="songs-details-section">
    <div class="<?php x_main_content_class(); ?>" role="main"> 
        <?php x_get_view( 'global', '_index' ); ?>
    </div>
    </div>

</div>
</div>
<!-- End Content wrapper -->
</div>
<div class="cat-song-lower-img">
    <img src="/wp-content/uploads/2021/10/footer-clouds.png"/>
</div> 
</div> 
<!--<div class="bg-layer-image">
 <img src="/wp-content/uploads/2021/09/home-parallax1-1-1-1.png"/>
</div>-->

<?php 
echo do_shortcode('[cs_gb id="3066"]');
get_footer(); ?>

<!--<script>
if (window.matchMedia('(min-width: 1200px)').matches){
jQuery('.parallax-window-two').parallax({
    imageSrc: '/wp-content/uploads/2021/09/secondary-cloud-blue.png',
    position: 'center center',
    naturalHeight: 500
});
}
if (window.matchMedia('(min-width: 1200px)').matches){
jQuery('.parallax-window').parallax({
    imageSrc: '/wp-content/uploads/2021/11/secondary-cloud-orange.jpg',
    position: 'center center',
    naturalHeight: 500
});
}
</script>-->
<script>
if (window.matchMedia('(min-width: 1200px)').matches){
jQuery(".products-single-wrapper").paroller();
}

jQuery(document).ready(function(){

    jQuery('.songs-details-section').each(function(){  
        var highestBox = 0;

        jQuery(this).find('.entry-wrap').each(function(){
            if(jQuery(this).height() > highestBox){  
                highestBox = jQuery(this).height();  
            }
        })

        jQuery(this).find('.entry-wrap').height(highestBox);
    });    


});
</script>
