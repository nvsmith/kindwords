<?php
/**
 * Plugin Name: KindWords - Testimonials Plugin
 * Description: A simple plugin to register a "kindwords" custom post type and display testimonials via shortcode.
 * Version: 1.0
 * Author: Nathan Smith
 * License: GPL2+
 * Text Domain: kindwords
 */

// Enqueue frontend scripts

// Hook into WP to load JavaScript for plugin on frontend
function kindwords_enqueue_scripts() {
    // Load JS file from the plugin's js folder
    wp_enqueue_script(
        // script identifier
        'kindwords-load-more',
        plugin_dir_url(__FILE__) . 'js/kindwords-load-more.js',
        // dependencies
        array('jquery'),
        // version
        '1.0',
        // load script in footer for better performance
        true
    );

    // Make PHP variables available to JS file as global a global object
    wp_localize_script(
        'kindwords-load-more',
        'KindWordsData',
        array(
            'ajaxUrl'        => admin_url('admin-ajax.php'),
            'postsPerPage'   => 5,
            // maxPages updated later in JS based on total post count
            'maxPages'       => null,
            // security token to verify AJAX requests
            'nonce'          => wp_create_nonce('kindwords_load_more_nonce'),
        )
    );
}

// Hook into WordPress
add_action('wp_enqueue_scripts', 'kindwords_enqueue_scripts');

// AJAX handler for loading more testimonials
function kindwords_load_more_ajax() {
    // Verify request against CSRF attacks
    check_ajax_referer('kindwords_load_more_nonce', 'nonce');

    // Parse AJAX Data from POST request:
    // default to page 1 and sanitize intval
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    // default to 5 posts per page
    $per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 5;

    // Create a new WP Query for custom post type
    $query = new WP_Query(array(
        'post_type'      => 'kindwords',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'post_status'    => 'publish',
    ));

    // Fallback if no posts found and let JS know nothing more to load
    if (!$query->have_posts()) {
        wp_send_json_success(array('html' => ''));
    }

    // Capture HTML as a string
    ob_start();

    // Start The Loop through each testimonial
    while ($query->have_posts()) {
        $query->the_post();
        
        // Get the raw content of the current post from the WP block editor 
        $block_markup = get_the_content(); 
        // Remove default WP comments from the raw content
        $parsed_content  = apply_filters('the_content', $block_markup); 

        // Output the HTML template
        echo '<div class="kindwords__item">';
            echo '<blockquote class="kindwords__quote">' . $parsed_content . '</blockquote>';
            echo '<p class="kindwords__author">&mdash; ' . esc_html(get_the_title()) . '</p>';
        echo '</div>';
    }

    wp_reset_postdata();

    // Store the output HTML
    $html = ob_get_clean();

    // Return HTML back to browser as JSON object so JS can insert it onto page
    wp_send_json_success(array('html' => $html));
}

// Register AJAX Actions for both logged-in and non-logged-in users
add_action('wp_ajax_nopriv_kindwords_load_more', 'kindwords_load_more_ajax');
add_action('wp_ajax_kindwords_load_more', 'kindwords_load_more_ajax');

// Define the shortcode for displaying testimonials
function kindwords_shortcode( $atts ) {

	// Set up default values for shortcode attributes in WP
	$atts = shortcode_atts(array(
        'posts_per_page' => 5,
        'paged'          => 1,
    ), $atts, 'kindwords');

	// Query the 'kindwords' CPT
	$query = new WP_Query(array(
        'post_type'      => 'kindwords',
        'posts_per_page' => intval($atts['posts_per_page']),
        'paged'          => intval($atts['paged']),
        'post_status'    => 'publish',
    ));

    // Handle no results
	if ( ! $query->have_posts() ) {
		return '<p>No testimonials found.</p>';
	}

    // Set maxPages for JS
    $max_pages = $query->max_num_pages;

    // Start output buffering to capture HTML
    ob_start();

    echo '<div id="kindwords-container" class="kindwords">'; // Block start

    while ( $query->have_posts() ) {
        $query->the_post();

        // Remove default WP comments from outputting on the front-end
        $block_markup = get_the_content(); // Get the raw content of the current post from the WP block editor
        $parsed_content = apply_filters( 'the_content', $block_markup ); // Remove default WP comments

        echo '<div class="kindwords__item">'; // Element start
            // Display the content of the current post as the testimonial
            echo '<blockquote class="kindwords__quote">' . $parsed_content . '</blockquote>';

            // Display the title of the current post as the author
            echo '<p class="kindwords__author">&mdash; ' . esc_html( get_the_title() ) . '</p>';
        echo '</div>'; // Element end
    }

    echo '</div>'; // Block end

    wp_reset_postdata();

    // Output Load More button if more pages exist
    if ($max_pages > intval($atts['paged'])) {
        echo '<div class="kindwords__load-more-wrap">';
            echo '<button id="kindwords-load-more-btn" data-page="' . intval($atts['paged']) . '" data-max-pages="' . $max_pages . '">Load More</button>';
        echo '</div>';
    }

    // Update localized maxPages, ensures JS knows when it has reached the last page
    add_action('wp_footer', function() use ($max_pages) {
        ?>
        <script>
            if (window.KindWordsData) {
                window.KindWordsData.maxPages = <?php echo $max_pages; ?>;
            }
        </script>
        <?php
    });

    // Replace shortcode in the content with the captured HTML from output buffering
    return ob_get_clean();
}

// Register shortcode with WordPress:
// run kindwords_shortcode() & replace shortcode with whatever HTML the function returns
add_shortcode( 'kindwords', 'kindwords_shortcode' );