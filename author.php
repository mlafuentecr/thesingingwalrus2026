<?php

get_header();

?>
<!-- Stripper -->
<div class="products-single-wrapper" data-paroller-factor="-0.1" data-paroller-type="foreground" data-paroller-direction="vertical">
<div class="songs-bg-layer" data-paroller-factor="-0.1" data-paroller-type="foreground" data-paroller-direction="vertical">
<div class="x-row x-container max width">
    <div class="stripper blog-stripper"> 
        <h1 class="blog-title-width"><?php the_author(); ?></h1> 
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

		   <?php echo do_shortcode('[cs_gb name="author-page"]'); ?>
		
<?php get_footer(); ?>
