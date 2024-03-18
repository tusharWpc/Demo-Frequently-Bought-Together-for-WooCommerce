<?php

add_action('wp_ajax_search_products', 'search_products');
add_action('wp_ajax_nopriv_search_products', 'search_products');

function search_products() {
    check_ajax_referer('search_nonce', 'security');

    $search_term = $_POST['search_term'];

    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        's' => $search_term,
    );

    $products_query = new WP_Query($args);

    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            // Output your product results here
            echo '<p>' . get_the_title() . '</p>';
        }
    } else {
        echo '<p>No products found.</p>';
    }

    wp_reset_postdata();

    wp_die();
}
