<?php
/* Template Name: Blog Category */ 
get_header();

?>
<?php
$categories = get_categories( $args );
$args = array(
    'child_of'                 => 0,
    'parent'                   => '',
    'hide_empty'               => 0,
    'exclude'                  => '' );
//print_r($categories);exit;
?>
<!-- Stripper -->

<div class="products-single-wrapper" data-paroller-factor="-0.1" data-paroller-type="foreground" data-paroller-direction="vertical">
<div class="songs-bg-layer" data-paroller-factor="-0.1" data-paroller-type="foreground" data-paroller-direction="vertical">
<div class="x-row x-container max width">
    <div class="stripper">
        <h1><?php //the_title(); ?>Blog</h1>
        <p><?php //any description ?></p>
        
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
    <li><a href="<? //echo home_url(); ?>">Home</a></li>
    <li><a href="<? //echo home_url(); ?>/blog">Blog</a></li>
    
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
        <?php //get_sidebar(); ?>
		<aside class="x-sidebar right" role="complementary">
            <div id="block-2" class="widget widget_block">
				<p> </p>
				<?php

					$child_terms = get_terms( array(
						'taxonomy' => 'category',
						'hide_empty' => false,
						'exclude' => '36,4,5,1',
					) );
					
					?>
					<div class="song-sidebar-bg">
						<ul class="song-icons">
							<?php foreach ($child_terms as $term) { ?>
								<?php $icon = get_field('blog_category_icon', $term->taxonomy . '_' . $term->term_id); ?>
								<li>
									<img src="<?php echo $icon; ?>" class="icona" alt="" />
									<p><a href="<?php echo get_term_link( $term->name, $term->taxonomy ); ?>"><?php echo $term->name; ?></a></p>
								</li>
								

							<?php } ?>
							
						</ul>
					</div>
				<p></p>
			</div>      
		</aside>
    </div>
    </div>
    
    <div class="x-column x-sm x-2-3 songs-details-section">
    <!--<div class="songs-details-section">
    <div class="<?php x_main_content_class(); ?>" role="main"> -->
        <?php //x_get_view( 'global', '_index' );
		echo do_shortcode('[ess_grid alias="grid-2"][/ess_grid]');?>
    <!--</div>
    </div>-->

</div>
</div>
<!-- End Content wrapper -->
</div>
<div class="cat-song-lower-img">
    <img src="/wp-content/uploads/2021/10/footer-clouds.png"/>
</div> 
</div> 

<?php get_footer(); ?>

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
<style>
	.esg-media-poster{
		border-radius:25px;
		height: calc(100% - 72px );
	}
	.esg-entry-cover{
		border-radius:25px;
	}
</style>