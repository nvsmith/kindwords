<?php
/**
 * Plugin Name: KindWords - Testimonials Plugin
 * Description: A simple plugin to register a "kindwords" custom post type and display testimonials via shortcode.
 * Version: 1.0
 * Author: Nathan Smith
 * License: GPL2+
 * Text Domain: kindwords
 */

 // Define the shortcode for displaying testimonials
function kindwords_shortcode( $atts ) {

	// Set up default values for shortcode attributes in WP
	$atts = shortcode_atts( array(
		'posts_per_page' => 5,
	), $atts, 'kindwords' );
	
	// Query the 'kindwords' CPT
	$query = new WP_Query( array(
		'post_type' => 'kindwords',
		'posts_per_page' => intval( $atts['posts_per_page'] ),
		'post_status' => 'publish',
	) );
	
	if ( ! $query->have_posts() ) {
		return '<p>No testimonials found.</p>';
	}

    // Start output buffering to capture HTML
    ob_start();

    echo '<div class="kindwords">'; // Block start

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

    // Return the captured HTML from output buffering
    return ob_get_clean();
}

// Register shortcode with WordPress:
// run kindwords_shortcode() & replace shortcode with whatever HTML the function returns
add_shortcode( 'kindwords', 'kindwords_shortcode' );