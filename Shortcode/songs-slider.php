<?php

function gallery($atts) {

         // Extract shortcode attributes
    $atts = shortcode_atts(
        array(
            'songs_id'   => '', // Term IDs for 'topics' taxonomy (songs)
            'blog_id' => '', // Term IDs for 'category' taxonomy (posts)
        ),
        $atts
    );
    
    // Define post types and taxonomies in code
    $post_types = array('songs', 'post');
    
    // Prepare tax_query array
    $tax_query = array('relation' => 'OR');

    // Process songs_id (topics taxonomy)
    if (!empty($atts['songs_id'])) {
        $tax_query[] = array(
            'taxonomy' => 'topics',
            'field'    => 'term_id',
            'terms'    => explode(',', $atts['songs_id']), // Convert CSV to array
        );
    }

    // Process blog_id (category taxonomy)
    if (!empty($atts['blog_id'])) {
        $tax_query[] = array(
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => explode(',', $atts['blog_id']), // Convert CSV to array
        );
    }
    
    // Define the query arguments
    $args = array(
        'post_type'      => $post_types,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'tax_query'      => count($tax_query) > 1 ? $tax_query : array(), // Only apply if conditions exist
    );
    // Run the query
    $songs_query = new WP_Query($args);

    // Start output buffering
    ob_start();

    if ($songs_query->have_posts()) : ?>
        <div class="category-slider">
            <div class="owl-carousel owl-theme">
                <?php while ($songs_query->have_posts()) : $songs_query->the_post(); ?>
                    <div class="item">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium', ['alt' => get_the_title()]); // Display featured image ?>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php
        wp_reset_postdata(); // Reset the query
    else : 
        echo '<p>No songs found.</p>'; // Message if no posts found
    endif;

    ?>
    <style>
        .category-slider {
            position: relative;
        }

        .category-slider .owl-nav button.owl-prev {
            position:absolute;
            left: -30px;
            top:30%;
            scale:1.5;
        }

        .category-slider .owl-nav button.owl-next {
            position:absolute;
            right:-30px;
            top:30%;
            scale:1.5;
        }

        .category-slider span {
            color: red;
            display: block;
            scale: 2;
        }
        .category-slider .owl-prev:hover,
        .category-slider .owl-next:hover
        {
            background: transparent !important;
        }
        .category-slider .owl-item:hover .item a::before{
            content: url(/wp-content/uploads/2021/10/link.png) !important;
        }
        .category-slider .owl-item:hover .item a::after{
            content: ''; /* Add this line */
        }
        .category-slider .item a::before{
            transform: scale(0.4) !important;
            width: 1em !important;
            display: inline-block !important;
            position: absolute;
            top: 0;
            bottom:0;
            right: 56%;
            /* left: 0; */
            z-index:2;
            line-height: 1em !important;
        }
        .category-slider .item a::after{
            background: rgba(255, 255, 255, 0.75);
            position: absolute; /* Required for positioning */
            top: 0; /* example positioning */
            left: 0; /* example positioning */
            right: 0; /* example positioning */
            bottom: 0; /* example positioning */
            z-index: 1; /* Ensure visibility over other elements */
        }
        .category-slider .item img{
            border-radius: 20px;
        }

        @media(max-width:768px){
        .category-slider .owl-nav button.owl-prev {
            left: -20px;
        }

        .category-slider .owl-nav button.owl-next {
            right:-20px;

        }
        .category-slider .owl-nav button.owl-next {
            top:15%;
        }
        .category-slider .owl-nav button.owl-prev {
            top:15%;
        }
        .category-slider .item a::before{
            /* top: -45% !important; */
            /* right: 60%; */
        }

        }

        @media(max-width:1024px){
            .category-slider .item a::before{
                transform: scale(0.3) !important;
            }
        }
    </style>
     <?php

    return ob_get_clean(); // Return the output buffer content
}

// Register the shortcode
add_shortcode('song-slider', 'gallery');
