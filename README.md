<a id="readme-top"></a>

# KindWords - WP Plugin

<a href="https://outpostwebstudio.com/" target="_blank" rel="author">Nate @ Outpost Web Studio</a> | Last Updated: 28 MAY 2025

<!-- ABOUT THE PROJECT -->

## About The Project

KindWords is a custom WordPress plugin built with PHP and JavaScript that implements a testimonial system using WordPress best practices. It registers a Custom Post Type (CPT) to manage testimonials separately from regular posts and pages.

The plugin includes a front-end shortcode—`[kindwords]`—which displays a list of published testimonials. It also incorporates AJAX functionality via jQuery to provide a seamless "Load More" button that dynamically fetches additional testimonials without requiring a page reload.

<!-- <div align="center">

![screenshot1](screenshots/screenshot1.png "before")
![screenshot2](screenshots/screenshot2.png "after")

</div> -->

### Key Technologies & Methods Used

-   **PHP:** Core plugin logic, shortcode rendering, and AJAX handling.
-   **WordPress APIs:**
    -   `register_post_type()` for the custom testimonial type.
    -   `add_shortcode()` for rendering testimonials via shortcode.
    -   `wp_localize_script()` to pass PHP data (like AJAX URLs and security nonces) to JavaScript.
-   **JavaScript (jQuery):**
    -   Handles front-end AJAX requests for loading additional testimonials.
    -   Dynamically appends new content and handles pagination.
-   **Output Buffering:** Uses `ob_start()` and `ob_get_clean()` to build HTML output cleanly for shortcode rendering.
-   **Security:** Implements `check_ajax_referer()` to validate AJAX requests and protect against CSRF.
-   **Content Filtering:** Uses `apply_filters('the_content', ...)` to ensure block content is rendered with full formatting (including shortcodes, embeds, etc.).

<!-- GETTING STARTED -->

## Getting Started

To use this plugin, upload the zipped `kindwords` file via the WordPress Dashboard > Plugins. Then activate.

You'll need to register a Custom Post Type named `kindwords`. Once registered, any testimonials added to this post type will be queryable by the plugin.

Use the shortcode `[kindwords]` on any page that you create or edit, and the plugin will automatically render your `kindwords` testimonials!

<!-- CONTACT -->

## Contact

Nate: [Website](https://outpostwebstudio.com/lets-talk-shop/) | [GitHub](https://github.com/nvsmith)

<!-- ACKNOWLEDGMENTS -->

## Acknowledgments

-   [Best README Template](https://github.com/othneildrew/Best-README-Template/tree/master)

<p align="right">(<a href="#readme-top">back to top</a>)</p>
