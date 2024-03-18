<?php
/**
 * Plugin Name: Demo Frequently Bought Together for WooCommerce
 * Plugin URI: 
 * Description: Add "Frequently Bought Together" section to product pages.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: 
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fbt
 */

// Include the AJAX handler file
include_once (plugin_dir_path(__FILE__) . 'inc/ajax-handler.php');

// Enqueue scripts and styles
add_action('admin_enqueue_scripts', 'enqueue_bought_together_scripts');
function enqueue_bought_together_scripts()
{
    wp_enqueue_script('fbt-ajax', plugin_dir_url(__FILE__) . 'inc/ajax-search.js', array('jquery'), '1.0', true);
    wp_localize_script('fbt-ajax', 'ajax_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'search_nonce' => wp_create_nonce('search_nonce')
    )
    );
}

// AJAX handler for product search
add_action('wp_ajax_search_products', 'fbt_search_products');
add_action('wp_ajax_nopriv_search_products', 'fbt_search_products');
function fbt_search_products()
{
    check_ajax_referer('search_nonce', 'security');

    $search_term = isset ($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';

    if (empty ($search_term)) {
        wp_send_json_error(__('Search term is empty.', 'fbt'));
    }

    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        's' => $search_term,
    );

    $products_query = new WP_Query($args);

    ob_start();
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            // Output your product results here
            echo '<p>' . get_the_title() . '</p>';
        }
    } else {
        echo '<p>' . __('No products found.', 'fbt') . '</p>';
    }
    $response = ob_get_clean();

    wp_reset_postdata();

    wp_send_json_success(array('data' => $response));
}

// Add "Frequently Bought Together" tab
add_filter('woocommerce_product_data_tabs', 'add_bought_together_tab', 10, 1);
function add_bought_together_tab($tabs)
{
    $tabs['bought_together'] = array(
        'label' => __('Frequently Bought Together', 'fbt'),
        'target' => 'bought_together_data_option',
        'class' => array('hide_if_grouped', 'hide_if_external', 'hide_if_bundle'),
    );
    return $tabs;
}

// Output the content of the 'Frequently Bought Together' panel
add_action('woocommerce_product_data_panels', 'add_bought_together_panel');
function add_bought_together_panel()
{
    ?>
    <div id="bought_together_data_option" class="panel woocommerce_options_panel">
        <div class="options_group">
            <p class="form-field">
                <label for="bought_together_search">
                    <?php _e('Search Products:', 'fbt'); ?>
                </label>
                <input type="text" id="bought_together_search" class="short" name="bought_together_search">
                <button id="bought_together_search_button" class="button">
                    <?php _e('Search', 'fbt'); ?>
                </button>
            <div id="bought_together_search_results"></div>
            </p>
        </div>
    </div>
    <?php
}
