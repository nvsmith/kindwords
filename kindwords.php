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
}

// Register shortcode with WordPress:
// run kindwords_shortcode() & replace shortcode with whatever HTML the function returns
add_shortcode( 'kindwords', 'kindwords_shortcode' );