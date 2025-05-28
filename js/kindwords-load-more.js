/**
 * Script to load more testimonials using AJAX.
 *
 * @package kindwords
 * @license GPL2+
 */

// Wait for the DOM to be ready
jQuery(document).ready(function ($) {
    // Listen for click events on the "Load More" button
    $("#kindwords-load-more-btn").on("click", function () {
        // Convert the button to a jQuery object
        const button = $(this); // this = the actual <button> element that fired the event

        // Convert button's data properties to a base-10 number
        const page = parseInt(button.attr("data-page"), 10) + 1; // Increment page
        const max = parseInt(button.attr("data-max-pages"), 10); // Get max pages

        // Show loading feedback
        const originalText = button.text();
        button.prop("disabled", true).text("Loading...");

        // Indicate loading state for accessibility
        button.attr("aria-busy", "true");

        // Make AJAX request to tell WP to run the function
        $.post(
            KindWordsData.ajaxUrl,
            {
                // tell WP which AJAX handler to run
                action: "kindwords_load_more",
                // how many testimonials to load from localized script data
                posts_per_page: KindWordsData.postsPerPage,
                // page number to fetch next
                page: page,
                // security token to validate request
                nonce: KindWordsData.nonce,
            },
            function (response) {
                // response = wp_send_json_success(array('html' => $html)) from PHP;
                if (response.success && response.data.html) {
                    // append the new HTML
                    $("#kindwords-container").append(response.data.html);

                    // update button's data-page value
                    button.attr("data-page", page);

                    // Remove the button if no more content
                    if (page >= max) {
                        button.remove(); // No more pages
                    } else {
                        // Re-enable button and restore text
                        button.prop("disabled", false).text(originalText).removeAttr("aria-busy");
                    }
                } else {
                    button.remove(); // Remove on failure or no more content
                }
            }
        );
    });
});
