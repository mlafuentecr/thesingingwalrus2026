<?php
/**
 * Portfolio Filter Functions
 */

/**
 * Professional Fuzzy Search Implementation
 */
class PortfolioFuzzySearch {
    
    public static function search($search_term, $post_types = ['post', 'songs']) {
        global $wpdb;
        
        // Require minimum 2 characters for search term
        if (empty($search_term) || strlen(trim($search_term)) < 2) {
            return [];
        }
        
        // Normalize search term
        $normalized_search = self::normalize_text($search_term);
        
        // Sanitize post types
        $post_types = array_map('sanitize_text_field', (array) $post_types);
        
        // Get all posts
        $posts = $wpdb->get_results($wpdb->prepare(
            "SELECT ID, post_title, post_type, post_date 
             FROM {$wpdb->posts} 
             WHERE post_type IN ('" . implode("','", $post_types) . "') 
             AND post_status = 'publish'
             ORDER BY post_date DESC"
        ));
        
        $scored_results = [];
        
        foreach ($posts as $post) {
            $normalized_title = self::normalize_text($post->post_title);
            $score = self::calculate_similarity($normalized_search, $normalized_title,$post->post_title);
        
            // Stricter threshold
            if ($score > 0.3) { // Raised to 0.5 for stricter matching
                $scored_results[] = [
                    'post' => $post,
                    'score' => $score
                ];
            }
        }
        
        // Sort by relevance score
        usort($scored_results, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return array_column($scored_results, 'post');
    }
    
    private static function normalize_text($text) {
        $text = strtolower(trim($text));
        
        // Normalize apostrophes and split contractions
        $text = str_replace(["’", "‘", "‚", "‛"], "'", $text);
        $text = preg_replace("/(['])(?=\w)/", '$1 ', $text);
        
        // Remove punctuation, keep spaces
        $text = preg_replace('/[^\w\s-]/', ' ', $text);
        
        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        return $text;
    }
    
    private static function calculate_similarity($search, $title, $original_title = '') {
        // Exact match
        if ($search === $title) {
            return 1.0;
        }
        
        // Define stop words
        $stop_words = ['a', 'an', 'the', 'is', 'it',  'and', 'or', 'in', 'on', 'at', 'by'];
        $search_words = array_diff(explode(' ', $search), $stop_words);
        $title_words = array_diff(explode(' ', $title), $stop_words);
        
        $matched_words = 0;
        $total_search_words = count(array_filter($search_words,  function($word) {

            return strlen(trim($word)) >= 1; // Allow single-character numbers like "2"

        }));
        
        foreach ($search_words as $search_word) {
            if (empty(trim($search_word))) {
                continue; // Skip words shorter than 4 letters
            }
            
            foreach ($title_words as $title_word) {
                // Skip short title words
                 if (empty(trim($title_word))) {
                    continue;
                }
                
                // Exact word match
                if ($search_word === $title_word) {
                    $matched_words += 1.0;
                    break;
                }
                
                // Significant substring match
                if (strpos($title_word, $search_word) !== false) {
                    // Ensure search word is a significant portion of title word
                    $length_ratio = strlen($search_word) / strlen($title_word);
                    if ($length_ratio >= 0.4) { // At least 60% of title word
                        $matched_words += 0.8;
                        break;
                    }
                }
                
                // Similarity match
                $similarity = similar_text($search_word, $title_word, $percent);
                if ($percent > 80) { // Stricter similarity
                    $matched_words += ($percent / 100);
                    break;
                }
            }
        }
         // Boost score for numeric terms in original title

        if (preg_match('/\d+/', $search) && preg_match('/\d+/', $original_title)) {

            $matched_words *= 1.2; // Boost by 20% for numeric relevance

        }
        
        return $total_search_words > 0 ? $matched_words / $total_search_words : 0;
    }
}

/**
 * Get filtered portfolio items based on provided filters
 * 
 * @param array $filters Filter parameters
 * @param int $page Current page number
 * @param int $posts_per_page Number of posts per page
 * @return mixed HTML content or response array
 */
function get_filtered_portfolios($filters = [], $page = 1, $posts_per_page = 15) {

    // Sanitize inputs

    $search = !empty($filters['search']) ? sanitize_text_field($filters['search']) : '';

    $type = !empty($filters['type']) && in_array($filters['type'], ['post', 'songs']) ? sanitize_text_field($filters['type']) : '';

    $sort = !empty($filters['sort']) ? sanitize_text_field($filters['sort']) : 'recent';

    

    // Default query arguments

    $args = [

        'post_type'      => $type ? $type : ['post', 'songs'],

        'posts_per_page' => $posts_per_page,

        'paged'          => max(1, (int) $page),

        'post_status'    => 'publish'

    ];



    $post_ids_to_include = [];



    // If search keyword is provided

    if (!empty($search)) {

        // Find posts by category/topic name

        $category = get_term_by('name', $search, 'category');

        $topic = get_term_by('name', $search, 'topics');



        if ($category) {

            $cat_posts = get_posts([

                'post_type' => $args['post_type'],

                'category' => $category->term_id,

                'posts_per_page' => -1, // Get all posts in this category

                'fields' => 'ids',

            ]);

            $post_ids_to_include = array_merge($post_ids_to_include, $cat_posts);

        }



        if ($topic) {

            $topic_posts = get_posts([

                'post_type' => $args['post_type'],

                'tax_query' => [[

                    'taxonomy' => 'topics',

                    'field'    => 'term_id',

                    'terms'    => $topic->term_id,

                ]],

                'posts_per_page' => -1, // Get all posts in this topic

                'fields' => 'ids',

            ]);

            $post_ids_to_include = array_merge($post_ids_to_include, $topic_posts);

        }



        // Find posts by fuzzy title search

        $fuzzy_results = PortfolioFuzzySearch::search($search, (array) $args['post_type']);

        if (!empty($fuzzy_results)) {

            $fuzzy_ids = array_column($fuzzy_results, 'ID');

            $post_ids_to_include = array_merge($post_ids_to_include, $fuzzy_ids);

        }



        // Remove duplicates and apply the combined list to the query

        $post_ids_to_include = array_unique($post_ids_to_include);



        if (!empty($post_ids_to_include)) {

            $args['post__in'] = $post_ids_to_include;

            $args['orderby'] = 'post__in'; // Maintain relevance order

        } else {

            // Force no results if no matches found

            $args['post__in'] = [0];

        }

    }

    

    // If a topic/category filter is explicitly selected, override the search results

    if (!empty($filters['topic'])) {

        $topic = sanitize_text_field($filters['topic']);

        $args['tax_query'] = [

            'relation' => 'OR',

            [

                'taxonomy' => 'category',

                'field'    => 'slug',

                'terms'    => $topic,

            ],

            [

                'taxonomy' => 'topics',

                'field'    => 'slug',

                'terms'    => $topic,

            ],

        ];

        // Remove 'post__in' to avoid conflict with tax_query

        unset($args['post__in']);
        unset($args['order']);
        unset($args['orderby']);

    }



    // Apply sorting

    if ($sort === 'alphabetical') {

        $args['orderby'] = ['title' => 'ASC'];

        $clauses_filter = function ($clauses) {

            global $wpdb;

            $clauses['orderby'] = "CASE 

                WHEN {$wpdb->posts}.post_title REGEXP '^[A-Za-z]' THEN 0 

                ELSE 1 

            END, {$wpdb->posts}.post_title ASC";

            return $clauses;

        };

        add_filter('posts_clauses', $clauses_filter);

    } else if ($sort === 'recent') {

        // Use post__in order from search, or fall back to date if no search

        if (!isset($args['post__in'])) {

            $args['orderby'] = 'date';
            $args['order']   = 'DESC';

        }

    }



    // Execute the query

    $query = new WP_Query($args);



    // Remove clauses filter if added

    if ($sort === 'alphabetical') {

        remove_filter('posts_clauses', $clauses_filter);

    }



    // Prepare response

    $response = [

        'html'        => '',

        'max_pages'   => $query->max_num_pages,

        'found_posts' => $query->found_posts

    ];



    ob_start();



    if ($query->have_posts()) {

        while ($query->have_posts()) : $query->the_post();

            get_portfolio_item_template();

        endwhile;

    } else {

        echo '<div class="no-results">' . 

            apply_filters('wpml_translate_single_string',

                'No items have been found that match your criteria. Please try adjusting your filter criteria.',

                'GeneratePress Child',

                'No_items_found') . 

            '</div>';

    }



    wp_reset_postdata();

    $response['html'] = ob_get_clean();



    return is_admin() ? $response : $response['html'];

}

/**
 * Render portfolio item template
 */
function get_portfolio_item_template() {
    ?>
    <div class="resource-item">
        <?php if (has_post_thumbnail()): ?>
            <a href="<?php the_permalink(); ?>" class="resource-image-link">
                <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
            </a>
        <?php endif; ?>
        <a href="<?php the_permalink(); ?>" class="resource-title-link">
            <h3><?php the_title(); ?></h3>
        </a>
    </div>
    <?php
}

/**
 * Portfolio filter shortcode
 */
function portfolio_filter_shortcode($atts) {
    $content_types = [
        'post' => 'Teaching Materials / Blog',
        'songs' => 'Videos'
    ];
    $sort_value = [
        'recent' => 'Most Recent',
        'alphabetical' => 'Alphabetical'
    ];

    $topics = get_combined_topics();
    
    wp_enqueue_script('portfolio-filter-script');
    $nonce = wp_create_nonce('portfolio_filter_nonce');
    
    ob_start();
    ?>
    <div class="resource-filters">
        <div class="resource-filter-item">
            <h6 class="filter-heading"><?php echo apply_filters('wpml_translate_single_string', 'Type', 'GeneratePress Child', 'TYPE'); ?>
                <!--<div class="tooltip-container">
                    <svg class="info-icon" stroke="currentColor" fill="currentColor" stroke-width="0" version="1.1" 
                         viewBox="0 0 17 17" height="20px" width="20px" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.5 0c-4.687 0-8.5 3.813-8.5 8.5s3.813 8.5 8.5 8.5 8.5-3.813 8.5-8.5-3.813-8.5-8.5-8.5zM8.5 16c-4.136 0-7.5-3.364-7.5-7.5s3.364-7.5 7.5-7.5 7.5 3.364 7.5 7.5-3.364 7.5-7.5 7.5zM9 12.369h0.979v1h-2.958v-1h0.979v-4.42h-0.946v-1h1.946v5.42zM7.185 4.986c0-0.545 0.441-0.986 0.986-0.986s0.985 0.441 0.985 0.986c0 0.543-0.44 0.984-0.985 0.984s-0.986-0.441-0.986-0.984z"></path>
                    </svg>
                    <span class="tooltip-text">
                        <?php echo get_field('resource_type') ? get_field('resource_type') : 'Default tooltip text'; ?>
                    </span>
                </div> -->
            </h6>
            <?php echo get_filter_dropdown('type', apply_filters('wpml_translate_single_string', 'Select', 'GeneratePress Child', 'Select'), $content_types); ?>
        </div>

        <div class="resource-filter-item">
            <h6 class="filter-heading"><?php echo apply_filters('wpml_translate_single_string', 'Topic', 'GeneratePress Child', 'Topic'); ?>
               <!-- <div class="tooltip-container">
                    <svg class="info-icon" stroke="currentColor" fill="currentColor" stroke-width="0" version="1.1" 
                         viewBox="0 0 17 17" height="20px" width="20px" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.5 0c-4.687 0-8.5 3.813-8.5 8.5s3.813 8.5 8.5 8.5 8.5-3.813 8.5-8.5-3.813-8.5-8.5-8.5zM8.5 16c-4.136 0-7.5-3.364-7.5-7.5s3.364-7.5 7.5-7.5 7.5 3.364 7.5 7.5-3.364 7.5-7.5 7.5zM9 12.369h0.979v1h-2.958v-1h0.979v-4.42h-0.946v-1h1.946v5.42zM7.185 4.986c0-0.545 0.441-0.986 0.986-0.986s0.985 0.441 0.985 0.986c0 0.543-0.44 0.984-0.985 0.984s-0.986-0.441-0.986-0.984z"></path>
                    </svg>
                    <span class="tooltip-text"><?php echo get_field('resource_topic') ? get_field('resource_topic') : 'Default tooltip text'; ?></span>
                </div>-->
            </h6>
            <?php echo get_filter_dropdown('topic', apply_filters('wpml_translate_single_string', 'Select', 'GeneratePress Child', 'Select'), $topics, 'slug', 'name'); ?>
        </div>

        <div class="resource-filter-item">
            <h6 class="filter-heading"><?php echo apply_filters('wpml_translate_single_string', 'Sort By', 'GeneratePress Child', 'Sort By'); ?>
                <!--<svg class="info-icon" stroke="currentColor" fill="currentColor" stroke-width="0" version="1.1" viewBox="0 0 17 17" height="20px" width="20px" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.5 0c-4.687 0-8.5 3.813-8.5 8.5s3.813 8.5 8.5 8.5 8.5-3.813 8.5-8.5-3.813-8.5-8.5-8.5zM8.5 16c-4.136 0-7.5-3.364-7.5-7.5s3.364-7.5 7.5-7.5 7.5 3.364 7.5 7.5-3.364 7.5-7.5 7.5zM9 12.369h0.979v1h-2.958v-1h0.979v-4.42h-0.946v-1h1.946v5.42zM7.185 4.986c0-0.545 0.441-0.986 0.986-0.986s0.985 0.441 0.985 0.986c0 0.543-0.44 0.984-0.985 0.984s-0.986-0.441-0.986-0.984z"></path>
                 </svg>-->
            </h6>
            <?php echo get_filter_dropdown('sort', apply_filters('wpml_translate_single_string', 'Select', 'GeneratePress Child', 'Select'), $sort_value); ?>
        </div>

        <div class="resource-filter-item">
            <h6 class="filter-heading"><?php echo apply_filters('wpml_translate_single_string', 'Search', 'GeneratePress Child', 'Search'); ?>
               <!-- <svg class="info-icon" stroke="currentColor" fill="currentColor" stroke-width="0" version="1.1" 
                     viewBox="0 0 17 17" height="20px" width="20px" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.5 0c-4.687 0-8.5 3.813-8.5 8.5s3.813 8.5 8.5 8.5 8.5-3.813 8.5-8.5-3.813-8.5-8.5-8.5zM8.5 16c-4.136 0-7.5-3.364-7.5-7.5s3.364-7.5 7.5-7.5 7.5 3.364 7.5 7.5-3.364 7.5-7.5 7.5zM9 12.369h0.979v1h-2.958v-1h0.979v-4.42h-0.946v-1h1.946v5.42zM7.185 4.986c0-0.545 0.441-0.986 0.986-0.986s0.985 0.441 0.985 0.986c0 0.543-0.44 0.984-0.985 0.984s-0.986-0.441-0.986-0.984z"></path>
                </svg> -->
            </h6>
            <div class="vg-tab-btn filter-group">
                <input type="text" class="filter-select" id="search" autocomplete="off" name="search" placeholder="Title or Category Search">
            </div>
        </div>
    </div>

    <div class="resource-grid">
        <?php
        $initial_filters = [
            'type'   => isset($_GET['type'])   ? sanitize_text_field($_GET['type'])   : '',
            'topic'  => isset($_GET['topic'])  ? sanitize_text_field($_GET['topic'])  : '',
            'search' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '',
            'sort'   => isset($_GET['sort'])   ? sanitize_text_field($_GET['sort'])   : '',
        ];
        echo get_filtered_portfolios($initial_filters, 1);
        ?>
    </div>

    <div class="load-more-btn-wrapper resource-btn-wrapper">
        <a id="load-more" class="x-btn vg-btn vg-btn-blue" data-page="1" data-action="loadmore">
            <span class="gb-button-text"><?php echo apply_filters('wpml_translate_single_string', 'Load more', 'GeneratePress Child', 'load_more'); ?></span>
        </a>
    </div>

    <script>
    var loadmore_params = {
        'ajax_url': '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
        'nonce': '<?php echo esc_js($nonce); ?>'
    };
    </script>
    <?php
    include(get_stylesheet_directory() . '/Shortcode/resource-shortcode/resource_filter_script.php');
    include(get_stylesheet_directory() . '/Shortcode/resource-shortcode/resource_filter_styles.php');

    return ob_get_clean();
}

/**
 * Get combined topics from both taxonomies
 */
function get_combined_topics() {
    $topics = [];
    
    $post_categories = get_terms([
        'taxonomy' => 'category',
        'hide_empty' => true,
    ]);

    $song_topics = get_terms([
        'taxonomy' => 'topics',
        'hide_empty' => true,
    ]);
    
    foreach ($post_categories as $cat) {
        if ($cat->slug !== 'uncategorized' && $cat->term_id !== 1) {
            $topics[$cat->slug] = (object) [
                'term_id' => $cat->term_id,
                'slug' => $cat->slug,
                'name' => $cat->name
            ];
        }
    }
    
    foreach ($song_topics as $topic) {
        if (!isset($topics[$topic->slug])) {
            $topics[$topic->slug] = (object) [
                'term_id' => $topic->term_id,
                'slug' => $topic->slug,
                'name' => $topic->name
            ];
        }
    }
    
    return array_values($topics);
}

/**
 * Generate filter dropdown HTML
 */
function get_filter_dropdown($id, $placeholder, $options, $value_key = null, $label_key = null) {
    ob_start();
    ?>
    <div class="vg-tab-btn filter-group">
        <select class="filter-select" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>">
            <option value=""><?php echo esc_html($placeholder); ?></option>
            <?php foreach ($options as $key => $value): 
                $option_value = $value_key ? $value->$value_key : $key;
                $option_label = $label_key ? $value->$label_key : $value;
            ?>
                <option value="<?php echo esc_attr($option_value); ?>">
                    <?php echo esc_html($option_label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <svg class="dropdown-icon" width="20" height="20" viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 3l4 4 4-4" stroke="#132641" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Register AJAX action for portfolio filtering
 */
function handle_portfolio_filter_ajax() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'portfolio_filter_nonce')) {
        wp_send_json_error('Invalid security token');
        wp_die();
    }

    $filters = [
        'type'  => isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '',
        'topic' => isset($_POST['topic']) ? sanitize_text_field($_POST['topic']) : '',
        'search' => isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '',
        'sort' => isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : '',
    ];
    
    $page = isset($_POST['page']) ? max(1, (int) $_POST['page']) : 1;
    
    $response = get_filtered_portfolios($filters, $page);
    
    wp_send_json($response);
    wp_die();
}

add_action('wp_ajax_portfolio_filter', 'handle_portfolio_filter_ajax');
add_action('wp_ajax_nopriv_portfolio_filter', 'handle_portfolio_filter_ajax');
add_shortcode('resource_filter', 'portfolio_filter_shortcode');